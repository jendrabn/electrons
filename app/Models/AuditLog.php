<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AuditLog extends Model
{
    use HasFactory;

    protected $table = 'audit_logs';

    protected $fillable = [
        'auditable_type',
        'auditable_id',
        'user_type',
        'user_id',
        'action',
        'description',
        'old_values',
        'new_values',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'old_values' => 'array',    // Laravel akan otomatis convert JSON ke array
        'new_values' => 'array',    // Laravel akan otomatis convert JSON ke array
    ];

    // Relationship ke auditable (model yang di-audit)
    public function auditable()
    {
        return $this->morphTo('auditable', 'auditable_type', 'auditable_id');
    }

    // Relationship ke user yang melakukan aksi
    // public function user()
    // {
    //     return $this->morphTo('user', 'user_type', 'user_id');
    // }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Scope untuk filter berdasarkan model
    public function scopeForAuditable($query, $auditable)
    {
        return $query->where('auditable_type', get_class($auditable))
            ->where('auditable_id', $auditable->getKey());
    }

    // Scope untuk filter berdasarkan user
    public function scopeByUser($query, $user)
    {
        return $query->where('user_type', get_class($user))
            ->where('user_id', $user->getKey());
    }

    // Scope untuk filter berdasarkan action
    public function scopeByAction($query, $action)
    {
        return $query->where('action', $action);
    }

    // Helper method untuk mendapatkan perubahan yang terjadi
    public function getChanges()
    {
        $old = $this->old_values;
        $new = $this->new_values;

        if (!$old || !$new) {
            return [];
        }

        $changes = [];
        foreach ($new as $key => $value) {
            if (!array_key_exists($key, $old) || $old[$key] != $value) {
                $changes[$key] = [
                    'old' => $old[$key] ?? null,
                    'new' => $value
                ];
            }
        }

        return $changes;
    }

    // Helper method untuk debug
    public function toDebugArray()
    {
        return [
            'id' => $this->id,
            'auditable' => $this->auditable_type . '#' . $this->auditable_id,
            'action' => $this->action,
            'user' => $this->user?->name ?? 'System',
            'old_values' => $this->old_values,
            'new_values' => $this->new_values,
            'created_at' => $this->created_at,
        ];
    }


    public function formatValue($value): string
    {
        if (is_null($value)) {
            return 'null';
        }

        if (is_bool($value)) {
            return $value ? 'true' : 'false';
        }

        if (is_array($value) || is_object($value)) {
            return json_encode($value, JSON_PRETTY_PRINT);
        }

        return (string) $value;
    }
}
