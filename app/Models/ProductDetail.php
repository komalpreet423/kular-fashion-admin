<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductDetail extends Model
{
    protected $guarded =[];

    public function variants()
    {
        return $this->hasMany(PurchaseOrderProduct::class);
    }
}
