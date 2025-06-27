<?php

namespace App\Models;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;

class WebPages extends Model
{
     protected $fillable = [
        'title',
        'slug',
        'page_content',
        'heading',
        'meta_title',
        'meta_keywords',
        'meta_description',
        'published_at',
        'description',
        'summary',
        'image_small',
        'image_medium',
        'image_large',
        'hide_categories',
        'hide_all_filters',
        'show_all_filters',
        'rules',
        'filters',
    ];
    protected $casts = [
    'filters' => 'array',
    'rules' => 'array',
];

    public static function boot()
    {
        parent::boot();

        static::creating(function ($webPage) {
            if (empty($webPage->slug)) {
                $webPage->slug = Str::slug($webPage->title);
            }
               if (empty($webPage->published_at)) {
            $webPage->published_at = now();
        }
        });

         static::updating(function ($webPage) {
        if ($webPage->isDirty('title')) {
            $webPage->slug = Str::slug($webPage->title);
        }
         if (empty($webPage->published_at)) {
            $webPage->published_at = now();
        }
        
    });
    }
}
