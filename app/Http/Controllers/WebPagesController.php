<?php

namespace App\Http\Controllers;

use App\Models\WebPages;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class WebPagesController extends Controller
{
    public function index()
    {
        if (!Gate::allows('view webpages')) {
            abort(403);
        }
        $webPages = WebPages::all();
        return view('web-pages.index', compact('webPages'));
    }

    public function create()
    {
        if (!Gate::allows('create webpages')) {
            abort(403);
        }
        return view('web-pages.create', ['webPage' => null]);
    }

    public function store(Request $request)
    {
        if (!Gate::allows('create webpages')) {
            abort(403);
        }
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
        if (!Gate::allows('edit webpages')) {
            abort(403);
        }
        $webPage = WebPages::findOrFail($id);
        return view('web-pages.edit', compact('webPage'));
    }

    public function update(Request $request, string $id)
    {
        if (!Gate::allows('edit webpages')) {
            abort(403);
        }
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

    public function destroy(string $id)
    {
        if(!Gate::allows('delete webpages')) {
            abort(403);
        }
        $webPage = WebPages::findOrFail($id);
        $webPage->delete();
        return response()->json([
            'success' => true,
            'message' => 'Web Page deleted successfully.'
        ]);
    }
}
