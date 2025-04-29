<?php

namespace App\Http\Controllers\Api;

use Illuminate\Support\Facades\Validator;
use App\Http\Resources\ProductTypeCollection;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Illuminate\Http\Request;
use App\Models\Coupon;
use Exception;

class CouponController extends Controller
{
    public function applyCoupon(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'coupon' => 'required||exists:coupons,code',
        ]);
    
        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()->first()], 400);
        }

        $currentDateTime = Carbon::now(); 

        $coupon = Coupon::where('code', $request->coupon)
                        ->where(function ($query) use ($currentDateTime) {
                            $query->whereNotNull('start_date')
                                  ->whereDate('start_date', '<=', $currentDateTime);
                        })
                        ->where(function ($query) use ($currentDateTime) {
                            $query->whereNotNull('expire_date')
                                  ->whereDate('expire_date', '>=', $currentDateTime);
                        })
                        ->where('status', 1) 
                        ->first();
        if (!$coupon) { 
            return response()->json(['success' => false,  'message' => 'Coupon is expired' ], 400);
        } 
   
        return response()->json(['success' => false,  'data' => $coupon ], 200);
    }
}
