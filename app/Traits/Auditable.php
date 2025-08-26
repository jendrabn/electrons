<?php

namespace App\Traits;

use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

trait Auditable
{
    public static function bootAuditable()
    {
        // Log saat data dibuat
        static::created(function ($model) {
            $model->logAudit('created', null, $model->getAttributes());
        });

        // Log saat data diupdate
        static::updated(function ($model) {
            $original = $model->getOriginal();
            $changes = $model->getChanges();

            if (!empty($changes) && !$model->isOnlyTimestampChange($changes)) {
                $model->logAudit('updated', $original, $changes);
            }
        });

        // Log saat data dihapus
        static::deleted(function ($model) {
            $model->logAudit('deleted', $model->getOriginal(), null);
        });
    }

    protected function logAudit($action, $oldData = null, $newData = null)
    {
        try {
            // Process dan convert data ke format yang benar
            $processedOldData = $this->processAuditData($oldData);
            $processedNewData = $this->processAuditData($newData);

            AuditLog::create([
                'auditable_type' => get_class($this),
                'auditable_id' => $this->getKey(),
                'user_type' => Auth::check() ? get_class(Auth::user()) : null,
                'user_id' => Auth::id(),
                'action' => $action,
                'description' => $this->getAuditDescription($action),
                'old_values' => $processedOldData,
                'new_values' => $processedNewData,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);
        } catch (\Exception $e) {
            // Log error tapi jangan sampai mengganggu operasi utama
            Log::error('Failed to create audit log: ' . $e->getMessage(), [
                'model' => get_class($this),
                'model_id' => $this->getKey(),
                'action' => $action,
                'error' => $e->getTraceAsString()
            ]);
        }
    }

    protected function processAuditData($data)
    {
        if (is_null($data)) {
            return null;
        }

        // Filter sensitive data
        $filteredData = $this->filterSensitiveData($data);

        // Convert ke array jika belum
        if (!is_array($filteredData)) {
            $filteredData = (array) $filteredData;
        }

        // Convert array ke JSON string untuk disimpan di database
        // Laravel akan otomatis cast kembali ke array saat diambil karena ada cast di model
        return $filteredData;
    }

    protected function getAuditDescription($action)
    {
        $modelName = class_basename($this);

        switch ($action) {
            case 'created':
                return "{$modelName} telah dibuat";
            case 'updated':
                return "{$modelName} telah diperbarui";
            case 'deleted':
                return "{$modelName} telah dihapus";
            default:
                return "{$modelName} {$action}";
        }
    }

    protected function filterSensitiveData($data)
    {
        if (!is_array($data)) {
            $data = (array) $data;
        }

        // Daftar kolom yang tidak ingin dilog (password, token, etc)
        $sensitiveFields = $this->getHiddenFields();

        return collect($data)->except($sensitiveFields)->toArray();
    }

    protected function getHiddenFields()
    {
        return array_merge(
            ['password', 'remember_token', 'api_token', 'password_confirmation'],
            $this->hidden ?? []
        );
    }

    protected function isOnlyTimestampChange($changes)
    {
        $timestampFields = ['updated_at', 'created_at'];
        $nonTimestampChanges = collect($changes)->except($timestampFields);

        return $nonTimestampChanges->isEmpty();
    }

    // Relationship ke audit logs
    public function auditLogs()
    {
        return $this->morphMany(AuditLog::class, 'auditable', 'auditable_type', 'auditable_id');
    }

    // Helper method untuk mendapatkan log audit terbaru
    public function getLatestAudit()
    {
        return $this->auditLogs()->latest()->first();
    }

    // Helper method untuk mendapatkan log berdasarkan action
    public function getAuditByAction($action)
    {
        return $this->auditLogs()->where('action', $action)->get();
    }

    // Method untuk disable/enable logging sementara
    public function withoutAuditing($callback)
    {
        static::$recordEvents = false;

        try {
            return $callback($this);
        } finally {
            static::$recordEvents = true;
        }
    }

    // Method untuk log custom audit
    public function logCustomAudit($action, $description, $oldValues = null, $newValues = null)
    {
        try {
            $processedOldValues = $this->processAuditData($oldValues);
            $processedNewValues = $this->processAuditData($newValues);

            AuditLog::create([
                'auditable_type' => get_class($this),
                'auditable_id' => $this->getKey(),
                'user_type' => Auth::check() ? get_class(Auth::user()) : null,
                'user_id' => Auth::id(),
                'action' => $action,
                'description' => $description,
                'old_values' => $processedOldValues,
                'new_values' => $processedNewValues,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to create custom audit log: ' . $e->getMessage());
        }
    }

    // Method untuk check apakah model pernah diaudit
    public function hasBeenAudited()
    {
        return $this->auditLogs()->exists();
    }

    // Method untuk mendapatkan user yang terakhir mengubah
    public function getLastModifiedBy()
    {
        $latestAudit = $this->getLatestAudit();
        return $latestAudit ? $latestAudit->user : null;
    }
}
