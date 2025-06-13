<?php

namespace App\Http\Controllers;

use App\Models\GiftCard;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;

class WebsiteGiftVoucherController extends Controller
{
    public function index(Request $request)
    {
        $query = GiftCard::with('user')->latest();

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->payment_method);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        $giftVouchers = $query->get();
        $users = User::whereHas('roles', function ($query) {
            $query->where('name', 'customer');
        })->get();

        $statusOptions = ['active', 'redeemed', 'expired'];

        return view('gift-voucher.index', compact('giftVouchers', 'users', 'statusOptions'));
    }

    public function create()
    {
        return view('gift-voucher.create');
    }
    public function show($id)
    {
        $voucher = GiftCard::with('user')->findOrFail($id);
        return view('gift-voucher.show', compact('voucher'));
    }
    public function edit($id)
    {
        $voucher = GiftCard::findOrFail($id);
        return view('gift-voucher.edit', compact('voucher'));
    }
    public function update(Request $request, $id)
    {
        $voucher = GiftCard::findOrFail($id);
        $request->validate([
            'recipient_email' => 'required|email',
            'sender_name' => 'required|max:20',
            'delivery_date' => 'nullable|date_format:d-m-Y',
            'amount' => 'required|numeric|min:1',
            'card_number' => 'required|unique:gift_cards,card_number,' . $voucher->id,
            'transaction_id' => 'required',
            'payment_method' => 'required',
            'status' => 'required|in:active,redeemed,expired',
        ]);
        $data = $request->all();
        if ($request->filled('delivery_date')) {
            $data['delivery_date'] = Carbon::createFromFormat('d-m-Y', $request->delivery_date)->format('Y-m-d');
        }
        $voucher->update($data);
        return redirect()->route('gift-voucher.index')->with('success', 'Gift Voucher updated.');
    }
    public function destroy($id)
    {
        $voucher = GiftCard::find($id);

        if (!$voucher) {
            return response()->json(['success' => false, 'message' => 'Voucher not found.']);
        }

        $voucher->delete();
        return response()->json(['success' => true, 'message' => 'Voucher deleted successfully.']);
    }
}
