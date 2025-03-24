<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function addToCart(Request $request)
    {
        $request->validate([
            'user_id' => 'nullable|exists:users,id',
            'cart_id' => 'nullable|exists:cart,id',
            'product_id' => 'required|exists:products,id',
            'variant_id' => 'nullable|exists:variants,id',
            'color_id' => 'nullable|exists:colors,id',
            'size_id' => 'nullable|exists:sizes,id',
            'quantity' => 'required|integer|min:1',
            'price' => 'required|numeric|min:0',
        ]);

        if ($request->cart_id) {
            $cartItem = Cart::findOrFail($request->cart_id);
            $cartItem->update($request->only(['quantity', 'variant_id', 'color_id', 'size_id', 'price']));
        } else {
            $cartItem = Cart::create($request->only(['user_id', 'product_id', 'variant_id', 'color_id', 'size_id', 'quantity', 'price']));
        }

        return response()->json(['message' => 'Cart updated', 'cart' => $cartItem]);
    }
    
    public function removeItem($id)
    {
        $cart = session()->get('cart', []);

        if (isset($cart[$id])) {
            unset($cart[$id]);
            session()->put('cart', $cart);
            return response()->json(['message' => 'Item removed', 'cart' => $cart]);
        }

        return response()->json(['message' => 'Item not found'], 404);
    }
}
