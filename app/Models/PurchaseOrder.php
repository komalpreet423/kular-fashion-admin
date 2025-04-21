<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseOrder extends Model
{
    protected $guarded = [];

    public function purchaseOrderProduct()
    {
        return $this->hasMany(PurchaseOrderProduct::class, 'purchase_order_id')->with('variants');
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }
    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }
}
