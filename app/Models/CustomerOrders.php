<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CustomerOrders extends Model
{
    use SoftDeletes;

    protected $table = 'customer_orders';

    protected $fillable = [
        'unique_order_id',
            
        'user_id',
        'customer_address_id',
        'coupon_id',
    
        // Payment Details
        'payment_type',
        'payment_gateway',
        'payment_status',
        'payment_attempts',
        'transaction_id',
        'payment_reference_id',
        'payment_signature',  
        'payment_failure_reason',
        'paid_at',
        'gateway_response_snapshot',
    
        // Order Totals
        'subtotal',
        'discount',
        'tax',
        'shipping_charge',
        'total',
    
        // Order Lifecycle
        'status',
        'placed_at',
        'shipped_at',
        'delivered_at',
        'cancelled_at',
        'returned_at',
    
        'notes',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function orderItems()
    {
        return $this->hasMany(CustomerOrderItems::class,'customer_order_id');
    }

    public function customerAddress()
    {
        return $this->belongsTo(CustomerAddresses::class,'customer_address_id');
    }

}
