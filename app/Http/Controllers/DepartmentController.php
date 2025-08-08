<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Department;
use Illuminate\Support\Facades\File;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Gate;

class DepartmentController extends Controller
{
    public function index()
    {
        if(!Gate::allows('view departments')) {
            abort(403);
        }
        $departments = Department::withCount('products')->latest()->get();

        return view('departments.index', compact('departments'));
    }

    public function create()
    {
        if(!Gate::allows('create departments')) {
            abort(403);
        }
        return view('departments.create');
    }

    public function store(Request $request)
    {
        if(!Gate::allows('create departments')) {
            abort(403);
        }
        $request->validate([
            'name' => [
                'required',
                Rule::unique('departments')->whereNull('deleted_at'),
            ],
        ]);

        $imageName = uploadFile($request->file('image'), 'uploads/departments/');

        Department::create([
            'name'          => $request->name,
            'status'        => $request->status,
            'description'   => $request->description,
            'image'         => $imageName
        ]);

        return redirect()->route('departments.index')->with('success', 'Department created successfully.');
    }

    public function show(string $id)
    {
        //
    }

    public function edit($id)
    {
        if(!Gate::allows('edit departments')) {
            abort(403);
        }
        $department = Department::where('id', $id)->first();

        return view('departments.edit', compact('department'));
    }

    public function update(Request $request, Department $department)
    {
        if(!Gate::allows('edit departments')) {
            abort(403);
        }
        $request->validate([
            'name' => [
                'required',
                Rule::unique('departments')->ignore($department->id)->whereNull('deleted_at'),
            ],
        ]);

        $oldDepartmentImage = $department ? $department->image : NULL;

        if($request->image) {
            $imageName = uploadFile($request->file('image'), 'uploads/departments/');
            $image_path = public_path($oldDepartmentImage);

            if ($oldDepartmentImage && File::exists($image_path)) {
                File::delete($image_path);
            }
        }

        $department->update([
            'name'          => $request->name,
            'status'        => $request->status,
            'description'   => $request->description,
            'image'         => $imageName ?? $oldDepartmentImage
        ]);

        return redirect()->route('departments.index')->with('success', 'Department updated successfully.');
    }

    public function destroy(Department $department)
    {
        if(!Gate::allows('delete departments')) {
            abort(403);
        }
        
        $department->delete();

        return response()->json([
            'success' => true,
            'message' => 'Department deleted successfully.'
        ]);
    }

    public function updateStatus(Request $request)
    {
        $department = Department::find($request->id);
        if ($department) {
            $department->update([
                'status' => $request->status
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Status updated successfully.'
            ]);
        }
        return response()->json(['error' => 'Department not found.'], 404);
    }
}
