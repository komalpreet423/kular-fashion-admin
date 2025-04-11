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

class BestBrandOverallController extends Controller
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

    $query = OrderItem::selectRaw('brand_id, SUM(quantity) as total_quantity, SUM(quantity * changed_price) as sales_value')
                        ->groupBy('brand_id')
                        ->with('brand');

    $results = $this->applyFilters($query, $validatedData);

    // Sort: first by total_quantity DESC, then by sales_value DESC
    $sorted = $results->sort(function ($a, $b) {
        if ($a->total_quantity === $b->total_quantity) {
            return $b->sales_value <=> $a->sales_value;
        }
        return $b->total_quantity <=> $a->total_quantity;
    })->values();

    $filteredData = $sorted->map(function ($item, $index) {
        $rank = $index + 1;
        $suffix = match (true) {
            $rank % 100 >= 11 && $rank % 100 <= 13 => 'th',
            $rank % 10 === 1 => 'st',
            $rank % 10 === 2 => 'nd',
            $rank % 10 === 3 => 'rd',
            default => 'th'
        };

        return [
            'rank' => $rank . $suffix,
            'brand_id' => $item->brand_id,
            'brand_name' => optional($item->brand)->name,
            'total_quantity' => $item->total_quantity,
            'sales_value' => number_format($item->sales_value, 2),
        ];
    });

    $request->session()->put('filteredData', $filteredData);

    return view('best-brands-overall.index', compact(
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
                'from_date' => 'nullable',
                'to_date' => 'nullable',
                'sequence' => 'nullable',
                'show_options' => 'nullable',
                'first_lines_count' => 'nullable'
            ]);
    
            $query = OrderItem::selectRaw('brand_id, SUM(quantity) as total_quantity, SUM(quantity * changed_price) as sales_value')
                                ->groupBy('brand_id')
                                ->with('brand');
    
            $results = $this->applyFilters($query, $validatedData);
    
            $direction = ($validatedData['sequence'] ?? 'descending') === 'ascending' ? 'asc' : 'desc';
            $sorted = $direction === 'asc' ? $results->sortBy('total_quantity') : $results->sortByDesc('total_quantity');
    
            $total = $sorted->count();
    
            $filteredData = $sorted->values()->map(function ($item, $index) use ($direction, $total) {
                $rank = $direction === 'desc' ? $index + 1 : $total - $index;
                $suffix = match (true) {
                    $rank % 100 >= 11 && $rank % 100 <= 13 => 'TH',
                    $rank % 10 === 1 => 'ST',
                    $rank % 10 === 2 => 'ND',
                    $rank % 10 === 3 => 'RD',
                    default => 'TH'
                };
    
                return [
                    'rank' => $rank . $suffix,
                    'brand_id' => $item['brand_id'],
                    'brand_name' => optional($item->brand)->name,
                    'total_quantity' => $item['total_quantity'],
                    'sales_value' => number_format($item['sales_value'], 2),
                ];
            });
            
    
            if ($filteredData->isEmpty()) {
                Log::warning('No results found for filters:', $validatedData);
                
                return response()->json([
                    'status' => 200,
                    'message' => 'No products found matching all your criteria. Try broadening your search.',
                    'data' => [],
                ]);
            }
    
            $request->session()->put('filteredData', $filteredData);

            return response()->json([
                'status' => 200,
                'message' => 'Best Brand Overall data.',
                'data' => $filteredData,
            ]);
        } catch (\Exception $e) {
            Log::error('Filter error:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request' => $request->all()
            ]);
            return back()
                ->with('error', 'An error occurred: ' . $e->getMessage())
                ->withInput();
        }
    }
    

    protected function applyFilters($query, $filters)
    {
        return $query
        ->when(!empty($filters['branch_id']), function ($q) use ($filters) {
            $q->where('branch_id', $filters['branch_id']);
        })
        ->when(!empty($filters['department_id']), function ($q) use ($filters) {
            $q->whereHas('product', fn($sub) => $sub->where('department_id', $filters['department_id']));
        })
        ->when(!empty($filters['product_type_id']), function ($q) use ($filters) {
            $q->whereHas('product', fn($sub) => $sub->where('product_type_id', $filters['product_type_id']));
        })
        ->when(!empty($filters['season']), function ($q) use ($filters) {
            $q->whereHas('product', fn($sub) => $sub->where('season', $filters['season']));
        })
        ->when(!empty($filters['from_date']), function ($q) use ($filters) {
            $q->whereDate('created_at', '>=', $filters['from_date']);
        })
        ->when(!empty($filters['to_date']), function ($q) use ($filters) {
            $q->whereDate('created_at', '<=', $filters['to_date']);
        })
        ->orderBy('sales_value', ($filters['sequence'] ?? 'descending') === 'ascending' ? 'asc' : 'desc')
        ->when(($filters['show_options'] ?? 'all') === 'first' && !empty($filters['first_lines_count']), function ($q) use ($filters) {
            $q->limit($filters['first_lines_count']);
        })
        ->get();
    }

    /**
     * Export filtered data to PDF or CSV.
     */
    public function exportData(Request $request, $type)
    {
        try {
            $filteredData = $request->session()->get('filteredData');

            if (!$filteredData || $filteredData->isEmpty()) {
                return redirect()->route('best-brands-overall.index')
                    ->with('error', 'No data available for export. Please perform a new search.');
            }

            $filename = 'best_brands_' . now()->format('Ymd_His');

            if ($type === 'csv') {
                return $this->exportCSV($filteredData, $filename);
            } elseif ($type === 'pdf') {
                return $this->exportPDF($filteredData, $filename);
            }

            return back()->with('error', 'Invalid export type specified.');
        } catch (\Exception $e) {
            Log::error('Export error:', ['error' => $e->getMessage()]);
            return back()->with('error', 'Export failed: ' . $e->getMessage());
        }
    }

    /**
     * Export to CSV
     */
    private function exportCSV($data, $filename)
    {
        $filename .= '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=$filename",
        ];

        $callback = function () use ($data) {
            $file = fopen('php://output', 'w');

            fputcsv($file, [
                'ID',
                'Brand',
                'Department',
                'Product Type',
                'Category',
                'Season',
                'Created At',
                'Updated At'
            ]);

            foreach ($data as $product) {
                fputcsv($file, [
                    $product->id,
                    $product->brand->name ?? 'N/A',
                    $product->department->name ?? 'N/A',
                    $product->productType->name ?? 'N/A',
                    $product->category->name ?? 'N/A',
                    $product->season,
                    $product->created_at->format('d-m-Y H:i'),
                    $product->updated_at->format('d-m-Y H:i')
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Export to PDF
     */
    private function exportPDF($data, $filename)
    {
        $filename .= '.pdf';
        $pdf = Pdf::loadView('exports.best-brands', [
            'products' => $data,
            'title' => 'Best Brands Report'
        ]);

        return $pdf->download($filename);
    }
}
