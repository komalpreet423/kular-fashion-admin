<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    protected $guarded = [];

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }
}
