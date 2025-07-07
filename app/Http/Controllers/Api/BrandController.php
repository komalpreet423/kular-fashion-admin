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

        $brands = Brand::where('status', 'Active')->paginate($request->input('length', 42));

        if ($brands) {
            return new BrandCollection($brands);
        }
    }


    public function getBrandProducts(Request $request)
    {
        $brandSlug = $request->input('slug');
        $perPage = $request->input('per_page', 12);

        $brand = Brand::where('slug', $brandSlug)->first();

        if (!$brand) {
            return response()->json([
                'success' => false,
                'message' => 'Brand not found.',
                'data' => []
            ], 404);
        }

        $query = Product::with([
            'brand',
            'department',
            'productType',
            'webImage',
            'quantities',
            'colors.colorDetail',
            'sizes.sizeDetail',
            'webInfo',
            'wishlist'
        ])->where('brand_id', $brand->id)
            ->where('status', 'Active');

        $query->where(function ($q) {
            // Include products where `web_info.status` is 1
            $q->whereHas('webInfo', function ($q) {
                $q->where('status', 1);
            });

            // Include products where `web_info.status` is 2 AND sum of `quantities.quantity` > 0
            $q->orWhereHas('webInfo', function ($q) {
                $q->where('status', 2);
            })->whereHas('quantities', function ($q) {
                $q->select('product_id')
                    ->groupBy('product_id')
                    ->havingRaw('SUM(quantity) > 0');
            });

            $q->orWhereDoesntHave('webInfo');
        });

        if ($request->has('department_slug')) {
            $slug = $request->input('department_slug');
            $query->whereHas('department', function ($q) use ($slug) {
                $q->where('slug', $slug);
            });
        }

        if ($request->has('product_type_slug')) {
            $slug = $request->input('product_type_slug');
            $query->whereHas('productType', function ($q) use ($slug) {
                $q->where('slug', $slug);
            });
        }

        if ($request->has('product_ids')) {
            $product_ids = explode(',', $request->input('product_ids'));
            $query->whereIn('id', $product_ids);
        }

        if ($request->has('product_types') && !empty($request->input('product_types'))) {
            $product_types = explode(',', $request->input('product_types'));
            $query->whereHas('productType', function ($q) use ($product_types) {
                $q->whereIn('id', $product_types);
            });
        }

        if ($request->has('sizes') && !empty($request->input('sizes')) && !is_null($request->input('sizes'))) {
            $sizes = explode(',', $request->input('sizes'));
            $query->whereHas('sizes', function ($q) use ($sizes) {
                $q->whereIn('size_id', $sizes);
            });
        }

        if ($request->has('colors') && !empty($request->input('colors')) && !is_null($request->input('colors'))) {
            $colors = explode(',', $request->input('colors'));
            $query->whereHas('colors', function ($q) use ($colors) {
                $q->whereIn('color_id', $colors);
            });
        }

        if ($request->has('min_price') && $request->has('max_price')) {
            $minPrice = $request->input('min_price');
            $maxPriceToFilter = $request->input('max_price');
            $query->whereHas('sizes', function ($q) use ($minPrice, $maxPriceToFilter) {
                $q->whereBetween('web_price', [$minPrice, $maxPriceToFilter]);
            });
        }
        if ($request->has('sort_by')) {
            $sortField = $request->input('sort_by');
            $sortDirection = $request->input('sort_dir', 'asc');
            $query->orderBy($sortField, $sortDirection);
        }

        $products = $query->paginate($perPage);
        $productIds = $query->pluck('id');

        $productTypes = Product::whereIn('id', $productIds)
            ->with('productType')
            ->get()
            ->pluck('productType')
            ->unique('id')
            ->values()
            ->map(function ($type) {
                return [
                    'id' => $type->id,
                    'name' => $type->name,
                ];
            });

        $colors = Product::whereIn('id', $productIds)
            ->with('colors.colorDetail')
            ->get()
            ->pluck('colors')
            ->flatten()
            ->unique(function ($color) {
                return $color->colorDetail ? $color->colorDetail->id : null;
            })->values()
            ->map(function ($color) {
                return [
                    'id' => $color->color_id,
                    'name' => $color->colorDetail ? $color->colorDetail->name : null,
                    'color_code' => $color->colorDetail ? $color->colorDetail->ui_color_code : null,
                ];
            });

        $sizes = Product::whereIn('id', $productIds)
            ->with('sizes.sizeDetail')
            ->get()
            ->pluck('sizes')
            ->flatten()
            ->unique(fn($size) => $size->sizeDetail->size)
            ->map(function ($size) {
                return [
                    'id' => $size->size_id,
                    'name' => $size->sizeDetail->size,
                ];
            });

        $minPrice = $products->min(function ($product) {
            return $product->sizes->min('web_sale_price');
        });

        $maxPrice = $products->max(function ($product) {
            return $product->sizes->max('web_price');
        });

        $response = [
            'success' => true,
            'brand_name' => $brand->name,
            'data' => $products->items(),
            'pagination' => [
                'total' => $products->total(),
                'per_page' => $products->perPage(),
                'current_page' => $products->currentPage(),
                'last_page' => $products->lastPage(),
                'next_page_url' => $products->nextPageUrl(),
                'prev_page_url' => $products->previousPageUrl(),
            ],
        ];

        if ($request->filters == true) {
            $response['filters'] = [
                'product_types' => $productTypes,
                'colors' => $colors,
                'sizes' => $sizes,
                'price' => [
                    'min' => (float)$minPrice,
                    'max' => (float)$maxPrice,
                ]
            ];
        }

        return response()->json($response);
    }
}
