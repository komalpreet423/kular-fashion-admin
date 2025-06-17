<?php

namespace App\Http\Controllers;

use App\Models\HomeImage;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $images = HomeImage::latest()->get();
        return view('home.index', compact('images'));
    }

    public function show($id)
{
    
}

  public function uploadImages(Request $request)
{
    if ($request->hasFile('slider_images')) {
        $request->validate([
            'slider_images.*' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        foreach ($request->file('slider_images') as $image) {
            $filename = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('images'), $filename);

            HomeImage::create([
                'image_path' => 'images/' . $filename,
                'type' => 'slider',
            ]);
        }

        return back()->with('success', 'Slider images uploaded successfully.');
    }

    if ($request->hasFile('newsletter_image')) {
        $request->validate([
            'newsletter_image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $image = $request->file('newsletter_image');
        $filename = 'newsletter_' . time() . '.' . $image->getClientOriginalExtension();
        $image->move(public_path('images'), $filename);

        HomeImage::create([
            'image_path' => 'images/' . $filename,
            'type' => 'newsletter',
        ]);

        return back()->with('success', 'Newsletter image uploaded successfully.');
    }

    return back()->withErrors(['message' => 'No image selected.']);
}


   public function destroy($id)
{
    $image = HomeImage::findOrFail($id);
    $filePath = null;

    if (!empty($image->slider_images)) {
        $filePath = public_path($image->slider_images);
    } elseif (!empty($image->newsletter_image)) {
        $filePath = public_path($image->newsletter_image);
    }
    if ($filePath && file_exists($filePath)) {
        unlink($filePath);
    }
    $image->delete();
    if (request()->expectsJson()) {
        return response()->json([
            'success' => true,
            'message' => 'Image deleted successfully.'
        ]);
    }
    return back()->with('success', 'Image deleted successfully.');
}

}
