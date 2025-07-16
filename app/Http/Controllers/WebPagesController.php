<?php

namespace App\Http\Controllers;

use App\Models\WebPages;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Models\Tag;

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
        $tags = Tag::where('status', 'Active')->pluck('name')->toArray();
        return view('web-pages.create', ['webPage' => null, 'tags' => $tags]);
    }

    public function store(Request $request)
    {
        if (!Gate::allows('create webpages')) {
            abort(403);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:web_pages,slug',
            'published_at' => 'nullable',
            'page_content' => 'nullable|string',
            'description' => 'nullable|string',
            'summary' => 'nullable|string',
            'heading' => 'nullable|string|max:255',
            'meta_title' => 'nullable|string|max:255',
            'meta_keywords' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:1000',
            'filters' => 'nullable|array',
            'rules' => 'nullable|array',
            'image_small' => 'nullable|image',
            'image_medium' => 'nullable|image',
            'image_large' => 'nullable|image',
        ]);

        // Save images to public/assets/images
        foreach (['image_small', 'image_medium', 'image_large'] as $field) {
            if ($request->hasFile($field)) {
                $file = $request->file($field);
                $fileName = time() . '_' . $file->getClientOriginalName();
                $file->move(public_path('assets/images'), $fileName);
                $validated[$field] = $fileName;
            }
        }

        $validated['hide_categories'] = $request->has('hide_categories');
        $validated['hide_all_filters'] = $request->has('hide_all_filters');
        $validated['show_all_filters'] = $request->has('show_all_filters');
        $validated['filters'] = $request->input('filters', []);
        $validated['rules'] = $request->input('rules', []);

        WebPages::create($validated);
        return redirect()->route('webpages.index')->with('success', 'Web Page created successfully.');
    }

    public function edit(string $id)
    {
        if (!Gate::allows('edit webpages')) {
            abort(403);
        }
        $webPage = WebPages::findOrFail($id);
        $tags = Tag::where('status', 'Active')->pluck('name')->toArray();
        return view('web-pages.edit', compact('webPage', 'tags'));
    }

    public function update(Request $request, string $id)
    {
        if (!Gate::allows('edit webpages')) {
            abort(403);
        }

        $validated = $request->validate([

            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:web_pages,slug,' . $id,
            'page_content' => 'nullable|string',
            'published_at' => 'nullable',
            'description' => 'nullable|string',
            'summary' => 'nullable|string',
            'heading' => 'nullable|string|max:255',
            'meta_title' => 'nullable|string|max:255',
            'meta_keywords' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:1000',
            'rules' => 'nullable|array',
            'filters' => 'nullable|array',
            'image_small' => 'nullable|image',
            'image_medium' => 'nullable|image',
            'image_large' => 'nullable|image',
        ]);

        $webPage = WebPages::findOrFail($id);

        foreach (['image_small', 'image_medium', 'image_large'] as $field) {
            if ($request->hasFile($field)) {
                $file = $request->file($field);
                $fileName = time() . '_' . $file->getClientOriginalName();
                $file->move(public_path('assets/images'), $fileName);
                $validated[$field] = $fileName;
            }
        }

        $validated['hide_categories'] = $request->has('hide_categories');
        $validated['hide_all_filters'] = $request->has('hide_all_filters');
        $validated['show_all_filters'] = $request->has('show_all_filters');
        $validated['filters'] = $request->input('filters', []);
        $validated['rules'] = $request->input('rules', []);
        $validated['filter_mode'] = $request->input('filter_mode') === 'show_some' ? 'show_some' : null;



        $webPage->update($validated);

        return redirect()->route('webpages.index')->with('success', 'Web Page updated successfully.');
    }

    public function destroy(string $id)
    {
        if (!Gate::allows('delete webpages')) {
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
