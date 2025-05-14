<?php

namespace App\Http\Controllers\Api;

use Illuminate\Support\Facades\Validator;
use App\Http\Resources\ProductTypeCollection;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Illuminate\Http\Request;
use App\Models\Coupon;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\ProductQuantity;
use App\Models\CouponUsagePerCustomer;
use Exception;

class CouponController extends Controller
{
    public function applyCoupon(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'coupon' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()->first()], 400);
        }

        if (!empty($request->user_id)) {
            $cart = Cart::where('user_id', $request->user_id)
                ->with(['cartItems.variant.product.tags'])
                ->first();

            $total_of_cart_items = 0;

            if ($cart) {
                foreach ($cart->cartItems as $item) {
                    if (
                        $item->variant &&
                        $item->variant->product &&
                        $item->variant->product->price
                    ) {
                        $total_of_cart_items += $item->variant->product->price * ($item->quantity ?? 1);
                    }
                }
            }

            $currentDateTime = Carbon::now();

            $coupon = Coupon::whereRaw('BINARY `code` = ?', [$request->coupon])
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

            if (!empty($coupon)) {

                if ($coupon->min_spend > $total_of_cart_items) {
                    return response()->json(['success' => false,  'message' => 'Coupon valid on shopping of more than ' . $coupon->min_spend . ' only!'], 201);
                }

                if ($coupon->max_spend < $total_of_cart_items) {
                    return response()->json(['success' => false,  'message' => 'Coupon valid on shopping of less than ' . $coupon->max_spend . ' only!'], 201);
                }

                if ($coupon->usage_limit_total == 1 && $coupon->usage_total_limit_used >= $coupon->usage_limit_total_value) {
                    return response()->json(['success' => false,  'message' => 'Coupon is expired'], 201);
                }

                if ($coupon->usage_limit_per_customer == 1 && $coupon->usage_limit_per_customer_value > 0) {
                    $couponUsagePerCustomer = CouponUsagePerCustomer::where('coupon_id', $coupon->id)
                        ->where('user_id', $request->id)
                        ->first();

                    if (!empty($couponUsagePerCustomer) && $couponUsagePerCustomer->usage_value >= $coupon->usage_limit_per_customer_value) {
                        return response()->json(['success' => false,  'message' => 'Your limit to use this coupon is over.'], 201);
                    }
                }

                // === Prepare for item-level filtering ===
                $allowedTags = json_decode($coupon->allowed_tags, true) ?? [];
                $disallowedTags = json_decode($coupon->disallowed_tags, true) ?? [];

                $filtered_total = 0; // sum of items eligible for coupon
                $eligibleItems = []; // for buy_x logic

                foreach ($cart->cartItems as $item) {
                    $variant = $item->variant;
                    $product = $variant?->product;

                    if (!$variant || !$product || !$product->price) {
                        continue;
                    }

                    $price = $product->price;
                    $mrp = $product->mrp ?? $price;
                    $quantity = $item->quantity ?? 1;
                    $productTagIds = $product->tags->pluck('tag_id')->toArray();
                    $productId = $product->id;

                    // === 1. limit_by_price filters ===
                    if ($coupon->limit_by_price === 'reduced_items' && $price >= $mrp) {
                        continue;
                    }

                    if ($coupon->limit_by_price === 'full_price_items' && $price < $mrp) {
                        continue;
                    }

                    // === 2. allowed_tags filter ===
                    if (!empty($allowedTags) && !array_intersect($productTagIds, $allowedTags)) {
                        continue;
                    }

                    // === 3. disallowed_tags filter ===
                    if (!empty($disallowedTags) && array_intersect($productTagIds, $disallowedTags)) {
                        continue;
                    }

                    // === 4. Passed all filters ===
                    $filtered_total += $price * $quantity;

                    // Keep item info for buy_x logic
                    $eligibleItems[] = [
                        'product_id' => $productId,
                        'price' => $price,
                        'quantity' => $quantity,
                    ];
                }

                // === Calculate final price based on coupon type ===
                $final_price = $filtered_total;
                $discounted_total = 0;
                if ($coupon->type === 'fixed') {
                    // Apply flat discount
                    $final_price = max(0, $filtered_total - $coupon->value);
                } elseif ($coupon->type === 'percentage') {
                    // Apply percentage discount
                    $discounted_total = ($coupon->value / 100) * $filtered_total;
                    $final_price = max(0, $filtered_total - $discounted_total);
                } elseif ($coupon->type === 'buy_x_get_y') {
                    $buyQty = (int) $coupon->buy_x_quantity;
                    $getQty = (int) $coupon->get_y_quantity;

                    // Flatten all eligible items into single list with quantity
                    $allItems = [];
                    foreach ($eligibleItems as $item) {
                        for ($i = 0; $i < $item['quantity']; $i++) {
                            $allItems[] = $item['price'];
                        }
                    }

                    // Sort prices low to high to apply free item to cheapest ones
                    sort($allItems);

                    $totalQty = count($allItems);
                    $chargeableQty = max(0, $totalQty - $getQty);

                    $final_price = array_sum(array_slice($allItems, 0, $chargeableQty));
                } elseif ($coupon->type === 'buy_x_for_y') {
                    $discountProductIds = json_decode($coupon->buy_x_product_ids, true) ?? [];
                    $discountType = $coupon->buy_x_discount_type; // 'discount' or 'percentage'
                    $discountValue = $coupon->buy_x_discount;

                    foreach ($eligibleItems as $item) {
                        $isDiscounted = in_array($item['product_id'], $discountProductIds);
                        $line_total = $item['price'] * $item['quantity'];

                        if ($isDiscounted) {
                            if ($discountType === 'discount') {
                                $line_total -= ($discountValue * $item['quantity']);
                            } elseif ($discountType === 'percentage') {
                                $line_total -= ($item['price'] * ($discountValue / 100) * $item['quantity']);
                            }
                        }

                        $discounted_total += $line_total;
                    }

                    $final_price = max(0, $discounted_total);
                }

                Cart::where('id', $cart->id)->update(['coupon_id' => $coupon->id]);

                return response()->json([
                    'success' => true,
                    'original_total' => number_format($total_of_cart_items, 2, '.', ''),
                    'eligible_total' => number_format($filtered_total, 2, '.', ''),
                    'final_price' => number_format(($total_of_cart_items - $discounted_total), 2, '.', ''),
                    'discount' => number_format($discounted_total, 2, '.', ''),
                    'message' => 'Coupon applied successfully.'
                ], 200);
            } else {
                return response()->json(['success' => false,  'message' => 'Coupon code does not exists or is expired'], 201);
            }
        } else {

            if (!empty($request->cart)) {
                $cartItems = collect($request->cart['cartItems']);
                $variant_ids = $cartItems->pluck('variant_id');

                // Fetch variants data with related product and tags
                $variants_data = ProductQuantity::whereIn('id', $variant_ids)
                    ->with(['product.tags'])
                    ->get();

                // Attach quantity to each variant from the original cart items
                $variants_data->map(function ($variant) use ($cartItems) {
                    // Find matching cart item by variant_id
                    $matchingItem = $cartItems->firstWhere('variant_id', $variant->id);

                    // Attach quantity if found
                    $variant->quantity = $matchingItem['quantity'] ?? 1;

                    return $variant;
                });

                $total_of_cart_items = 0;

                if ($variants_data) {
                    foreach ($variants_data as $item) {
                        if (
                            $item &&
                            $item->product &&
                            $item->product->price
                        ) {
                            $total_of_cart_items += $item->product->price * ($item->quantity ?? 1);
                        }
                    }
                } else {
                    return response()->json(['success' => false,  'message' => 'Cart Items not found!'], 201);
                }

                $currentDateTime = Carbon::now();

                $coupon = Coupon::whereRaw('BINARY `code` = ?', [$request->coupon])
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

                if (!empty($coupon)) {

                    if ($coupon->min_spend > $total_of_cart_items) {
                        return response()->json(['success' => false,  'message' => 'Coupon valid on shopping of more than ' . $coupon->min_spend . ' only!'], 201);
                    }

                    if ($coupon->max_spend < $total_of_cart_items) {
                        return response()->json(['success' => false,  'message' => 'Coupon valid on shopping of less than ' . $coupon->max_spend . ' only!'], 201);
                    }

                    if ($coupon->usage_limit_total == 1 && $coupon->usage_total_limit_used >= $coupon->usage_limit_total_value) {
                        return response()->json(['success' => false,  'message' => 'Coupon is expired'], 201);
                    }

                    if ($coupon->usage_limit_per_customer == 1) {
                        return response()->json(['success' => false,  'message' => 'This coupon can be applied only after login'], 201);
                    }

                    // === Prepare for item-level filtering ===
                    $allowedTags = json_decode($coupon->allowed_tags, true) ?? [];
                    $disallowedTags = json_decode($coupon->disallowed_tags, true) ?? [];

                    $filtered_total = 0; // sum of items eligible for coupon
                    $eligibleItems = []; // for buy_x logic

                    foreach ($variants_data as $item) {
                        $variant = $item;
                        $product = $variant?->product;

                        if (!$variant || !$product || !$product->price) {
                            continue;
                        }

                        $price = $product->price;
                        $mrp = $product->mrp ?? $price;
                        $quantity = $item->quantity ?? 1;
                        $productTagIds = $product->tags->pluck('tag_id')->toArray();
                        $productId = $product->id;

                        // === 1. limit_by_price filters ===
                        if ($coupon->limit_by_price === 'reduced_items' && $price >= $mrp) {
                            continue;
                        }

                        if ($coupon->limit_by_price === 'full_price_items' && $price < $mrp) {
                            continue;
                        }

                        // === 2. allowed_tags filter ===
                        if (!empty($allowedTags) && !array_intersect($productTagIds, $allowedTags)) {
                            continue;
                        }

                        // === 3. disallowed_tags filter ===
                        if (!empty($disallowedTags) && array_intersect($productTagIds, $disallowedTags)) {
                            continue;
                        }

                        // === 4. Passed all filters ===
                        $filtered_total += $price * $quantity;

                        // Keep item info for buy_x logic
                        $eligibleItems[] = [
                            'product_id' => $productId,
                            'price' => $price,
                            'quantity' => $quantity,
                        ];
                    }
                    $discounted_total = 0;
                    // === Calculate final price based on coupon type ===
                    $final_price = $filtered_total;

                    if ($coupon->type === 'fixed') {
                        // Apply flat discount
                        $final_price = max(0, $filtered_total - $coupon->value);
                    } elseif ($coupon->type === 'percentage') {
                        // Apply percentage discount
                        $discounted_total = ($coupon->value / 100) * $filtered_total;
                        $final_price = max(0, $filtered_total - $discounted_total);
                    } elseif ($coupon->type === 'buy_x_get_y') {
                        $buyQty = (int) $coupon->buy_x_quantity;
                        $getQty = (int) $coupon->get_y_quantity;

                        // Flatten all eligible items into single list with quantity
                        $allItems = [];
                        foreach ($eligibleItems as $item) {
                            for ($i = 0; $i < $item['quantity']; $i++) {
                                $allItems[] = $item['price'];
                            }
                        }

                        // Sort prices low to high to apply free item to cheapest ones
                        sort($allItems);

                        $totalQty = count($allItems);
                        $chargeableQty = max(0, $totalQty - $getQty);

                        $final_price = array_sum(array_slice($allItems, 0, $chargeableQty));
                    } elseif ($coupon->type === 'buy_x_for_y') {
                        $discountProductIds = json_decode($coupon->buy_x_product_ids, true) ?? [];
                        $discountType = $coupon->buy_x_discount_type; // 'discount' or 'percentage'
                        $discountValue = $coupon->buy_x_discount;
    
                        foreach ($eligibleItems as $item) {
                            $isDiscounted = in_array($item['product_id'], $discountProductIds);
                            $line_total = $item['price'] * $item['quantity'];

                            if ($isDiscounted) {
                                if ($discountType === 'discount') {
                                    $line_total -= ($discountValue * $item['quantity']);
                                } elseif ($discountType === 'percentage') {
                                    $line_total -= ($item['price'] * ($discountValue / 100) * $item['quantity']);
                                }
                            }

                            $discounted_total += $line_total;
                        }

                        $final_price = max(0, $discounted_total);
                    }

                    return response()->json([
                        'success' => true,
                        'original_total' => number_format($total_of_cart_items, 2, '.', ''),
                        'eligible_total' => number_format($filtered_total, 2, '.', ''),
                        'final_price' => number_format(($total_of_cart_items - $discounted_total), 2, '.', ''),
                        'discount' => number_format($discounted_total, 2, '.', ''),
                        'message' => 'Coupon applied successfully.'
                    ], 200);
                } else {
                    return response()->json(['success' => false,  'message' => 'Coupon not found or expired'], 201);
                }
            } else {
                return response()->json(['success' => false,  'message' => 'Cart not found'], 201);
            }
        }
    }
}
