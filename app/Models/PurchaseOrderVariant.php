<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseOrderVariant extends Model
{
    protected $table = 'purchase_order_variants';
    protected $guarded =[];
    
    public function sizes()
    {
        return $this->hasMany(PurchaseOrderVariantSize::class,'purchase_product_variant_id')->with('sizeDetail');
    }

    public function colors()
    {
        return $this->hasMany(Color::class,'id','color_id');
    }

}
