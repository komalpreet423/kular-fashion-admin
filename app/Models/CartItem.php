<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CartItem extends Model
{
    protected $guarded = [];

    public function cart()
    {
        return $this->belongsTo(Cart::class, 'cart_id');
    }

    public function variant()
    {
        return $this->belongsTo(ProductQuantity::class, 'variant_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
