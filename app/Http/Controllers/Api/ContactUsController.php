<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ContactUs;

class ContactUsController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'message' => 'required|string',
        ]);
        $contact = ContactUs::create($validated);
        return response()->json([
            'message' => 'Contact form submitted successfully.',
            'data' => $contact,
        ], 201);
    }
}
