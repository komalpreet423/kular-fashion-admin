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
    public function uploadImages(Request $request)
    {
        $request->validate([
            'images.*' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $filename = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
                $image->move(public_path('images'), $filename);

                HomeImage::create([
                    'image_path' => 'images/' . $filename,
                ]);
            }
            return back()->with('success', 'Images uploaded successfully.');
        }
        return back()->withErrors(['message' => 'No images selected.']);
    }
    public function destroy($id)
    {
        $image = HomeImage::findOrFail($id);

        $path = public_path($image->image_path);
        if (file_exists($path)) {
            unlink($path);
        }
        $image->delete();

        if (request()->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Deleted']);
        }
        return back()->with('success', 'Image removed successfully.');
    }
}
