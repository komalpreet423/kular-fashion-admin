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

class WeekelyTurnoverController extends Controller
{
    /**
     * Display the filter form.
     */
    public function index(Request $request)
    {
        try {
            $brands = Brand::all();
            $branches = Branch::all();
            $departments = Department::all();
            $productTypes = ProductType::all();
            $categories = Category::all();

            // Load OrderItems with relationships
            $orderItems = OrderItem::with(['branch'])->get();

            // Apply filters if needed
            $filteredItems = $this->applyFilters($orderItems, []);

            // Group by branch_id
            $groupedByBranch = $filteredItems->groupBy('branch_id');

            // Summarize per branch
            $finalResult = $groupedByBranch->map(function ($items, $branchId) {
                $branch = optional($items->first()->branch);
                return [
                    'branch_name' => $branch?->name ?? 'Unknown Branch',
                    'total_quantity' => $items->sum('quantity'),
                    'sales_value' => number_format($items->sum(fn($item) => $item->quantity * $item->changed_price), 2),
                ];
            })->values();

            // Sorting logic
            $sequence = strtolower($request->get('sequence', 'descending'));
            $sortedResult = $finalResult->sortBy('total_quantity', SORT_REGULAR, $sequence === 'descending')->values();

            $filteredData = $sortedResult;

            // Store in session
            $request->session()->put('weekelyTurnoverFilterData', $filteredData);

            return view('weekely-turnover.index', compact(
                'brands',
                'branches',
                'departments',
                'productTypes',
                'categories',
                'filteredData'
            ));

        } catch (\Exception $e) {
            Log::error('Error in branch summary:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
    
            return back()->with('error', 'An error occurred while generating the report.');
        }
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

            // Load OrderItems with related branch
            $orderItems = OrderItem::with('branch')->get();

            // Apply filters
            $filteredItems = $this->applyFilters($orderItems, $validatedData);

            // Group by branch
            $groupedByBranch = $filteredItems->groupBy('branch_id');

            // Summarize per branch
            $result = $groupedByBranch->map(function ($items, $branchId) {
                $branchName = optional($items->first()->branch)->name ?? 'Unknown Branch';
                return [
                    'branch_name' => $branchName,
                    'total_quantity' => $items->sum('quantity'),
                    'sales_value' => number_format($items->sum(fn($item) => $item->quantity * $item->changed_price), 2),
                ];
            })->values();

            // Sort the result by total_quantity
            $sequence = strtolower($validatedData['sequence'] ?? 'descending');
            $sortedResult = $result->sortBy('total_quantity', SORT_REGULAR, $sequence === 'descending')->values();

            // Store in session
            $request->session()->put('weekelyTurnoverFilterData', $sortedResult);

            return response()->json([
                'status' => 200,
                'message' => 'Filtered branch-wise summary.',
                'data' => $sortedResult,
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
