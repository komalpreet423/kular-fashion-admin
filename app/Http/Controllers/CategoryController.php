<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use Illuminate\Support\Facades\File;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::whereNull('parent_id')
            ->with('childrenRecursive')
            ->get();

        return view('categories.index', compact('categories'));
    }

    public function create()
    {
        $categories = Category::whereNull('parent_id')
            ->with('childrenRecursive')
            ->get();
        return view('categories.create', compact('categories'));
    }

    public function store(Request $request)
    {
        // Validation
        $request->validate([
            'name' => [
                'required',
                Rule::unique('categories')->whereNull('deleted_at'),
            ],
            'parent_id' => 'nullable|exists:categories,id',
            'category_image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        // Handle Image Upload
        $imageName = $request->hasFile('category_image')
            ? uploadFile($request->file('category_image'), 'uploads/categories/')
            : null;

        // Create Category
        Category::create([
            'name'             => $request->name,
            'slug'             => Str::slug($request->slug),
            'parent_id'        => $request->parent_id, // comes from modal hidden input
            'status'           => $request->status ?? 'Active',
            'description'      => $request->description,
            'image'            => $imageName,
            'summary'          => $request->summary,
            'heading'          => $request->heading,
            'meta_title'       => $request->meta_title,
            'meta_keywords'    => $request->meta_keywords,
            'meta_description' => $request->meta_description,
        ]);

        return redirect()
            ->route('categories.index')
            ->with('success', 'Category created successfully.');
    }


    public function edit(Category $category)
    {
        $categories = Category::whereNull('parent_id')
            ->with('childrenRecursive')
            ->get();

        return view('categories.edit', compact('category', 'categories'));
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
            'slug'            => Str::slug($request->slug),
            'parent_id'       => $request->parent_id,
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
        if ($category->image && File::exists(public_path($category->image))) {
            File::delete(public_path($category->image));
        }
        $this->deleteChildren($category);
        $category->delete();
        return response()->json([
            'success' => true,
            'message' => 'Category and its subcategories deleted successfully.'
        ]);
    }

    private function deleteChildren($category)
    {
        foreach ($category->children as $child) {
            if ($child->image && File::exists(public_path($child->image))) {
                File::delete(public_path($child->image));
            }
            $this->deleteChildren($child);

            $child->delete();
        }
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
