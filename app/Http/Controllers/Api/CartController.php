<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Cart;

class CartController extends Controller
{
    public function addToCart(Request $request)
    {

    
        if (!empty($request->cart_id)) {
            $cartItem = Cart::find($request->cart_id);
            if (!$cartItem) {
                return response()->json(['message' => 'Cart not found'], 404);
            }
            $cartItem->update($request->only(['quantity', 'variant_id', 'color_id', 'size_id', 'price']));
        } else {
            $cartItem = Cart::create(['user_id' => $request->user_id ?? 0, 
                                    'product_id' => $request->product_id, 
                                    'variant_id'=> $request->variant_id, 
                                    'color_id' => $request->color_id, 
                                    'size_id' => $request->size_id, 
                                    'quantity' => $request->quantity, 
                                    'price'  => $request->price 
                                ]);
        }
    
        return response()->json(['message' => 'Cart updated', 'cart' => $cartItem]);
    }
    
    
    public function removeItem($id)
    {
        $cartItem = Cart::find($id);
        
        if (!$cartItem) {
            return response()->json(['message' => 'Item not found'], 404);
        }

        $cartItem->delete();
        return response()->json(['message' => 'Item removed successfully']);
    }
}
