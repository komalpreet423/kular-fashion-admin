<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Size extends Model
{
    use SoftDeletes;
    protected $guarded = [];

    public function sizeScale()
    {
        return $this->belongsTo(SizeScale::class);
    }
    public function sizedata(){
        return $this->belongsTo(PurchaseOrderVariantSize::class,'id','size_id');
    }
}
