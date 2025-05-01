<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CouponUsagePerCustomer extends Model
{
    protected $guarded=[];

    protected $table = 'coupon_usage_per_customer';

    

    public function coupon()
    {
        return $this->belongsTo(Coupon::class, 'coupon_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
