<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Post extends Model
{
    /** @use HasFactory<\Database\Factories\PostFactory> */
    use HasFactory, Auditable;

    protected $guarded = [];

    // protected $casts = [
    //     'tags' => 'array',
    // ];

    public function tags()
    {
        return $this->belongsToMany(Tag::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    #[Scope]
    public function published($query)
    {
        return $query->where('status', 'published')
            ->orWhereNotNull('published_at');
    }

    public function audits()
    {
        return $this->morphMany(AuditLog::class, 'auditable');
    }

    public function sections(): BelongsToMany
    {
        return $this->belongsToMany(PostSection::class, 'post_section_post', 'post_id', 'post_section_id');
    }
}
