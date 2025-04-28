<?php

namespace App\Models;
use App\Models\Branch;
use App\Models\Product;
use App\Models\ProductSize;
use App\Models\ProductColor;

use App\Models\InventoryTransfer;
use Illuminate\Database\Eloquent\Model;

class InventoryItem extends Model
{
    protected $guarded =[];

    public function brand()
    {
        return $this->belongsTo(Brand::class,'brand_id', 'id');
    }
    public function product()
    {
        return $this->belongsTo(Product::class,'product_id', 'id');
    }
    public function productSize()
    {
        return $this->belongsTo(ProductSize::class,'product_size_id', 'id');
    }

    public function productColor()
    {
        return $this->belongsTo(ProductColor::class,'product_color_id', 'id');
    }

    public function inventoryTransfer()
    {
        return $this->belongsTo(InventoryTransfer::class,'inventroy_transfer_id', 'id');
    }
}
