<?php

namespace App\Http\Controllers;

use App\Models\SizeScale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class SizeScaleController extends Controller
{
    public function index()
    {
        if (!Gate::allows('view size scales')) {
            abort(403);
        }
        $sizeScales = SizeScale::withCount('products')->withCount('sizes')->latest()->get();
        return view('size-scales.index', compact('sizeScales'));
    }

    public function create()
    {
        if (!Gate::allows('create size scales')) {
            abort(403);
        }
        return view('size-scales.create');
    }

    public function store(Request $request)
    {
        if (!Gate::allows('create size scales')) {
            abort(403);
        }

        $request->validate([
            'name' => 'required|unique:size_scales,name',
        ]);

        $isDefault = $request->is_default == '1' ? 1 : 0;

        if ($isDefault) {
            SizeScale::where('is_default', 1)->update(['is_default' => 0]);
        }

        SizeScale::create([
            'name'          => $request->name,
            'is_default'    => $isDefault,
            'status'        => $request->status,
        ]);

        return redirect()->route('size-scales.index')->with('success', 'Size Scale created successfully.');
    }

    public function show($id)
    {
        //
    }

    public function edit($id)
    {
        if (!Gate::allows('edit size scales')) {
            abort(403);
        }
        $sizeScale = SizeScale::where('id', $id)->first();

        return view('size-scales.edit', compact('sizeScale'));
    }

    public function update(Request $request, SizeScale $sizeScale)
    {
        if (!Gate::allows('edit size scales')) {
            abort(403);
        }

        $request->validate([
            'name' => 'required|unique:size_scales,name,' . $sizeScale->id,
        ]);

        $isDefault = $request->is_default == '1' ? 1 : 0;

        if ($isDefault) {
            SizeScale::where('is_default', 1)->update(['is_default' => 0]);
        }

        $sizeScale->update([
            'name'          => $request->name,
            'is_default'    => $isDefault,
            'status'        => $request->status
        ]);

        return redirect()->route('size-scales.index')->with('success', 'Size Scale updated successfully.');
    }

    public function destroy(SizeScale $sizeScale)
    {
        if (!Gate::allows('delete size scales')) {
            abort(403);
        }
        
        $sizeScale->delete();

        return response()->json([
            'success' => true,
            'message' => 'Size scale deleted successfully.'
        ]);
    }

    public function sizeScaleStatus(Request $request)
    {
        $sizeScale = SizeScale::find($request->id);
        if ($sizeScale) {
            $sizeScale->update([
                'status' => $request->status
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Status updated successfully.'
            ]);
        }
        return response()->json(['error' => 'Size Scale not found.'], 404);
    }
}
