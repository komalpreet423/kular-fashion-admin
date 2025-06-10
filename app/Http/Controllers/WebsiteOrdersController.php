<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\CustomerOrders;
use App\Models\User;
class WebsiteOrdersController extends Controller
{
 public function index(Request $request)
{
    $query = CustomerOrders::with('user')
        ->select(
            'id',
            'unique_order_id',
            'user_id',
            'payment_type',
            'payment_status',
            'total',
            'placed_at',
            'status'
        );

    if ($request->filled('status')) {
        $query->where('status', $request->status);
    }

    if ($request->filled('user_id')) {
        $query->where('user_id', $request->user_id);
    }

    if ($request->filled('payment_status')) {
        $query->where('payment_status', $request->payment_status);
    }

    if ($request->filled('payment_type')) {
        $query->where('payment_type', $request->payment_type);
    }

    $orders = $query->latest()->get();

    $users = User::whereIn('id', CustomerOrders::select('user_id')->distinct())->get();
    $paymentTypes = CustomerOrders::select('payment_type')->distinct()->pluck('payment_type');
    return view('orders.index', compact('orders', 'users', 'paymentTypes'));
}



}

