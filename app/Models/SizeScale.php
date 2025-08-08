<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SizeScale extends Model
{
    use SoftDeletes;
    protected $guarded =[];
    
    public function sizes()
    {
        return $this->hasMany(Size::class);
    }
    public function products()
    {
        return $this->hasMany(Product::class, 'size_scale_id');
    }
}
