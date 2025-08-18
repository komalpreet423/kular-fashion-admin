<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

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
                    'parent_id',
                    'image',
                    'summary',
                    'description',

                ]);
            }])
            ->select([
                'id',
                'slug',
                'name',
                'parent_id',
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

    public function GetBySlug($slug)
    {
        $category = Category::where('slug', $slug)->with('categoriesProduct.products')->first();

        if (!$category) {
            return response()->json([
                'success' => false,
                'message' => 'Category not found.'
            ], 404);
        }
        $products = $category->categoriesProduct->pluck('products')->filter();
        $filters = $this->buildProductFilters($products);
        return response()->json([
            'success' => true,
            'data' => $category,
            'filters' => $filters,
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

        if ($collection?->listingOption?->hide_filters) {
            return [];
        }

        return $allFilters;
    }
    public function GetCategoryProducts($slug, Request $request)
    {
        $category = Category::where('slug', $slug)->firstOrFail();
        $categoryIds = $category->childrenRecursive->pluck('id')->push($category->id);

        $products = Product::whereHas('categories', function ($query) use ($categoryIds) {
            $query->whereIn('id', $categoryIds);
        })
            ->with([
                'brand',
                'department',
                'productType',
                'webImage',
                'quantities',
                'colors.colorDetail',
                'sizes.sizeDetail',
                'webInfo',
                'wishlist'
            ])
            ->filter($request->all())
            ->paginate($request->get('per_page', 20));

        return response()->json([
            'success' => true,
            'data' => $products,
            'message' => 'Products for category retrieved successfully.'
        ]);
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
            'parent_id' => $category->parent_id,
            'image' => $category->image ? url($category->image) : null,
            'summary' => $category->summary,
            'description' => $category->description,
            'heading' => $category->heading,
            'meta_title' => $category->meta_title,
            'meta_keywords' => $category->meta_keywords,
            'meta_description' => $category->meta_description,
            'status' => $category->status,
            'children' => $this->formatCategories($category->childrenRecursive),
            'created_at' => $category->created_at,
            'updated_at' => $category->updated_at
        ];
    }
}
