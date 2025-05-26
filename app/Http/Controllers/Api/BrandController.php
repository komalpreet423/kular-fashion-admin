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


class BrandController extends Controller
{
    public function brands(Request $request){

        $brands = Brand::where('status','Active')->paginate($request->input('length', 10));
        
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
        $query = Product::query()->where('status', 'Active');
        if ($brandId) {
            $query->where('brand_id', $brandId);
        }
        if ($sizeId) {
            $query->with(['sizes' => function ($q) use ($sizeId) {
                $q->where('size_id', $sizeId)
                  ->whereExists(function ($query) {
                      $query->select(DB::raw(1))
                            ->from('product_quantities')
                            ->whereColumn('product_quantities.product_size_id', 'product_sizes.id')
                            ->where('total_quantity', '>', 0);
                  });
            }]);
        } else {
            $query->with('sizes');
        }
        $products = $query->paginate($length);
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
          
 


