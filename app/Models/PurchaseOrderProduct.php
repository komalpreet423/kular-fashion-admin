<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseOrderProduct extends Model
{
    protected $table = 'purchase_order_products';
    protected $guarded =[];

    public function variants()
    {
        return $this->hasMany(PurchaseOrderVariant::class,'purchase_product_id')->with('sizes')->with('colors');
    }
    
}
