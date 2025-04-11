<?php

namespace App\Http\Controllers;

use App\Models\PickList;
use App\Models\OrderItem;
use App\Models\Branch;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class PickListController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $defaultDate = $request->date ?? now()->subDay()->format('Y-m-d');
        $defaultBranch = $request->branch ?? Auth::user()->branch_id;

        $query = OrderItem::with(['product.department', 'product.productType', 'branch']);

        if (!empty($defaultBranch)) {
            $query->where('branch_id', $defaultBranch);
        }

        if (!empty($defaultDate)) {
            $query->whereDate('created_at', $defaultDate);
        }

        $orderItems = $query->latest('created_at')->get();
        $branches = Branch::all();

        return view('pick-list.index', compact('orderItems', 'branches', 'defaultDate', 'defaultBranch'));
    }



    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(PickList $report)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(PickList $report)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, PickList $report)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PickList $report)
    {
        //
    }
}
