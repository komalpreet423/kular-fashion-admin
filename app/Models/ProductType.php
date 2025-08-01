<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Department;
use App\Models\Product;
use Cviebrock\EloquentSluggable\Sluggable;

class ProductType extends Model
{
    use SoftDeletes, Sluggable;
    protected $guarded = [];

    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'name'
            ]
        ];
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function productTypeDepartments()
    {
        return $this->hasMany(ProductTypeDepartment::class, 'product_type_id')->with('departments');
    }

    public function product()
    {
        return $this->belongsTo(Product::class,'id','product_type_id');
    }
}
