<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Branch extends Model
{
    use SoftDeletes;
    protected $guarded = [];

    public function sentTransfers()
    {
        return $this->hasMany(InventoryTransfer::class, 'sent_from');
    }

    public function receivedTransfers()
    {
        return $this->hasMany(InventoryTransfer::class, 'sent_to');
    }

    public function inventory()
    {
        return $this->hasMany(StoreInventory::class, 'store_id');
    }
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }
}
