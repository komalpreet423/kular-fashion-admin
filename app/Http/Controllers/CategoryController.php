<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use Illuminate\Support\Facades\File;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Gate;

class CategoryController extends Controller
{
    public function index()
    {
        // if (!Gate::allows('view categories')) {
        //     abort(403);
        // }

        $categories = Category::latest()->get();
        return view('categories.index', compact('categories'));
    }

    public function create()
    {
        // if (!Gate::allows('create categories')) {
        //     abort(403);
        // }
        return view('categories.create');
    }

    public function store(Request $request)
    {
        // if (!Gate::allows('create categories')) {
        //     abort(403);
        // }

        $request->validate([
            'name' => [
                'required',
                Rule::unique('categories')->whereNull('deleted_at'),
            ],
        ]);

        $imageName = $request->hasFile('category_image') ? uploadFile($request->file('category_image'), 'uploads/categories/') : null;

        Category::create([
            'name'            => $request->name,
            'status'          => $request->status,
            'description'     => $request->description,
            'image'           => $imageName,
            'summary'         => $request->summary,
            'heading'         => $request->heading,
            'meta_title'      => $request->meta_title,
            'meta_keywords'   => $request->meta_keywords,
            'meta_description' => $request->meta_description,
        ]);

        return redirect()->route('categories.index')->with('success', 'Category created successfully.');
    }

    public function edit(Category $category)
    {
        // if (!Gate::allows('edit categories')) {
        //     abort(403);
        // }

        return view('categories.edit', compact('category'));
    }

    public function update(Request $request, Category $category)
    {
        // if (!Gate::allows('edit categories')) {
        //     abort(403);
        // }

        $request->validate([
            'name' => [
                'required',
                Rule::unique('categories')->ignore($category->id)->whereNull('deleted_at'),
            ],
        ]);

        $oldImage = $category->image;
        if ($request->hasFile('category_image')) {
            $imageName = uploadFile($request->file('category_image'), 'uploads/categories/');
            if ($oldImage && File::exists(public_path($oldImage))) {
                File::delete(public_path($oldImage));
            }
        }

        $category->update([
            'name'            => $request->name,
            'status'          => $request->status,
            'description'     => $request->description,
            'image'           => $imageName ?? $oldImage,
            'summary'         => $request->summary,
            'heading'         => $request->heading,
            'meta_title'      => $request->meta_title,
            'meta_keywords'   => $request->meta_keywords,
            'meta_description' => $request->meta_description,
        ]);

        return redirect()->route('categories.index')->with('success', 'Category updated successfully.');
    }

    public function destroy(Category $category)
    {
        // if (!Gate::allows('delete categories')) {
        //     abort(403);
        // }

        if ($category->image && File::exists(public_path($category->image))) {
            File::delete(public_path($category->image));
        }

        $category->delete();

        return response()->json([
            'success' => true,
            'message' => 'Category deleted successfully.'
        ]);
    }

    public function updateStatus(Request $request)
    {
        $category = Category::find($request->id);
        if ($category) {
            $category->update(['status' => $request->status]);

            return response()->json([
                'success' => true,
                'message' => 'Status updated successfully.'
            ]);
        }
        return response()->json(['error' => 'Category not found.'], 404);
    }
}
