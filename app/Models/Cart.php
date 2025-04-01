<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    protected $guarded = [];

    public function cartItems()
    {
        return $this->hasMany(CartItem::class, 'cart_id');
    }
}
