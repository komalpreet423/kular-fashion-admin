<?php

namespace App\Http\Controllers;

use App\Models\Coupon;
use App\Models\Product;
use App\Models\Tag;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class CouponController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $coupons = Coupon::orderBy('id', 'desc')->get();
        return view('coupons.index', compact('coupons'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $products = Product::select('id', 'article_code', 'name')->where('status', 'Active')->get();
        $tags = Tag::select('id', 'name')->where('status', 'Active')->get();
        return view('coupons.create', compact('products', 'tags'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $imagePath = session()->get('uploaded_image', null);

        if ($request->file('image')) {
            if ($imagePath) {
                $uploadedImagePath = public_path($imagePath);

                if (file_exists($uploadedImagePath)) {
                    unlink($uploadedImagePath);
                }
            }

            $imagePath = uploadFile($request->file('image'), 'uploads/coupons/');
            session()->put('uploaded_image', $imagePath);
        }

        $rules = [
            'code' => 'required|string|max:50|unique:coupons,code',
            'type' => 'required|in:percentage,fixed,free_shipping,buy_x_get_y,buy_x_for_y',
            'min_spend' => 'nullable|numeric|min:0',
            'max_spend' => 'nullable|numeric|min:0|gte:min_spend',
            'start_date' => 'nullable|date|after_or_equal:today',
            'expire_date' => 'nullable|date|after:start_date',
            'status' => 'required|in:0,1,2',
            'description' => 'nullable|string',
        ];

        if ($request->type == 'percentage' || $request->type == 'fixed') {
            $rules['value'] = 'required|numeric|min:1';
        }

        if ($request->type == 'free_shipping') {
            $rules['shipping_methods'] = 'required|array|min:1';
        }

        if ($request->type == 'buy_x_get_y') {
            $rules['buy_x_quantity'] = 'required|numeric|min:1';
            $rules['get_y_quantity'] = 'required|numeric|min:1';
        }

        if ($request->type == 'buy_x_for_y') {
            $rules['buy_x_products'] = 'required|array|min:1';
            $rules['buy_x_products.*'] = 'exists:products,id';
            $rules['buy_x_discount'] = 'required|numeric|min:1';
            $rules['buy_x_discount_type'] = 'required|in:percentage,fixed';
        }

        if ($request->usage_limit_total) {
            $rules['usage_limit_total_value'] = 'required|numeric|min:1';
        }

        if ($request->usage_limit_per_customer) {
            $rules['usage_limit_per_customer_value'] = 'required|numeric|min:1';
        }


        $validator = Validator::make($request->all(), $rules);

        // If validation fails, redirect back with errors
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        Coupon::create(
            [
                "code" => $request->code,
                "type" => $request->type,
                'value' => $request->value,
                "shipping_methods" => json_encode($request->shipping_methods),
                "buy_x_product_ids" => json_encode($request->buy_x_products),
                "get_y_product_ids" => json_encode($request->buy_y_products),
                "buy_x_quantity" => $request->buy_x_quantity,
                "get_y_quantity" => $request->get_y_quantity,
                "buy_x_discount" => $request->buy_x_discount,
                "buy_x_discount_type" => $request->buy_x_discount_type,
                "min_spend" => $request->min_spend,
                "max_spend" => $request->max_spend,
                "usage_limit_total" => $request->usage_limit_total ? 1 : 0,
                "usage_limit_total_value" => $request->usage_limit_total_value,
                "usage_limit_per_customer" => $request->usage_limit_per_customer ? 1 : 0,
                "usage_limit_per_customer_value" => $request->usage_limit_per_customer_value,
                "limit_by_price" => $request->limit_by_price,
                "allowed_tags" => json_encode($request->allowed_tags),
                "disallowed_tags" => json_encode($request->disallowed_tags),
                "start_date" => isset($request->start_date) ? Carbon::createFromFormat('d-m-Y', $request->start_date)->format('Y-m-d H:i:s') : null,
                "expire_date" => isset($request->expire_date) ? Carbon::createFromFormat('d-m-Y', $request->expire_date)->format('Y-m-d H:i:s') : null,
                "status" => $request->status,
                "image" => $imagePath,
                "description" => $request->description
            ]
        );

        session()->forget('uploaded_image');

        return redirect()->route('coupons.index')->with('success', 'Coupon created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Coupon $coupon)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Coupon $coupon)
    {
        $products = Product::select('id', 'article_code', 'name')->where('status', 'Active')->get();
        $tags = Tag::select('id', 'name')->where('status', 'Active')->get();
        return view('coupons.edit', compact('coupon', 'products', 'tags'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Coupon $coupon)
    {
        $rules = [
            'code' => [
                'required',
                'max:50',
                Rule::unique('coupons')->ignore($coupon->id)->whereNull('deleted_at'),
            ],
            'type' => 'required|in:percentage,fixed,free_shipping,buy_x_get_y,buy_x_for_y',
            'min_spend' => 'nullable|numeric|min:0',
            'max_spend' => 'nullable|numeric|min:0|gte:min_spend',
            'start_date' => 'nullable|date|after_or_equal:today',
            'expire_date' => 'nullable|date|after:start_date',
            'status' => 'required|in:0,1,2',
            'description' => 'nullable|string',
        ];

        if ($request->type == 'percentage' || $request->type == 'fixed') {
            $rules['value'] = 'required|numeric|min:1';
        }

        if ($request->type == 'free_shipping') {
            $rules['shipping_methods'] = 'required|array|min:1';
        }

        if ($request->type == 'buy_x_get_y') {
            $rules['buy_x_quantity'] = 'required|numeric|min:1';
            $rules['get_y_quantity'] = 'required|numeric|min:1';
        }

        if ($request->type == 'buy_x_for_y') {
            $rules['buy_x_products'] = 'required|array|min:1';
            $rules['buy_x_products.*'] = 'exists:products,id';
            $rules['buy_x_discount'] = 'required|numeric|min:1';
            $rules['buy_x_discount_type'] = 'required|in:percentage,fixed';
        }

        if ($request->usage_limit_total) {
            $rules['usage_limit_total_value'] = 'required|numeric|min:1';
        }

        if ($request->usage_limit_per_customer) {
            $rules['usage_limit_per_customer_value'] = 'required|numeric|min:1';
        }

        $validator = Validator::make($request->all(), $rules);

        // If validation fails, redirect back with errors
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $imagePath = $coupon->image;
        if ($request->file('image')) {
            if ($imagePath) {
                $uploadedImagePath = public_path($imagePath);

                if (file_exists($uploadedImagePath)) {
                    unlink($uploadedImagePath);
                }
            }

            $imagePath = uploadFile($request->file('image'), 'uploads/coupons/');
        }
        $coupon->update(
            [
                "code" => $request->code,
                "type" => $request->type,
                'value' => $request->value,
                "shipping_methods" => json_encode($request->shipping_methods),
                "buy_x_product_ids" => json_encode($request->buy_x_products),
                "get_y_product_ids" => json_encode($request->buy_y_products),
                "buy_x_quantity" => $request->buy_x_quantity,
                "get_y_quantity" => $request->get_y_quantity,
                "buy_x_discount" => $request->buy_x_discount,
                "buy_x_discount_type" => $request->buy_x_discount_type,
                "min_spend" => $request->min_spend,
                "max_spend" => $request->max_spend,
                "usage_limit_total" => $request->usage_limit_total ? 1 : 0,
                "usage_limit_total_value" => $request->usage_limit_total_value,
                "usage_limit_per_customer" => $request->usage_limit_per_customer ? 1 : 0,
                "usage_limit_per_customer_value" => $request->usage_limit_per_customer_value,
                "limit_by_price" => $request->limit_by_price,
                "allowed_tags" => json_encode($request->allowed_tags),
                "disallowed_tags" => json_encode($request->disallowed_tags),
                "start_date" => isset($request->start_date) ? Carbon::createFromFormat('d-m-Y', $request->start_date)->format('Y-m-d H:i:s') : null,
                "expire_date" => isset($request->expire_date) ? Carbon::createFromFormat('d-m-Y', $request->expire_date)->format('Y-m-d H:i:s') : null,
                "status" => $request->status,
                "image" => $imagePath,
                "description" => $request->description
            ]
        );

        return redirect()->route('coupons.index')->with('success', 'Coupon updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Coupon $coupon)
    {
        if ($coupon->image) {
            $uploadedImagePath = public_path($coupon->image);

            if (file_exists($uploadedImagePath)) {
                unlink($uploadedImagePath);
            }
        }

        $coupon->delete();

        return response()->json([
            'success' => true,
            'message' => 'Coupon deleted successfully.',
        ]);
    }
}
