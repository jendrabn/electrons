<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class ThreadCategory extends Model
{
    /** @use HasFactory<\Database\Factories\ThreadCategoryFactory> */
    use HasFactory;

    protected $guarded = [];

    public function threads(): BelongsToMany
    {
        return $this->belongsToMany(Thread::class);
    }
}
