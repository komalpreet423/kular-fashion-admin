<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CustomerOrderItems extends Model
{
    use SoftDeletes;

    protected $table = 'customer_order_items';

    protected $fillable = [
        'customer_order_id',
        'user_id',
        'product_id',
        'variant_id',
    
        'actual_rate',
        'offered_rate',
        'quantity',
        'price',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function order()
    {
        return $this->belongsTo(CustomerOrders::class, 'customer_order_id', 'id');
    }

    public function variant()
    {
        return $this->belongsTo(ProductQuantity::class, 'variant_id', 'id');
    }
    
    public function product()
    {
        return $this->belongsTo(Product::class,'product_id', 'id');
    }
}
