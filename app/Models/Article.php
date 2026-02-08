<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class Article extends Model
{
    use HasFactory, HasSlug;

    protected $fillable = [
        'category_id',
        'source_id',
        'title',
        'slug',
        'summary',
        'content',
        'image_url',
        'original_url',
        'author',
        'published_at',
        'views_count',
        'is_featured',
        'is_published',
        'ai_confidence_score',
        'ai_metadata',
    ];

    protected $casts = [
        'published_at' => 'datetime',
        'is_featured' => 'boolean',
        'is_published' => 'boolean',
        'ai_confidence_score' => 'float',
        'ai_metadata' => 'json',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the options for generating the slug.
     */
    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('title')
            ->saveSlugsTo('slug');
    }

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName()
    {
        return 'slug';
    }

    /**
     * Category relationship
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Source relationship
     */
    public function source()
    {
        return $this->belongsTo(Source::class);
    }

    /**
     * Article views relationship
     */
    public function views()
    {
        return $this->hasMany(ArticleView::class);
    }

    /**
     * Scopes
     */
    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopePopular($query, $limit = 5)
    {
        return $query->orderBy('views_count', 'desc')->limit($limit);
    }

    /**
     * Increment views count
     */
    public function incrementViews(string $ip, string $userAgent): void
    {
        $today = now()->startOfDay();

        $view = $this->views()
            ->where('ip_address', $ip)
            ->whereDate('created_at', $today)
            ->first();

        if (!$view) {
            $this->views()->create([
                'ip_address' => $ip,
                'user_agent' => $userAgent,
            ]);
            $this->increment('views_count');
        }
    }
}
