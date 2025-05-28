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
    ];

    public static function boot()
    {
        parent::boot();

        static::creating(function ($webPage) {
            if (empty($webPage->slug)) {
                $webPage->slug = Str::slug($webPage->title);
            }
        });

         static::updating(function ($webPage) {
        if ($webPage->isDirty('title')) {
            $webPage->slug = Str::slug($webPage->title);
        }
    });
    }
}
