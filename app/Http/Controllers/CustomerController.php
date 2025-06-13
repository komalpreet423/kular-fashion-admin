<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
   public function index(Request $request)
{
    $query = User::whereHas('roles', function ($q) {
        $q->where('name', 'customer');
    });

    if ($request->filled('user_id')) {
        $query->where('id', $request->user_id);
    }

    if ($request->filled('status')) {
        $query->where('status', $request->status);
    }

    $customers = $query->get();

    $users = User::whereHas('roles', function ($q) {
        $q->where('name', 'customer');
    })->get();

    return view('customers.index', compact('customers', 'users'));
}


    public function edit($id)
    {
        $customer = User::findOrFail($id);
        return view('customers.edit', compact('customer'));
    }

   public function update(Request $request, $id)
{
    $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:users,email,' . $id,
        'phone_number' => 'nullable|string|max:20',
        'status' => 'required|in:active,inactive,suspended,pending', 
    ]);

    $customer = User::findOrFail($id);
    $customer->update($request->only('name', 'email', 'phone_number', 'status'));

    return redirect()->route('customers.index')->with('success', 'Customer updated successfully.');
}
    public function destroy($id)
    {
        $customers = User::find($id);

        if (!$customers) {
            return response()->json(['success' => false, 'message' => 'customers not found.']);
        }

        $customers->delete();

        return response()->json(['success' => true, 'message' => 'customers deleted successfully.']);
    }
}
