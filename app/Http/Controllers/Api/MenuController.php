<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Department;
use App\Models\Product;
use App\Models\ProductType;
use App\Models\HomeImage;



class MenuController extends Controller
{
   public function index(Request $request)
{
    $departments = Department::where('status', 'Active')
        ->select('id', 'slug', 'name')
        ->with(['products' => function ($query) {
            $query->where('status', 'Active');
        }])
        ->paginate($request->input('length', 5));

    // Transform and update the internal collection
    $departments->setCollection(
        $departments->getCollection()->transform(function ($department) {
            $productTypeIds = $department->products->pluck('product_type_id')->unique()->toArray();

            $productTypes = ProductType::whereIn('id', $productTypeIds)
                ->select('id', 'slug', 'name', 'short_name')
                ->get();

            unset($department->products);
            $department->product_types = $productTypes;

            return $department;
        })
    );

    return response()->json([
        'success' => true,
        'departments' => $departments
    ]);
}




}
