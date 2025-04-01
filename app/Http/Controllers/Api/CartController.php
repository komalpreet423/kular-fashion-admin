<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Cart;
use App\Models\CartItem;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    public function addToCart(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'variant_id' => 'required|exists:product_quantities,id',
            'quantity' => 'required|integer|min:1',
            'user_id' => 'required|exists:users,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $cart = Cart::firstOrCreate(
            ['user_id' => $request->user_id],
            ['coupon_id' => $request->coupon_id ?? null, 'note' => $request->note]
        );

        $cartItem = CartItem::where('cart_id', $cart->id)
            ->where('variant_id', $request->variant_id)
            ->first();

        if ($cartItem) {
            $cartItem->update(['quantity' => $cartItem->quantity + $request->quantity]);
        } else {
            $cartItem = CartItem::create([
                'cart_id' => $cart->id,
                'user_id' => $request->user_id,
                'variant_id' => $request->variant_id,
                'quantity' => $request->quantity,
            ]);
        }

        return response()->json(['message' => 'Cart updated', 'cart' => $cart, 'cart_item' => $cartItem], 201);
    }

    public function removeItem($id)
    {
        $cartItem = CartItem::find($id);

        if (!$cartItem) {
            return response()->json(['message' => 'Item not found'], 404);
        }

        $cartItem->delete();
        return response()->json(['message' => 'Item removed successfully']);
    }

    public function viewCart(Request $request)
    {
        $cart = Cart::where('user_id', Auth::id())
            ->with('cartItems.variant', 'cartItems.user')
            ->first();

        if (!$cart || $cart->cartItems->isEmpty()) {
            return response()->json(['message' => 'Cart is empty'], 404);
        }

        return response()->json(['cart' => $cart]);
    }

    public function clearCart(Request $request)
    {
        $cart = Cart::where('user_id', $request->user()->id)->first();

        if (!$cart) {
            return response()->json(['message' => 'Cart not found'], 404);
        }

        CartItem::where('cart_id', $cart->id)->delete();

        return response()->json(['message' => 'Cart cleared successfully']);
    }
}
