<?php

namespace App\Models;

use App\Enums\Status;
use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Str;

class Post extends Model
{
    /** @use HasFactory<\Database\Factories\PostFactory> */
    use HasFactory, Auditable;

    protected $guarded = [];

    protected $appends = ['image_url'];



    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function audits(): MorphMany
    {
        return $this->morphMany(AuditLog::class, 'auditable');
    }

    public function imageUrl(): Attribute
    {
        return Attribute::make(
            get: fn() => filter_var($this->image, FILTER_VALIDATE_URL) ? $this->image : asset('storage/' . $this->image),
        );
    }

    #[Scope]
    public function published($query): void
    {
        $query->where('status', Status::PUBLISHED->value);
    }

    #[Scope]
    public function popular($query): void
    {
        $query->published()
            ->where('created_at', '>', now()->subWeek())
            ->orderBy('views_count', 'desc')
            ->limit(5);
    }

    #[Scope]
    public function recent($query): void
    {
        $query->published()
            ->orderBy('id', 'desc')
            ->limit(5);
    }

    public function excerpt(): Attribute
    {
        return Attribute::make(
            get: fn() => Str::limit(strip_tags($this->content), 130, '...'),
        );
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }
}
