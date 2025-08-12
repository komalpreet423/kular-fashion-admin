<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Collection;
use App\Models\Product;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class CollectionController extends Controller
{
    public function showCollection(Request $request, $slug = null)
    {
        if (!$slug) {
            return response()->json(['success' => false, 'message' => 'Collection slug is required.'], 400);
        }

        $collection = Collection::with('listingOption')
            ->where('slug', $slug)
            ->where('status', 1)
            ->with(['listingOption' => fn($q) => $q->where('listable_type', 'collection')])
            ->first();

        if (!$collection) {
            return response()->json(['success' => false, 'message' => 'Collection not found.'], 404);
        }

        $productQuery = Product::with([
            'brand',
            'productType',
            'webImage',
            'quantities',
            'colors.colorDetail',
            'sizes.sizeDetail',
            'webInfo',
            'wishlist',
            'tags'
        ])->where(function ($query) {
            $query->whereHas('webInfo', fn($q) => $q->whereIn('status', [1, 2]))
                ->whereHas('quantities', fn($q) => $q->select('product_id')
                    ->groupBy('product_id')
                    ->havingRaw('SUM(quantity) > 0'))
                ->orWhereDoesntHave('webInfo');
        });

        $this->applyConditions($productQuery, json_decode($collection->include_conditions, true) ?? [], 'include');
        $this->applyConditions($productQuery, json_decode($collection->exclude_conditions, true) ?? [], 'exclude');
        $this->applyRequestFilters($productQuery, $request);

        $sortOptions = json_decode($collection->listingOption->sort_options, true) ?? [];
        $productQuery = $this->sortingFilter($productQuery, $sortOptions);

        $perPage = $request->input('per_page', $collection->listingOption->show_per_page ?? 12);
        $products = $productQuery->paginate($perPage, ['*'], 'page', $request->input('page', 1));

        return response()->json([
            'success' => true,
            'data' => [
                'collection' => $this->formatCollection($collection),
                'products' => $products->items(),
                'pagination' => $this->getPaginationData($products),
                'filters' => $this->buildProductFilters(
                    $products->items(),
                    $collection->listingOption ? json_decode($collection->listingOption->visible_filters, true) : [],
                    $collection
                ),
            ]
        ]);
    }

    public function sortingFilter($productQuery, $sortOptions)
    {
        if (is_array($sortOptions)) {
            foreach ($sortOptions as $sortOption) {
                switch ($sortOption) {
                    case 'name_asc':
                        $productQuery->orderBy('name', 'asc');
                        break;
                    case 'name_desc':
                        $productQuery->orderBy('name', 'desc');
                        break;
                    case 'manufacturer_asc':
                        $productQuery
                            ->leftJoin('brands', 'products.brand_id', '=', 'brands.id')
                            ->orderBy('brands.name', 'asc')
                            ->select('products.*');
                        break;
                    case 'manufacturer_desc':
                        $productQuery
                            ->leftJoin('brands', 'products.brand_id', '=', 'brands.id')
                            ->orderBy('brands.name', 'desc')
                            ->select('products.*');
                        break;
                    case 'price_asc':
                        $productQuery->orderByRaw('COALESCE(sale_price, price) ASC');
                        break;
                    case 'price_desc':
                        $productQuery->orderByRaw('COALESCE(sale_price, price) DESC');
                        break;
                    case 'newest':
                        $productQuery->orderBy('created_at', 'desc');
                        break;
                    case 'saving_price_asc':
                        $productQuery
                            ->whereNotNull('sale_price')
                            ->orderByRaw('(price - sale_price) ASC');
                        break;
                    case 'saving_price_desc':
                        $productQuery
                            ->whereNotNull('sale_price')
                            ->orderByRaw('(price - sale_price) DESC');
                        break;
                    case 'id_asc':
                        $productQuery->orderBy('products.id', 'asc');
                        break;
                    case 'id_desc':
                        $productQuery->orderBy('products.id', 'desc');
                        break;
                }
            }
        }

        if (!is_array($sortOptions) || empty($sortOptions)) {
            $productQuery->orderBy('created_at', 'desc');
        }

        return $productQuery;
    }

    protected function applyConditions(&$query, array $conditions, $type = 'include')
    {
        if (empty($conditions)) return;

        if (isset($conditions['price_range'])) {
            $this->applyPriceRangeCondition($query, $conditions['price_range'], $type);
        }

        if (isset($conditions['tags'])) {
            $this->applyTagConditions($query, $conditions['tags'], $type);
        }

        if (isset($conditions['product_types'])) {
            $this->applyProductTypeConditions($query, $conditions['product_types'], $type);
        }

        if (isset($conditions['price_list'])) {
            $this->applyPriceListCondition($query, $conditions['price_list'], $type);
        }

        foreach ($conditions as $condition) {
            if (is_array($condition) && isset($condition['type'])) {
                $this->applySingleCondition($query, $condition, $type);
            }
        }
    }

    protected function applySingleCondition(&$query, array $condition, $type)
    {
        $value = $condition['value'] ?? null;
        $values = $condition['values'] ?? [];

        switch ($condition['type']) {
            case 'tag':
                $this->applyTagConditions($query, $values, $type);
                break;
            case 'brand':
                $method = $type === 'include' ? 'whereHas' : 'whereDoesntHave';
                $query->$method('brand', fn($q) => $q->where('slug', $value));
                break;
            case 'product_type':
                $this->applyProductTypeConditions($query, $values, $type);
                break;
            case 'product_ids':
                $method = $type === 'include' ? 'whereIn' : 'whereNotIn';
                $query->$method('id', $values);
                break;
            case 'price_list':
                $this->applyPriceListCondition($query, $value, $type);
                break;
            case 'price_range':
                $this->applyPriceRangeCondition($query, [
                    'min' => $condition['min'] ?? 0,
                    'max' => $condition['max'] ?? PHP_FLOAT_MAX
                ], $type);
                break;
        }
    }

    protected function applyProductTypeConditions($query, $productTypeIds, $type = 'include')
    {
        if (empty($productTypeIds)) return;

        $method = $type === 'include' ? 'whereHas' : 'whereDoesntHave';
        $query->$method('productType', fn($q) => $q->whereIn('id', array_map('intval', (array)$productTypeIds)));
    }

    protected function applyTagConditions($query, $tagIds, $type = 'include')
    {
        if (empty($tagIds)) return;

        $method = $type === 'include' ? 'whereHas' : 'whereDoesntHave';
        $query->$method('tags', fn($q) => $q->whereIn('tag_id', array_map('intval', (array)$tagIds)));
    }

    protected function applyPriceListCondition(&$query, $price, $type = 'include')
    {
        if ($type === 'include') {
            $query->where(function ($q) use ($price) {
                $q->where('mrp', '<=', $price)
                    ->orWhere('price', '<=', $price)
                    ->orWhere('sale_price', '<=', $price);
            });
        } else {
            $query->where(function ($q) use ($price) {
                $q->where(function ($q2) use ($price) {
                    $q2->whereNull('mrp')->orWhere('mrp', '>', $price);
                })->where(function ($q2) use ($price) {
                    $q2->whereNull('price')->orWhere('price', '>', $price);
                })->where(function ($q2) use ($price) {
                    $q2->whereNull('sale_price')->orWhere('sale_price', '>', $price);
                });
            });
        }
    }

    protected function applyPriceRangeCondition(&$query, array $priceRange, $type = 'include')
    {
        $min = (float)($priceRange['min'] ?? 0);
        $max = (float)($priceRange['max'] ?? PHP_FLOAT_MAX);

        if ($type === 'include') {
            $query->where(function ($q) use ($min, $max) {
                $q->where(function ($q2) use ($min, $max) {
                    $q2->whereNotNull('sale_price')
                        ->where('sale_price', '>=', $min)
                        ->where('sale_price', '<=', $max);
                })->orWhere(function ($q2) use ($min, $max) {
                    $q2->whereNull('sale_price')
                        ->whereNotNull('price')
                        ->where('price', '>=', $min)
                        ->where('price', '<=', $max);
                });
            });
        } else {
            $query->where(function ($q) use ($min, $max) {
                $q->where(function ($q2) use ($min, $max) {
                    $q2->whereNull('sale_price')
                        ->orWhere('sale_price', '<', $min)
                        ->orWhere('sale_price', '>', $max);
                })->where(function ($q2) use ($min, $max) {
                    $q2->whereNull('price')
                        ->orWhere('price', '<', $min)
                        ->orWhere('price', '>', $max);
                });
            });
        }
    }

    protected function applyRequestFilters($query, $request)
    {
        $filters = [
            'tags' => ['relation' => 'tags', 'field' => 'tag_id'],
            'product_types' => ['relation' => 'productType', 'field' => 'id'],
            'sizes' => ['relation' => 'sizes.sizeDetail', 'field' => 'id'],
            'brands' => ['relation' => 'brand', 'field' => 'id'],
            'colors' => ['relation' => 'colors.colorDetail', 'field' => 'id'],
        ];

        foreach ($filters as $key => $config) {
            if ($request->has($key)) {
                $values = is_array($request->$key) ? $request->$key : explode(',', $request->$key);
                $query->whereHas($config['relation'], fn($q) => $q->whereIn($config['field'], array_map('intval', $values)));
            }
        }

        if ($request->has('min_price')) {
            $query->where(function ($q) use ($request) {
                $q->where('sale_price', '>=', (float)$request->min_price)
                    ->orWhere('price', '>=', (float)$request->min_price);
            });
        }

        if ($request->has('max_price')) {
            $query->where(function ($q) use ($request) {
                $q->where('sale_price', '<=', (float)$request->max_price)
                    ->orWhere('price', '<=', (float)$request->max_price);
            });
        }

        return $query;
    }

    protected function buildProductFilters($products, $visibleFilters = [], $collection = null)
    {
        $allFilters = [
            'price' => ['min' => PHP_FLOAT_MAX, 'max' => 0],
            'brands' => [],
            'sizes' => [],
            'colors' => [],
            'product_types' => [],
            'tags' => []
        ];

        foreach ($products as $product) {
            $price = $product->sale_price ?? $product->price;
            $allFilters['price']['min'] = min($allFilters['price']['min'], $price);
            $allFilters['price']['max'] = max($allFilters['price']['max'], $price);

            if ($product->brand) {
                $allFilters['brands'][$product->brand->id] = [
                    'id' => $product->brand->id,
                    'name' => $product->brand->name,
                    'slug' => $product->brand->slug ?? Str::slug($product->brand->name)
                ];
            }

            if ($product->productType) {
                $allFilters['product_types'][$product->productType->id] = [
                    'id' => $product->productType->id,
                    'name' => $product->productType->name,
                    'slug' => $product->productType->slug ?? Str::slug($product->productType->name)
                ];
            }

            foreach ($product->sizes ?? [] as $size) {
                if ($size->sizeDetail) {
                    $sizeName = $size->sizeDetail->size ?? $size->sizeDetail->new_code ?? $size->sizeDetail->old_code ?? "Size {$size->sizeDetail->id}";
                    $allFilters['sizes'][$size->sizeDetail->id] = [
                        'id' => $size->sizeDetail->id,
                        'size' => $sizeName,
                        'name' => $sizeName
                    ];
                }
            }

            foreach ($product->colors ?? [] as $color) {
                if ($color->colorDetail) {
                    $allFilters['colors'][$color->colorDetail->id] = [
                        'id' => $color->colorDetail->id,
                        'name' => $color->colorDetail->name,
                        'hex' => $color->colorDetail->ui_color_code ?? $color->colorDetail->hex ?? '#000000'
                    ];
                }
            }

            foreach ($product->tags ?? [] as $tag) {
                $allFilters['tags'][$tag->id] = [
                    'id' => $tag->id,
                    'name' => $tag->name,
                    'slug' => $tag->slug
                ];
            }
        }

        $allFilters['price'] = [
            'min' => $allFilters['price']['min'] === PHP_FLOAT_MAX ? 0 : (float)number_format($allFilters['price']['min'], 2, '.', ''),
            'max' => $allFilters['price']['min'] === $allFilters['price']['max']
                ? $allFilters['price']['min'] + 100
                : (float)number_format($allFilters['price']['max'], 2, '.', '')
        ];

        if ($collection?->listingOption?->hide_filters) {
            return [];
        }

        $showAllFilters = empty($visibleFilters) || ($collection?->listingOption?->show_all_filters);
        $filterMap = [
            'brand' => 'brands',
            'size' => 'sizes',
            'product_types' => 'product_types',
            'color' => 'colors',
            'tag' => 'tags'
        ];

        $finalFilters = [];
        if ($showAllFilters || in_array('price', $visibleFilters)) {
            $finalFilters['price'] = $allFilters['price'];
        }

        foreach ($filterMap as $visibleKey => $filterKey) {
            if ($showAllFilters || in_array($visibleKey, $visibleFilters)) {
                if (!empty($allFilters[$filterKey])) {
                    $finalFilters[$filterKey] = array_values($allFilters[$filterKey]);
                }
            }
        }

        return $finalFilters;
    }

    private function formatCollection($collection)
    {
        return [
            'id' => $collection->id,
            'name' => $collection->name,
            'slug' => $collection->slug,
            'description' => $collection->description,
            'summary' => $collection->summary,
            'heading' => $collection->heading,
            'image' => $collection->image ? url($collection->image) : null,
            'listing_options' => $collection->listingOption ? [
                'hide_filters' => (bool)$collection->listingOption->hide_filters,
                'show_all_filters' => (bool)$collection->listingOption->show_all_filters,
                'visible_filters' => json_decode($collection->listingOption->visible_filters ?? '[]', true) ?: [],
                'show_per_page' => $collection->listingOption->show_per_page,
                'sort_options' => json_decode($collection->listingOption->sort_options ?? '[]', true) ?: [],
                'default_sort' => $collection->listingOption->default_sort ?? 'newest'
            ] : [
                'hide_filters' => false,
                'show_all_filters' => false,
                'visible_filters' => [],
                'show_per_page' => 12,
                'sort_options' => [],
                'default_sort' => 'newest'
            ],
            'include_conditions' => json_decode($collection->include_conditions, true) ?: [],
            'exclude_conditions' => json_decode($collection->exclude_conditions, true) ?: [],
        ];
    }

    private function getPaginationData($paginator)
    {
        return [
            'total' => $paginator->total(),
            'current_page' => $paginator->currentPage(),
            'per_page' => $paginator->perPage(),
            'total_pages' => $paginator->lastPage(),
        ];
    }
}
