<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class ThreadComment extends Model
{
    /** @use HasFactory<\Database\Factories\ThreadCommentFactory> */
    use HasFactory;

    protected $guarded = [];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function thread(): BelongsTo
    {
        return $this->belongsTo(Thread::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(ThreadComment::class, 'parent_id', 'id');
    }

    public function replies(): HasMany
    {
        return $this->hasMany(ThreadComment::class, 'parent_id', 'id');
    }

    public function likes(): MorphMany
    {
        return $this->morphMany(Like::class, 'likeable');
    }

    public function body(): Attribute
    {
        return Attribute::make(
            get: fn (?string $value): ?string => $value === null ? null : mask_profanity($value),
        );
    }
}
