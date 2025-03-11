<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ProductType;
use App\Models\Department;
use App\Models\ProductTypeDepartment;
use Illuminate\Support\Facades\File;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Gate;

class ProductTypeController extends Controller
{
    public function index()
    {
        if(!Gate::allows('view product types')) {
            abort(403);
        }
        $productTypes = ProductType::with('productTypeDepartments')->get();
        return view('product-types.index', compact('productTypes'));
    }

    public function create()
    {
        if(!Gate::allows('create product types')) {
            abort(403);
        }
        $departments = Department::all();

        return view('product-types.create', compact('departments'));
    }

    public function store(Request $request)
    {
        if(!Gate::allows('create product types')) {
            abort(403);
        }

        $request->validate([
           'department_id'       => 'required',
           'short_name'          => 'required',
           'name'               => 'required|unique:product_types,name',
        ]);

        $imageName = uploadFile($request->file('image'), 'uploads/product-types/');
        
        $productType = ProductType::create([
            'name'              => $request->name,
            'short_name'        => $request->short_name,
            'status'             => $request->status,
            'description'        => $request->description,
            'image'              => $imageName,
            'summary'       => $request->summary,
            'heading'       => $request->heading,
            'meta_title'    => $request->meta_title,
            'meta_keywords' => $request->meta_keywords,
            'meta_description' => $request->description
        ]);
        
        foreach ($request->department_id as $departmentId) {
            ProductTypeDepartment::create([
                'product_type_id' => $productType->id,
                'department_id'   => $departmentId
            ]);
        }

        return redirect()->route('product-types.index')->with('success', 'Product Type created successfully.');
    }

    public function show(string $id)
    {
        //
    }

    public function edit($id)
    {
        if(!Gate::allows('edit product types')) {
            abort(403);
        }
        $departments = Department::all();
        $productType = ProductType::with('productTypeDepartments')->where('id', $id)->first();
        $selectedDeparments = $productType->productTypeDepartments->pluck('department_id')->toArray();
        return view('product-types.edit', compact('departments', 'productType','selectedDeparments'));
    }

    public function update(Request $request, $id)
    {
        if(!Gate::allows('edit product types')) {
            abort(403);
        }

        $request->validate([
            'department_id'     => 'required',
            'short_name'        => 'required',
            'name'              => 'required|unique:product_types,name,' . $id,
        ]);

        $productType = ProductType::where('id', $id)->first();
        $oldProductTypeImage = $productType ? $productType->image : NULL;

        if($request->image) {
            $imageName = uploadFile($request->file('image'), 'uploads/product-types/');
            $image_path = public_path($oldProductTypeImage);

            if ($oldProductTypeImage && File::exists($image_path)) {
                File::delete($image_path);
            }
        }

        $productType->update([
            'name'              => $request->name,
            'short_name'        => $request->short_name,
            'status'             => $request->status,
            'description'        => $request->description,
            'image'              => $imageName ?? $oldProductTypeImage,
            'description'        =>$request->description,
            'summary'            => $request->summary,
            'heading'       => $request->heading,
            'meta_title'    => $request->meta_title,
            'meta_keywords' => $request->meta_keywords,
            'meta_description' => $request->description
        ]);
        
        ProductTypeDepartment::where('product_type_id',$id)->delete();
        foreach ($request->department_id as $departmentId) {
            ProductTypeDepartment::create([
                'product_type_id' => $id,
                'department_id'   => $departmentId
            ]);
        }

        return redirect()->route('product-types.index')->with('success', 'Product Type updated successfully.');
    }

    public function destroy(string $id)
    {
        if(!Gate::allows('delete product types')) {
            abort(403);
        }
        $productType = ProductType::where('id', $id)->delete();
        ProductTypeDepartment::where('product_type_id',$id)->delete();
        return response()->json([
            'success' => true,
            'message' => 'Product Type deleted successfully.'
        ]);
    }

    public function productTypeStatus(Request $request)
    {
        $productType = ProductType::find($request->id);
        if ($productType) {
            $productType->update([
                'status' => $request->status
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Status updated successfully.'
            ]);
        }
        return response()->json(['error' => 'Product Type not found.'], 404);
    }
}
