<?php

namespace App\Http\Controllers\Api;

use Illuminate\Support\Facades\Validator;
use App\Http\Resources\BrandCollection;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Illuminate\Http\Request;
use App\Models\Brand;
use App\Models\Product;
use App\Models\ProductQuantity;
use App\Models\ProductSize;
use Exception;
use App\Models\Size;

class BrandController extends Controller
{
    public function brands(Request $request)
    {

        $brands = Brand::where('status','Active')->paginate($request->input('length', 42));
        
        if($brands)
        {
            return new BrandCollection($brands); 
        }
    }


    public function getProductByBrandId(Request $request)
    {
        $brandId = $request->input('brand_id');
        $sizeId = $request->input('size_id');
        $length = $request->input('length', 10);

        if (!$brandId) {
            return response()->json([
                'success' => false,
                'message' => 'The brand_id field is required.',
                'data' => []
            ], 422);
        }

        if (!Brand::where('id', $brandId)->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'The provided brand_id does not exist.',
                'data' => []
            ], 422);
        }

        if ($sizeId && !Size::where('id', $sizeId)->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'The provided size_id is invalid.',
                'data' => []
            ], 422);
        }

        $query = Product::where('status', 'Active')
            ->where('brand_id', $brandId);


        if ($sizeId) {
            $query->whereHas('sizes', function ($q) use ($sizeId) {
                $q->where('size_id', $sizeId)
                    ->whereExists(function ($subQuery) {
                        $subQuery->select(DB::raw(1))
                            ->from('product_quantities')
                            ->whereColumn('product_quantities.product_size_id', 'product_sizes.id')
                            ->where('total_quantity', '>', 0);
                    });
            });
        }


        if ($sizeId) {
            $query->with(['sizes' => function ($q) use ($sizeId) {
                $q->where('size_id', $sizeId)
                    ->whereExists(function ($subQuery) {
                        $subQuery->select(DB::raw(1))
                            ->from('product_quantities')
                            ->whereColumn('product_quantities.product_size_id', 'product_sizes.id')
                            ->where('total_quantity', '>', 0);
                    });
            }]);
        } else {
            $query->with('sizes');
        }

        $products = $query->paginate($length);


        if ($products->isEmpty()) {
            return response()->json([
                'data' => []
            ], 200);
        }

        return response()->json([
            'success' => true,
            'data' => $products->items(),
            'pagination' => [
                'total' => $products->total(),
                'per_page' => $products->perPage(),
                'current_page' => $products->currentPage(),
                'last_page' => $products->lastPage(),
                'next_page_url' => $products->nextPageUrl(),
                'prev_page_url' => $products->previousPageUrl(),
            ],
        ]);
    }
}
