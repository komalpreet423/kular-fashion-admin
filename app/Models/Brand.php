<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Cviebrock\EloquentSluggable\Sluggable;

class Brand extends Model
{
    use SoftDeletes, Sluggable;
    protected $guarded=[];

    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'name'
            ]
        ];
    }

    public function products()
    {
        return $this->hasMany(Product::class, 'color_id');
    }
    public function product()
    {
        return $this->hasMany(Product::class, 'brand_id');
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class, 'brand_id');
    }
}
