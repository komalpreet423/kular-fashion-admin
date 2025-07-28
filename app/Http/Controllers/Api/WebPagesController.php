<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\WebPages;
use Illuminate\Http\Request;
use App\Models\Product;

class WebPagesController extends Controller
{
    public function index()
    {
        try {
            $pages = WebPages::whereNotNull('published_at')
                ->orderBy('published_at', 'desc')
                ->get(['id', 'title', 'slug', 'description', 'image_small', 'published_at']);

            return response()->json([
                'success' => true,
                'data' => $pages,
                'meta' => [
                    'count' => $pages->count()
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve web pages',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show(Request $request, $slug)
    {
        try {
            $webPage = WebPages::where('slug', $slug)->firstOrFail();

            $rules = is_array($webPage->rules) ? $webPage->rules : json_decode($webPage->rules, true);
            $filters = is_array($webPage->filters) ? $webPage->filters : json_decode($webPage->filters, true);

            $productsQuery = $this->initializeProductsQuery();

            if (!empty($rules)) {
                $this->applyRules($productsQuery, $rules);
            }

            if (!empty($filters)) {
                $this->applyFilters($productsQuery, $filters);
            }

            $this->applyRequestFilters($productsQuery, $request);

            $perPage = $request->input('per_page', 12);
            $products = $productsQuery->paginate($perPage);

            return response()->json([
                'success' => true,
                'data' => [
                    'page' => $this->formatWebPageData($webPage),
                    'products' => $products->items(),
                    'pagination' => [
                        'current_page' => $products->currentPage(),
                        'per_page' => $products->perPage(),
                        'total' => $products->total(),
                        'last_page' => $products->lastPage(),
                    ]
                ]
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Web page not found'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve web page',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    protected function initializeProductsQuery()
    {
        return Product::with([
            'brand',
            'department',
            'productType',
            'webImage',
            'quantities',
            'colors.colorDetail',
            'sizes.sizeDetail',
            'webInfo',
            'wishlist'
        ])->where(function ($query) {
            $query->whereHas('webInfo', function ($q) {
                $q->where('status', 1);
            })
                ->orWhereHas('webInfo', function ($q) {
                    $q->where('status', 2);
                })->whereHas('quantities', function ($q) {
                    $q->select('product_id')
                        ->groupBy('product_id')
                        ->havingRaw('SUM(quantity) > 0');
                })
                ->orWhereDoesntHave('webInfo');
        });
    }

    protected function applyRules($query, array $rules)
    {
        foreach ($rules as $rule) {
            if (!is_array($rule)) continue;

            $type = $rule['type'] ?? null;
            $condition = $rule['condition'] ?? null;
            $tagIds = $rule['tag_ids'] ?? [];

            // Skip if no valid condition or no tag IDs
            if (empty($condition) || empty($tagIds)) continue;

            switch ($condition) {
                case 'has_tags':
                    // Products must have at least one of these tags
                    $query->whereHas('tags', function ($q) use ($tagIds, $type) {
                        $q->whereIn('id', $tagIds);
                        if ($type === 'must_not') {
                            $q->whereNotIn('id', $tagIds);
                        }
                    });
                    break;

                case 'has_all_tags':
                    // Products must have all of these tags
                    $query->whereHas('tags', function ($q) use ($tagIds, $type) {
                        foreach ($tagIds as $tagId) {
                            $q->where('id', $tagId);
                        }
                        if ($type === 'must_not') {
                            $q->whereNotIn('id', $tagIds);
                        }
                    }, '=', count($tagIds));
                    break;
            }
        }
    }

    protected function applyFilters($query, array $filters)
    {
        foreach ($filters as $filter) {
            if (!is_array($filter)) continue;

            switch ($filter['type'] ?? null) {
                case 'brands':
                    if (!empty($filter['values'])) {
                        $query->whereIn('brand_id', $filter['values']);
                    }
                    break;

                case 'product_types':
                    if (!empty($filter['values'])) {
                        $query->whereHas('productType', function ($q) use ($filter) {
                            $q->whereIn('id', $filter['values']);
                        });
                    }
                    break;

                case 'sizes':
                    if (!empty($filter['values'])) {
                        $query->whereHas('sizes', function ($q) use ($filter) {
                            $q->whereIn('size_id', $filter['values']);
                        });
                    }
                    break;

                case 'colors':
                    if (!empty($filter['values'])) {
                        $query->whereHas('colors', function ($q) use ($filter) {
                            $q->whereIn('color_id', $filter['values']);
                        });
                    }
                    break;

                case 'price_range':
                    if (isset($filter['min'], $filter['max'])) {
                        $query->whereHas('sizes', function ($q) use ($filter) {
                            $q->whereBetween('web_price', [$filter['min'], $filter['max']]);
                        });
                    }
                    break;
            }
        }
    }

    protected function applyRequestFilters($query, Request $request)
    {
        if ($request->filled('brands')) {
            $brands = explode(',', $request->input('brands'));
            $query->whereIn('brand_id', $brands);
        }

        if ($request->filled('product_types')) {
            $productTypes = explode(',', $request->input('product_types'));
            $query->whereHas('productType', function ($q) use ($productTypes) {
                $q->whereIn('id', $productTypes);
            });
        }

        if ($request->filled('sizes')) {
            $sizes = explode(',', $request->input('sizes'));
            $query->whereHas('sizes', function ($q) use ($sizes) {
                $q->whereIn('size_id', $sizes);
            });
        }

        if ($request->filled('colors')) {
            $colors = explode(',', $request->input('colors'));
            $query->whereHas('colors', function ($q) use ($colors) {
                $q->whereIn('color_id', $colors);
            });
        }

        if ($request->filled('min_price') && $request->filled('max_price')) {
            $query->whereHas('sizes', function ($q) use ($request) {
                $q->whereBetween('web_price', [
                    $request->input('min_price'),
                    $request->input('max_price')
                ]);
            });
        }

        if ($request->filled('sort_by')) {
            $sortField = $request->input('sort_by');
            $sortDirection = $request->input('sort_dir', 'asc');
            $query->orderBy($sortField, $sortDirection);
        }
    }

    protected function formatWebPageData(WebPages $webPage)
    {
        return [
            'id' => $webPage->id,
            'title' => $webPage->title,
            'slug' => $webPage->slug,
            'heading' => $webPage->heading,
            'content' => $webPage->page_content,
            'description' => $webPage->description,
            'summary' => $webPage->summary,
            'meta_title' => $webPage->meta_title,
            'meta_description' => $webPage->meta_description,
            'meta_keywords' => $webPage->meta_keywords,
            'image_small' => $webPage->image_small,
            'image_medium' => $webPage->image_medium,
            'image_large' => $webPage->image_large,
            'published_at' => $webPage->published_at,
            'hide_categories' => $webPage->hide_categories,
            'hide_all_filters' => $webPage->hide_all_filters,
            'show_all_filters' => $webPage->show_all_filters
        ];
    }
}
