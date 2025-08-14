<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function Index()
    {
        $categories = Category::whereNull('parent_id')
            ->with(['childrenRecursive' => function($query) {
                $query->select([
                    'id', 'slug', 'name', 'parent_id', 'image', 
                    'summary', 'description', 'heading',
                    'meta_title', 'meta_keywords', 'meta_description', 'status'
                ]);
            }])
            ->select([
                'id', 'slug', 'name', 'parent_id', 'image', 
                'summary', 'description', 'heading',
                'meta_title', 'meta_keywords', 'meta_description', 'status'
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

        return response()->json([
            'success' => true,
            'data' => $category,
            'message' => 'Category retrieved successfully.'
        ]);
    }

    public function GetCategoryProducts($slug, Request $request)
    {
        $category = Category::where('slug', $slug)->firstOrFail();
        $categoryIds = $category->childrenRecursive->pluck('id')->push($category->id);

        $products = Product::whereHas('categories', function($query) use ($categoryIds) {
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