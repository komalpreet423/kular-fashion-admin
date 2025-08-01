<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Collection;
use App\Models\Product;
use Illuminate\Support\Facades\Log;

class CollectionController extends Controller
{
    public function showCollection(Request $request, $slug = null)
    {
        if (!$slug) {
            return response()->json([
                'success' => false,
                'message' => 'Collection slug is required.',
            ], 400);
        }

        $collection = Collection::where('slug', $slug)
            ->where('status', 1)
            ->with(['listingOption' => function ($query) {
                $query->where('listable_type', 'collection');
            }])
            ->first();

        if (!$collection) {
            return response()->json([
                'success' => false,
                'message' => 'Collection not found.',
            ], 404);
        }

        $productQuery = Product::with([
            'brand',
            'productType',
            'webImage',
            'quantities',
            'colors.colorDetail',
            'sizes.sizeDetail',
            'webInfo',
            'wishlist'
        ]);

        // Apply collection include conditions
        $includeConditions = json_decode($collection->include_conditions, true);
        if (is_array($includeConditions)) {
            $this->applyConditionsToProductQuery($productQuery, $includeConditions);
        }

        // Apply filters from request
        $productQuery = $this->applyRequestFilters($productQuery, $request);

        // Pagination
        $perPage = $request->input('per_page', $collection->listingOption->show_per_page ?? 12);
        $page = $request->input('page', 1);

        $products = $productQuery->paginate($perPage, ['*'], 'page', $page);

        // Build filters for frontend
        $filters = $this->buildProductFilters($products->items());

        return response()->json([
            'success' => true,
            'data' => [
                'collection' => $this->formatCollection($collection),
                'products' => $products->items(),
                'pagination' => [
                    'total' => $products->total(),
                    'current_page' => $products->currentPage(),
                    'per_page' => $products->perPage(),
                    'total_pages' => $products->lastPage(),
                ],
                'filters' => $filters,
            ]
        ]);
    }

    protected function initializeProductsQuery()
    {
        return Product::with([
            'brand',
            'productType',
            'webImage',
            'quantities',
            'colors.colorDetail',
            'sizes.sizeDetail',
            'webInfo',
            'wishlist'
        ])->where(function ($query) {
            $query->whereHas('webInfo', function ($q) {
                $q->whereIn('status', [1, 2]);
            })->whereHas('quantities', function ($q) {
                $q->select('product_id')
                    ->groupBy('product_id')
                    ->havingRaw('SUM(quantity) > 0');
            })->orWhereDoesntHave('webInfo');
        });
    }

    protected function applyRequestFilters($query, $request)
    {
        Log::debug('Applying filters:', $request->all());

        if ($request->has('product_types')) {
            $productTypes = explode(',', $request->product_types);
            $query->whereHas('productType', function ($q) use ($productTypes) {
                $q->whereIn('id', array_map('intval', $productTypes));
            });
        }

        if ($request->has('sizes')) {
            $sizes = explode(',', $request->sizes);
            $query->whereHas('sizes.sizeDetail', function ($q) use ($sizes) {
                $q->whereIn('id', array_map('intval', $sizes));
            });
        }

        if ($request->has('colors')) {
            $colors = explode(',', $request->colors);
            $query->whereHas('colors.colorDetail', function ($q) use ($colors) {
                $q->whereIn('id', array_map('intval', $colors));
            });
        }

        if ($request->has('brands')) {
            $brands = explode(',', $request->brands);
            $query->whereHas('brand', function ($q) use ($brands) {
                $q->whereIn('id', array_map('intval', $brands));
            });
        }

        if ($request->has('min_price')) {
            $minPrice = (float)$request->min_price;
            $query->where(function ($q) use ($minPrice) {
                $q->where('sale_price', '>=', $minPrice)
                    ->orWhere('price', '>=', $minPrice);
            });
        }

        if ($request->has('max_price')) {
            $maxPrice = (float)$request->max_price;
            $query->where(function ($q) use ($maxPrice) {
                $q->where('sale_price', '<=', $maxPrice)
                    ->orWhere('price', '<=', $maxPrice);
            });
        }

        return $query;
    }

    protected function applyConditionsToProductQuery($query, array $conditions)
    {
        foreach ($conditions as $condition) {
            $type = $condition['type'] ?? null;
            $value = $condition['value'] ?? null;
            $values = $condition['values'] ?? [];

            switch ($type) {
                case 'brand':
                    $query->whereHas('brand', fn($q) => $q->where('slug', $value));
                    break;

                case 'product_type':
                    $query->whereHas('productType', fn($q) => $q->where('slug', $value));
                    break;

                case 'tag':
                    $query->whereHas('tags', fn($q) => $q->where('slug', $value));
                    break;

                case 'product_ids':
                    $query->whereIn('id', $values);
                    break;
            }
        }
    }

    protected function buildProductFilters($products)
    {
        $brands = [];
        $sizes = [];
        $colors = [];
        $productTypes = [];
        $minPrice = PHP_FLOAT_MAX;
        $maxPrice = 0;

        foreach ($products as $product) {
            // Calculate price range
            $price = $product->sale_price ?? $product->price;
            $minPrice = min($minPrice, $price);
            $maxPrice = max($maxPrice, $price);

            // Collect brands
            if ($product->brand) {
                $brands[$product->brand->id] = [
                    'id' => $product->brand->id,
                    'name' => $product->brand->name,
                    'slug' => $product->brand->slug,
                ];
            }

            // Collect product types
            if ($product->productType) {
                $productTypes[$product->productType->id] = [
                    'id' => $product->productType->id,
                    'name' => $product->productType->name,
                ];
            }

            // Collect sizes
            if ($product->relationLoaded('sizes') && $product->sizes->isNotEmpty()) {
                foreach ($product->sizes as $size) {
                    if ($size->relationLoaded('sizeDetail') && $size->sizeDetail) {
                        $sizeName = $size->sizeDetail->size ??
                            $size->sizeDetail->new_code ??
                            $size->sizeDetail->old_code ??
                            "Size {$size->sizeDetail->id}";

                        $sizes[$size->sizeDetail->id] = [
                            'id' => $size->sizeDetail->id,
                            'size' => $sizeName,
                            'name' => $sizeName,
                        ];
                    }
                }
            }

            // Collect colors
            if ($product->relationLoaded('colors') && $product->colors->isNotEmpty()) {
                foreach ($product->colors as $color) {
                    if ($color->relationLoaded('colorDetail') && $color->colorDetail) {
                        $colors[$color->colorDetail->id] = [
                            'id' => $color->colorDetail->id,
                            'name' => $color->colorDetail->name,
                            'hex' => $this->getColorHex($color->colorDetail->name)
                        ];
                    }
                }
            }
        }

        // Handle cases where all products have same price
        if ($minPrice === PHP_FLOAT_MAX) $minPrice = 0;
        if ($minPrice === $maxPrice) $maxPrice = $minPrice + 100;

        return [
            'brands' => array_values($brands),
            'sizes' => array_values($sizes),
            'colors' => array_values($colors),
            'product_types' => array_values($productTypes),
            'price' => [
                'min' => (float)number_format($minPrice, 2, '.', ''),
                'max' => (float)number_format($maxPrice, 2, '.', '')
            ]
        ];
    }

    private function formatCollection($collection)
    {
        return [
            'id' => $collection->id,
            'name' => $collection->name,
            'slug' => $collection->slug,
            'description' => $collection->description,
            'heading' => $collection->heading,
            'image' => $collection->image ? url($collection->image) : null,
            'listing_options' => $collection->listingOption ? [
                'hide_filters' => $collection->listingOption->hide_filters,
                'show_all_filters' => $collection->listingOption->show_all_filters,
                'visible_filters' => json_decode($collection->listingOption->visible_filters ?? '[]'),
                'show_per_page' => $collection->listingOption->show_per_page,
            ] : null,
        ];
    }
}
