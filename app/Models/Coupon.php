<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Coupon extends Model
{
    use SoftDeletes;
    protected $guarded=[];

    public function couponUsagePerCustomer()
    {
        return $this->hasMany(CouponUsagePerCustomer::class, 'coupon_id');
    }
}
