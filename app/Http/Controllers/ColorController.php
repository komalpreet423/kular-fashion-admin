<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Color;
use Illuminate\Support\Facades\Gate;

class ColorController extends Controller
{
    public function index()
    {
        if (!Gate::allows('view colors')) {
            abort(403);
        }
        $colors = Color::withCount('productColor')->latest()->get();
        return view('colors.index', compact('colors'));
    }

    public function create()
    {
        if (!Gate::allows('create colors')) {
            abort(403);
        }
        return view('colors.create');
    }

    public function store(Request $request)
    {
        if (!Gate::allows('create colors')) {
            abort(403);
        }

        $request->validate([
            'name' => 'required|unique:colors,name',
            'short_name' => 'required|min:1|max:5|unique:colors,short_name',
            'code' => 'required|min:1|max:3|unique:colors,code',
        ]);

        Color::create([
            'name'              => $request->name,
            'short_name'        => $request->short_name,
            'code'              => $request->code,
            'ui_color_code'     => $request->color,
            'status'            => $request->status,
        ]);

        return redirect()->route('colors.index')->with('success', 'Color created successfully.');
    }

    public function show()
    {
        //
    }

    public function edit($id)
    {
        if (!Gate::allows('edit colors')) {
            abort(403);
        }
        $color = Color::where('id', $id)->first();
        return view('colors.edit', compact('color'));
    }

    public function update(Request $request, Color $color)
    {
        if (!Gate::allows('edit colors')) {
            abort(403);
        }

        $request->validate([
            'name' => 'required|unique:colors,name,'.$color->id,
            'short_name' => 'required|min:1|max:5|unique:colors,short_name,'.$color->id,
            'code' => 'required|min:1|max:3|unique:colors,code,'.$color->id, 
        ]);

        $color->update([
            'name'              => $request->name,
            'short_name'        => $request->short_name,
            'code'              => $request->code,
            'ui_color_code'     => $request->color,
            'status'            => $request->status,
        ]);

        return redirect()->route('colors.index')->with('success', 'Color updated successfully.');
    }

    public function destroy(string $id)
    {
        if (!Gate::allows('delete colors')) {
            abort(403);
        }

        Color::where('id', $id)->delete();

        return response()->json([
            'success' => true,
            'message' => 'Color deleted successfully.'
        ]);
    }

    public function colorStatus(Request $request)
    {
        $color = Color::find($request->id);
        if ($color) {
            $color->update([
                'status' => $request->status
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Status updated successfully.'
            ]);
        }
        return response()->json(['error' => 'Color not found.'], 404);
    }
}
