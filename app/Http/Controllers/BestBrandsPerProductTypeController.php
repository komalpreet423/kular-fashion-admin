<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Branch;
use App\Models\Department;
use App\Models\ProductType;
use App\Models\Category;
use App\Models\Product;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class BestBrandsPerProductTypeController extends Controller
{
    /**
     * Display the filter form.
     */
    public function index(Request $request)
    {
        $brands = Brand::get();
        $branches = Branch::get();
        $departments = Department::get();
        $productTypes = ProductType::get();
        $categories = Category::get();

        $validatedData = [];

        $orderItems = OrderItem::with(['brand', 'product.productType'])->get();

        // Apply filters
        $filteredItems = $this->applyFilters($orderItems, $validatedData);

        // Group by product_type_id
        $groupedByProductType = $filteredItems->groupBy(fn($item) => optional($item->product)->product_type_id);

        $finalResult = collect();

        // Sort product types alphabetically by name
        $sortedProductTypes = $groupedByProductType->sortBy(function ($items, $productTypeId) {
            return optional($items->first()->product->productType)->name;
        });

        foreach ($sortedProductTypes as $productTypeId => $items) {
            // Group by brand within product type
            $groupedByBrand = $items->groupBy('brand_id');

            // Aggregate brand data under this product type
            $aggregated = $groupedByBrand->map(function ($group) {
                $first = $group->first();
                return [
                    'product_type_id' => optional($first->product)->product_type_id,
                    'product_type_name' => optional($first->product->productType)->name,
                    'brand_id' => $first->brand_id,
                    'brand_name' => optional($first->brand)->name,
                    'total_quantity' => $group->sum('quantity'),
                    'sales_value' => $group->sum(fn($item) => $item->quantity * $item->changed_price),
                ];
            })->sort(function ($a, $b) {
                // Sort by quantity descending, then sales_value descending
                if ($a['total_quantity'] === $b['total_quantity']) {
                    return $b['sales_value'] <=> $a['sales_value'];
                }
                return $b['total_quantity'] <=> $a['total_quantity'];
            })->values();

            // Assign ranks per product type
            foreach ($aggregated as $index => $item) {
                $rankNumber = $index + 1;
                $suffix = match (true) {
                    $rankNumber % 100 >= 11 && $rankNumber % 100 <= 13 => 'TH',
                    $rankNumber % 10 === 1 => 'ST',
                    $rankNumber % 10 === 2 => 'ND',
                    $rankNumber % 10 === 3 => 'RD',
                    default => 'TH',
                };

                $finalResult->push([
                    'rank' => $rankNumber . $suffix,
                    'product_type_id' => $item['product_type_id'],
                    'product_type_name' => $item['product_type_name'],
                    'brand_id' => $item['brand_id'],
                    'brand_name' => $item['brand_name'],
                    'total_quantity' => $item['total_quantity'],
                    'sales_value' => number_format($item['sales_value'], 2),
                ]);
            }
        }

        $filteredData = $finalResult;

        $request->session()->put('brandsPerProductTypeFilteredData', $filteredData);

        return view('best-brands-per-product-type.index', compact(
            'brands',
            'branches',
            'departments',
            'productTypes',
            'categories',
            'filteredData'
        ));
    }

    /**
     * Process the filtering and redirect to results page.
     */
    public function filterData(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'branch_id' => 'nullable',
                'department_id' => 'nullable',
                'product_type_id' => 'nullable',
                'season' => 'nullable',
                'from_date' => 'nullable|date',
                'to_date' => 'nullable|date',
                'sequence' => 'nullable|string',
                'show_options' => 'nullable|string',
                'first_lines_count' => 'nullable|integer'
            ]);

            $orderItems = OrderItem::with(['brand', 'product.productType'])->get();
            $filteredItems = $this->applyFilters($orderItems, $validatedData);

            $sequence = strtolower($validatedData['sequence'] ?? 'descending');

            // Group by product_type_id
            $groupedByProductType = $filteredItems->groupBy(fn($item) => optional($item->product)->product_type_id);

            // Sort product types by name (ascending or descending)
            $sortedProductTypes = $groupedByProductType->sortBy(function ($items, $productTypeId) {
                return optional($items->first()->product->productType)->name;
            }, SORT_REGULAR, $sequence === 'descending');

            $finalResult = collect();

            foreach ($sortedProductTypes as $productTypeId => $items) {
                $groupedByBrand = $items->groupBy('brand_id');

                $aggregated = $groupedByBrand->map(function ($group) {
                    $first = $group->first();
                    return [
                        'product_type_id' => optional($first->product)->product_type_id,
                        'product_type_name' => optional($first->product->productType)->name,
                        'brand_id' => $first->brand_id,
                        'brand_name' => optional($first->brand)->name,
                        'total_quantity' => $group->sum('quantity'),
                        'sales_value' => $group->sum(fn($item) => $item->quantity * $item->changed_price),
                    ];
                });

                // Rank brands inside the product type (always quantity desc, sales desc)
                $rankSorted = $aggregated->sort(function ($a, $b) {
                    if ($a['total_quantity'] === $b['total_quantity']) {
                        return $b['sales_value'] <=> $a['sales_value'];
                    }
                    return $b['total_quantity'] <=> $a['total_quantity'];
                })->values();

                $ranked = $rankSorted->map(function ($item, $index) {
                    $rankNumber = $index + 1;
                    $suffix = match (true) {
                        $rankNumber % 100 >= 11 && $rankNumber % 100 <= 13 => 'TH',
                        $rankNumber % 10 === 1 => 'ST',
                        $rankNumber % 10 === 2 => 'ND',
                        $rankNumber % 10 === 3 => 'RD',
                        default => 'TH',
                    };

                    return array_merge($item, [
                        'rank' => $rankNumber . $suffix,
                        'sales_value' => number_format($item['sales_value'], 2),
                    ]);
                });

                // Display sort by quantity + sales based on selected sequence
                $displaySorted = $ranked->sort(function ($a, $b) use ($sequence) {
                    if ($a['total_quantity'] === $b['total_quantity']) {
                        return $sequence === 'ascending'
                            ? $a['sales_value'] <=> $b['sales_value']
                            : $b['sales_value'] <=> $a['sales_value'];
                    }
                    return $sequence === 'ascending'
                        ? $a['total_quantity'] <=> $b['total_quantity']
                        : $b['total_quantity'] <=> $a['total_quantity'];
                })->values();

                $finalResult = $finalResult->concat($displaySorted);
            }

            $request->session()->put('brandsPerProductTypeFilteredData', $finalResult);

            return response()->json([
                'status' => 200,
                'message' => 'Filtered product-type → brand data.',
                'data' => $finalResult,
            ]);
        } catch (\Exception $e) {
            Log::error('Filter error:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request' => $request->all()
            ]);

            return response()->json([
                'status' => 500,
                'message' => 'An error occurred: ' . $e->getMessage()
            ]);
        }
    }

    protected function applyFilters($collection, $filters)
    {
        return $collection
            ->when(!empty($filters['branch_id']), function ($items) use ($filters) {
                return $items->where('branch_id', $filters['branch_id']);
            })
            ->when(!empty($filters['department_id']), function ($items) use ($filters) {
                return $items->filter(function ($item) use ($filters) {
                    return optional($item->product)->department_id == $filters['department_id'];
                });
            })
            ->when(!empty($filters['product_type_id']), function ($items) use ($filters) {
                return $items->filter(function ($item) use ($filters) {
                    return optional($item->product)->product_type_id == $filters['product_type_id'];
                });
            })
            ->when(!empty($filters['season']), function ($items) use ($filters) {
                return $items->filter(function ($item) use ($filters) {
                    return optional($item->product)->season == $filters['season'];
                });
            })
            ->when(!empty($filters['from_date']), function ($items) use ($filters) {
                return $items->filter(function ($item) use ($filters) {
                    return $item->created_at >= $filters['from_date'];
                });
            })
            ->when(!empty($filters['to_date']), function ($items) use ($filters) {
                return $items->filter(function ($item) use ($filters) {
                    return $item->created_at <= $filters['to_date'];
                });
            })
            ->when(($filters['show_options'] ?? 'all') === 'first' && !empty($filters['first_lines_count']), function ($items) use ($filters) {
                return $items->take($filters['first_lines_count']);
            });
    }
}
