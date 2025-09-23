<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class ThreadLike extends Model
{
    /** @use HasFactory<\Database\Factories\ThreadLikeFactory> */
    use HasFactory;

    protected $guarded = [];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function thread(): MorphTo
    {
        return $this->morphTo(Thread::class, 'likeable');
    }

    public function comment(): MorphTo
    {
        return $this->morphTo(Comment::class, 'likeable');
    }
}
