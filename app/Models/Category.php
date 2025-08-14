<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Category extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];
    public function categoriesProduct(){
        return $this->hasMany(ProductCategory::class, 'category_id');
    }

    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }
    
    public function childrenRecursive()
    {
        return $this->children()->with('childrenRecursive');
    }

    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    // Auto-generate slug
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($category) {
            if (empty($category->slug)) {
                $category->slug = Str::slug($category->name);
            }
        });
    }
}
