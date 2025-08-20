<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class CategoryController extends Controller
{
    public function Index()
    {
        $categories = Category::whereNull('parent_id')
            ->with(['childrenRecursive' => function ($query) {
                $query->select([
                    'id',
                    'slug',
                    'name',
                    'image',
                    'summary',
                    'description',
                ]);
            }])
            ->select([
                'id',
                'slug',
                'name',
                'image',
                'summary',
                'description',
            ])
            ->get();

        return response()->json([
            'success' => true,
            'data' => $this->formatCategories($categories),
            'message' => 'Categories retrieved successfully.'
        ]);
    }

    public function GetBySlug($slug, Request $request)
    {
        $category = Category::where('slug', $slug)
            ->with(['categoriesProduct.products' => function ($query) {
                $query->with(['brand', 'productType', 'sizes.sizeDetail', 'colors.colorDetail', 'tags']);
            }])
            ->first();

        if (!$category) {
            return response()->json([
                'success' => false,
                'message' => 'Category not found.'
            ], 404);
        }
        $productsQuery = $category->products()
            ->with(['brand', 'productType', 'sizes.sizeDetail', 'colors.colorDetail', 'tags']);

        $productsQuery = $this->applyRequestFilters($productsQuery, $request);

        $perPage = $request->get('per_page', 12);
        $page = $request->get('page', 1);

        $products = $productsQuery->paginate($perPage, ['*'], 'page', $page);
        $allProducts = $category->products()
            ->with(['brand', 'productType', 'sizes.sizeDetail', 'colors.colorDetail', 'tags'])
            ->get();

        $availableFilters = $this->buildProductFilters($allProducts);

        return response()->json([
            'success' => true,
            'data' => [
                'category' => $category,
                'products' => $products,
                'filters' => $availableFilters
            ],
            'message' => 'Category retrieved successfully.'
        ]);
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
        foreach (['brands', 'sizes', 'colors', 'product_types', 'tags'] as $filterType) {
            $allFilters[$filterType] = array_values($allFilters[$filterType]);
        }

        return $allFilters;
    }

    protected function applyRequestFilters($query, $request)
    {
        $filters = [
            'tags' => ['relation' => 'tags', 'field' => 'id'],
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
    private function formatCategories($categories)
    {
        return $categories;
    }

    private function formatSingleCategory($category)
    {
        return [
            'id' => $category->id,
            'slug' => $category->slug,
            'name' => $category->name,
            'image' => $category->image ? url($category->image) : null,
            'summary' => $category->summary,
            'description' => $category->description
        ];
    }
}
