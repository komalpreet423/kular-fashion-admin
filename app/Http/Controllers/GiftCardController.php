<?php

namespace App\Http\Controllers;

use App\Models\GiftCard;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class GiftCardController extends Controller
{
    /**
     * Create a new gift card.
     */
    public function createGiftCard(Request $request)
    {
        $request->validate([
            'recipient_email' => 'required|email|max:75',
            'sender_name' => 'required|string|max:20',
            'message' => 'nullable|string',
            'delivery_date' => 'nullable|date',
            'amount' => 'required|numeric|min:1',
            'transaction_id' => 'required|string|max:150',
            'payment_method' => 'required|string|max:25',
            'user_id' => 'nullable|exists:users,id', 
        ]);

        $cardNumber = strtoupper(Str::random(12));

        $giftCard = GiftCard::create([
            'user_id' => $request->user_id,
            'recipient_email' => $request->recipient_email,
            'sender_name' => $request->sender_name,
            'message' => $request->message,
            'delivery_date' => $request->delivery_date,
            'amount' => $request->amount,
            'card_number' => $cardNumber,
            'transaction_id' => $request->transaction_id,
            'payment_method' => $request->payment_method,
            'status' => 'active',
        ]);

        return response()->json([
            'message' => 'Gift Card created successfully!',
            'card_number' => $cardNumber
        ], 201);
    }

    /**
     * Get all gift cards.
     */
    public function getGiftCards()
    {
        return response()->json(GiftCard::all());
    }

    /**
     * Get a single gift card by card number.
     */
    public function getGiftCard(Request $request)
    {
        $request->validate([
            'card_number' => 'required|string|exists:gift_cards,card_number'
        ]);

        $giftCard = GiftCard::where('card_number', $request->card_number)->first();

        if (!$giftCard) {
            return response()->json(['message' => 'Gift card not found'], 404);
        }

        return response()->json($giftCard);
    }

    /**
     * Redeem a gift card.
     */
    public function redeemGiftCard(Request $request)
    {
        $request->validate([
            'card_number' => 'required|string|exists:gift_cards,card_number'
        ]);

        $giftCard = GiftCard::where('card_number', $request->card_number)
            ->where('status', 'active')
            ->first();

        if (!$giftCard) {
            return response()->json(['message' => 'Gift card already redeemed or expired.'], 400);
        }

        $giftCard->update(['status' => 'redeemed']);

        return response()->json(['message' => 'Gift card redeemed successfully!']);
    }

    /**
     * Delete a gift card.
     */
    public function deleteGiftCard(Request $request)
    {
        $request->validate([
            'card_number' => 'required|string|exists:gift_cards,card_number'
        ]);

        $giftCard = GiftCard::where('card_number', $request->card_number)->first();

        if (!$giftCard) {
            return response()->json(['message' => 'Gift card not found.'], 404);
        }

        $giftCard->delete();

        return response()->json(['message' => 'Gift card deleted successfully.']);
    }
}
