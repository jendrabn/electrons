<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Thread extends Model
{
    /** @use HasFactory<\Database\Factories\ThreadFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'slug',
        'body',
        'is_hidden'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function likes(): MorphMany
    {
        return $this->morphMany(ThreadLike::class, 'likeable');
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(ThreadCategory::class, 'thread_category');
    }

    public function bookmarks(): HasMany
    {
        return $this->hasMany(ThreadBookmark::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }
}
