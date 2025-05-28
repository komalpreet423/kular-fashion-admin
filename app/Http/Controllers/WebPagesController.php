<?php

namespace App\Http\Controllers;

use App\Models\WebPages;
use Illuminate\Http\Request;

class WebPagesController extends Controller
{
    public function index()
    {
        $webPages = WebPages::all();
        return view('web-pages.index', compact('webPages'));
    }

    public function create()
    {
        return view('web-pages.create', ['webPage' => null]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'page_content' => 'required|string',
            'heading' => 'nullable|string|max:255',
            'meta_title' => 'nullable|string|max:255',
            'meta_keywords' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:255',
        ]);

        WebPages::create($validated);
        return redirect()->route('webpages.index')->with('success', 'Web Page created successfully.');
    }

   

    public function edit(string $id)
    {
        $webPage = WebPages::findOrFail($id);
        return view('web-pages.edit', compact('webPage'));
    }

    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:web_pages,slug,' . $id,
            'page_content' => 'required|string',
            'heading' => 'nullable|string|max:255',
            'meta_title' => 'nullable|string|max:255',
            'meta_keywords' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:255',
        ]);

        $webPage = WebPages::findOrFail($id);
        $webPage->update($validated);

        return redirect()->route('webpages.index')->with('success', 'Web Page updated successfully.');
    }

    public function destroy(string $id){
        $webPage = WebPages::findOrFail($id);
        $webPage->delete();
        return response()->json([
            'success' => true,
            'message' => 'Web Page deleted successfully.'
        ]);
    }
}
