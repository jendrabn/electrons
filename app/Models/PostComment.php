<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class PostComment extends Model
{
    use HasFactory;

    protected $table = 'post_comments';

    protected $guarded = [];

    protected $appends = ['created_at_human', 'liked'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(PostComment::class, 'parent_id');
    }

    public function replies(): HasMany
    {
        return $this->hasMany(PostComment::class, 'parent_id');
    }

    public function likes(): MorphMany
    {
        return $this->morphMany(Like::class, 'likeable');
    }

    public function createdAtHuman(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->created_at->diffForHumans(),
        );
    }

    public function liked(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->likes->where('user_id', auth()->id())->count() > 0,
        );
    }

    public function body(): Attribute
    {
        return Attribute::make(
            get: fn (?string $value): ?string => $value === null ? null : mask_profanity($value),
        );
    }
}
