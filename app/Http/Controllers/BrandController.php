<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Brand;
use Illuminate\Support\Facades\File;
use Illuminate\Validation\Rule;
use App\Imports\BrandImport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Gate;

class BrandController extends Controller
{
    public function index()
    {
        if(!Gate::allows('view brands')) {
            abort(403);
        }
        $brands = Brand::latest()->get();

        return view('brands.index', compact('brands'));
    }

    public function create()
    {
        if(!Gate::allows('create brands')) {
            abort(403);
        }
        return view('brands.create');
    }

    public function store(Request $request)
    {
        if(!Gate::allows('create brands')) {
            abort(403);
        }

        $request->validate([
            'name' => [
                'required',
                    Rule::unique('brands')->whereNull('deleted_at'),
                ],
            'short_name' => [
                'required',
                    Rule::unique('brands')->whereNull('deleted_at'),
            ],
        ]);

        $imageName = uploadFile($request->file('brand_image'), 'uploads/brands/');

        Brand::create([
            'name'          => $request->name,
            'short_name'    => $request->short_name,
            'status'        => $request->status,
            'description'   => $request->description,
            'image'         => $imageName,
            'margin'        => $request->margin,
            'summary'       => $request->summary,
            'heading'       => $request->heading,
            'meta_title'    => $request->meta_title,
            'meta_keywords' => $request->meta_keywords,
            'meta_description' => $request->description
        ]);

        return redirect()->route('brands.index')->with('success', 'Brand created successfully.');
    }

    public function show(string $id)
    {
        //
    }

    public function edit(Brand $brand)
    {
        if(!Gate::allows('edit brands')) {
            abort(403);
        }
        
        return view('brands.edit', compact('brand'));
    }

    public function update(Request $request, Brand $brand)
    {
        if(!Gate::allows('edit brands')) {
            abort(403);
        }
        $request->validate([
            'name' => [
                'required',
                Rule::unique('brands')->ignore($brand->id)->whereNull('deleted_at'),
            ],
            'short_name' => [
                'required',
                Rule::unique('brands')->ignore($brand->id)->whereNull('deleted_at'),
            ],
        ]);

        $oldBrandImage = $brand ? $brand->image : NULL;

        if($request->brand_image) {
            $imageName = uploadFile($request->file('brand_image'), 'uploads/brands/');
            $image_path = public_path($oldBrandImage);

            if ($oldBrandImage && File::exists($image_path)) {
                File::delete($image_path);
            }
        }

        $brand->update([
            'name'          => $request->name,
            'short_name'    => $request->short_name,
            'status'        => $request->status,
            'description'   => $request->description,
            'image'         => $imageName ?? $oldBrandImage,
            'margin'        => $request->margin,
            'summary'       => $request->summary,
            'heading'       => $request->heading,
            'meta_title'    => $request->meta_title,
            'meta_keywords' => $request->meta_keywords,
            'meta_description' => $request->description
        ]);

        return redirect()->route('brands.index')->with('success', 'Brand updated successfully.');
    }

    public function destroy(Brand $brand)
    {
        if(!Gate::allows('delete brands')) {
            abort(403);
        }

        $brand->delete();

        return response()->json([
            'success' => true,
            'message' => 'Brand deleted successfully.'
        ]);
    }

    public function updateStatus(Request $request)
    {
        $brand = Brand::find($request->id);
        if ($brand) {
            $brand->update([
                'status' => $request->status
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Status updated successfully.'
            ]);
        }
        return response()->json(['error' => 'Brand not found.'], 404);
    }

    public function importBrands(Request $request)
    {
        $import = new BrandImport;
        Excel::import($import, $request->file('file'));

        $errors = $import->getErrors();
        if (!empty($errors)) {
            session()->flash('import_errors', $errors);
        }

        return redirect()->route('brands.index')->with('success', 'Brands imported successfully.');
    }
}
