<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseOrderVariantSize extends Model
{
    protected $table = 'purchase_order_variant_sizes';
    protected $guarded =[];

    public function sizeDetail(){
        return $this->hasMany(Size::class,'id','size_id');
    }
}
