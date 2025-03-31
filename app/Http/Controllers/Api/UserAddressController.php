<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\UserAddress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class UserAddressController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $addresses = UserAddress::all();
        return response()->json($addresses);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'first_name' => 'required|string|max:25',
            'last_name' => 'nullable|string|max:25',
            'address_line1' => 'required|string|max:100',
            'address_line2' => 'nullable|string|max:100',
            'city' => 'required|string|max:75',
            'state' => 'required|string|max:75',
            'zip_code' => 'required|string|max:12',
            'phone_number' => 'required|string|max:15',
            'country_id' => 'nullable|exists:countries,id',
            'state_id' => 'nullable|exists:states,id',
            'is_default' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $address = UserAddress::create($request->all());

        return response()->json([
            'message' => 'Address saved successfully',
            'address' => $address
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show()
    {
        $user = Auth::user();
        $address = UserAddress::where('user_id', $user->id)
            ->where('is_default', true)
            ->first();

        if (!$address) {
            return response()->json(['message' => 'No default address found'], 404);
        }

        return response()->json($address);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        $user = Auth::user();
        $address = UserAddress::where('user_id', $user->id)->where('is_default', true)->first();

        if (!$address) {
            return response()->json(['message' => 'No default address found'], 404);
        }

        $validator = Validator::make($request->all(), [
            'first_name' => 'sometimes|required|string|max:25',
            'last_name' => 'sometimes|nullable|string|max:25',
            'address_line1' => 'sometimes|required|string|max:100',
            'address_line2' => 'sometimes|nullable|string|max:100',
            'city' => 'sometimes|required|string|max:75',
            'state' => 'sometimes|required|string|max:75',
            'zip_code' => 'sometimes|required|string|max:12',
            'phone_number' => 'sometimes|required|string|max:15',
            'country_id' => 'sometimes|nullable|exists:countries,id',
            'state_id' => 'sometimes|nullable|exists:states,id',
            'is_default' => 'sometimes|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $address->update($request->all());

        return response()->json([
            'message' => 'Address updated successfully',
            'address' => $address
        ]);
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy()
    {
        $user = Auth::user();
        $address = UserAddress::where('user_id', $user->id)->where('is_default', true)->first();

        if (!$address) {
            return response()->json(['message' => 'No default address found'], 404);
        }

        $address->delete();

        return response()->json(['message' => 'Address deleted successfully']);
    }
}
