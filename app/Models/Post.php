<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

class Post extends Model
{
    /** @use HasFactory<\Database\Factories\PostFactory> */
    use HasFactory, Auditable;

    protected $guarded = [];

    // protected $casts = [
    //     'tags' => 'array',
    // ];

    protected $appends = ['excerpt', 'image_url'];

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

    #[Scope]
    public function popular($query)
    {
        return $query->published()
            ->where('created_at', '>=', now()->subWeek())
            ->orWhere('created_at', '>=', now()->subWeeks(2))
            ->orWhere('created_at', '>=', now()->subWeeks(3))
            ->orderBy('views_count', 'desc')
            ->limit(5);
    }

    #[Scope]
    public function recent($query)
    {
        return $query->published()
            ->orderBy('id', 'desc')
            ->limit(5);
    }

    public function audits()
    {
        return $this->morphMany(AuditLog::class, 'auditable');
    }

    public function sections(): BelongsToMany
    {
        return $this->belongsToMany(PostSection::class, 'post_section_post', 'post_id', 'post_section_id');
    }

    public function excerpt(): Attribute
    {
        return Attribute::make(
            get: fn($value) => $value ?: Str::limit(strip_tags($this->content), 100, '...'),
        );
    }

    public function imageUrl(): Attribute
    {
        return Attribute::make(
            get: fn($value) => filter_var($this->image, FILTER_VALIDATE_URL) ? $this->image : asset('storage/' . $this->image),
        );
    }
}
