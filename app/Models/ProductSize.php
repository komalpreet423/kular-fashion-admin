<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Size;

class ProductSize extends Model
{
    protected $guarded = [];

    public function sizeDetail()
    {
        return $this->belongsTo(Size::class, 'size_id', 'id');
    }

    public function quantity($product_color_id = null)
    {
        $query = $this->hasMany(ProductQuantity::class, 'product_size_id', 'id');

        if ($product_color_id) {
            $query->where('product_color_id', $product_color_id);
        }

        return $query->first()->quantity ?? 0;
    }

    public function totalQuantity($product_color_id = null)
    {
        $query = $this->hasMany(ProductQuantity::class, 'product_size_id', 'id');

        if ($product_color_id) {
            $query->where('product_color_id', $product_color_id);
        }

        return $query->first()->total_quantity ?? 0;
    }

    public function inventoryAvailableQuantity($product_color_id = null, $storeId = null)
    {
        $inventory = StoreInventory::where('store_id', $storeId)->where('product_size_id', $this->id)->where('product_color_id', $product_color_id);
        return $inventory->first()->quantity ?? 0;
    }

    public function inventoryTotalQuantity($product_color_id = null, $storeId = null)
    {
        $inventory = StoreInventory::where('store_id', $storeId)->where('product_size_id', $this->id)->where('product_color_id', $product_color_id);
        return $inventory->first()->total_quantity ?? 0;
    }
    public function quantities()
    {
        return $this->hasMany(ProductQuantity::class, 'product_size_id');
    }
}
