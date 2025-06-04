<?php

namespace App\Http\Controllers;
use App\Models\ContactUs;
use Illuminate\Http\Request;

class ContactUsController extends Controller
{
     public function index()
    {
        $messages = ContactUs::latest()->get(); 
        return view('contact-us.index', compact('messages'));
    }
    public function destroy($id)
{
    $message = ContactUs::find($id);

    if (!$message) {
        return response()->json(['success' => false, 'message' => 'Message not found.']);
    }

    $message->delete();

    return response()->json(['success' => true, 'message' => 'Message deleted successfully.']);
}

}
