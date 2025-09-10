<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Comment extends Model
{
    /** @use HasFactory<\Database\Factories\CommentFactory> */
    use HasFactory;

    protected $guarded = [];

    protected $appends = ['created_at_human', 'liked'];

    // protected $with = ['user', 'replies'];

    // protected $withCount = ['likes', 'replies'];

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
        return $this->belongsTo(Comment::class, 'parent_id');
    }

    public function replies(): HasMany
    {
        return $this->hasMany(Comment::class, 'parent_id');
    }

    public function likes(): HasMany
    {
        return $this->hasMany(CommentLike::class, 'comment_id');
    }

    public function createdAtHuman(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->created_at->diffForHumans(),
        );
    }

    public function liked(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->likes->where('user_id', auth()->id())->count() > 0,
        );
    }
}
