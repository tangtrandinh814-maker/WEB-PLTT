<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class Source extends Model
{
    use HasFactory, HasSlug;

    protected $fillable = [
        'name',
        'slug',
        'url',
        'rss_url',
        'logo',
        'is_active',
        'crawl_frequency',
        'last_crawled_at'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'last_crawled_at' => 'datetime',
    ];

    /**
     * Get the options for generating the slug.
     */
    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('name')
            ->saveSlugsTo('slug');
    }

    /**
     * Articles relationship
     */
    public function articles()
    {
        return $this->hasMany(Article::class);
    }

    /**
     * Scope for active sources
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Check if source needs to be crawled
     */
    public function needsCrawling()
    {
        if (!$this->last_crawled_at) {
            return true;
        }

        return $this->last_crawled_at->addMinutes($this->crawl_frequency)->isPast();
    }

    /**
     * Update last crawled timestamp
     */
    public function markAsCrawled()
    {
        $this->update(['last_crawled_at' => now()]);
    }
}
