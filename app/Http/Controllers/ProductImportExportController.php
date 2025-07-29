<?php

namespace App\Http\Controllers;
use App\Imports\SizeCodeImport;
use App\Imports\ProductsImport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Storage;
use App\Models\Product;
use App\Models\Brand;
use App\Models\Department;
use App\models\ProductTag;
use App\Models\ProductType;
use App\Models\ProductColor;
use App\Models\ProductSize;
use App\Models\ProductQuantity;
use App\Models\ProductTypeDepartment;
use App\Models\Size;
use App\Models\Tag;
use App\Models\SizeScale;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ProductImportExportController extends Controller
{
    // Export products to CSV
    public function exportProductsToCSV()
    {

    }

    public function importProductsFromCSV(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv'
        ]);

        try {
            Excel::queueImport(new ProductsImport, $request->file('file'));

            return back()->with('success', 'Product import started! You will be notified when it completes.');
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            $failures = $e->failures();
            $errorMessages = [];

            foreach ($failures as $failure) {
                $errorMessages[] = "Row {$failure->row()}: " . implode(', ', $failure->errors());
            }

            return back()->withErrors($errorMessages);
        } catch (\Throwable $e) {
            return back()->with('error', 'Error importing products: ' . $e->getMessage());
        }
    }

    public function downloadExcel()
    {
        $spreadsheet = new Spreadsheet();

        $this->addDepartmentSheet($spreadsheet);
        $this->addProductType($spreadsheet);
        $this->addBrand($spreadsheet);
        $this->addSizeScale($spreadsheet);
        $this->addSizes($spreadsheet);
        $this->addTags($spreadsheet);

        $writer = new Xlsx($spreadsheet);
        $fileName = 'Products_configuration' . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $fileName . '"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit;
    }

    protected function addDepartmentSheet($spreadsheet)
    {
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Departments');

        $headers = ['ID', 'Name', 'Slug', 'Description'];
        $sheet->fromArray($headers, NULL, 'A1');

        $departments = Department::where('status', 'Active')->get();

        foreach ($departments as $key => $department) {
            $sheet->fromArray(
                [$department->id, $department->name, $department->slug, $department->description],
                NULL,
                'A' . ($key + 2)
            );
        }

        $spreadsheet->createSheet();
    }

    protected function addProductType($spreadsheet)
    {
        $spreadsheet->setActiveSheetIndex(1);
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Product Types');

        $headers = ['ID', 'Department Id', 'Department', 'Product Type Id', 'Product Name'];
        $sheet->fromArray($headers, NULL, 'A1');

        $productTypeDepartments = ProductTypeDepartment::with(['productTypes' => function($query) {
            $query->where('status', 'Active');
        }, 'departments'])->get();

        foreach ($productTypeDepartments as $key => $productType) {
            $sheet->fromArray(
                [$productType->id, $productType->department_id, $productType->departments->name, $productType->product_type_id, $productType->productTypes->name],
                NULL,
                'A' . ($key + 2)
            );
        }

        $spreadsheet->createSheet();
    }

    protected function addBrand($spreadsheet)
    {
        $spreadsheet->setActiveSheetIndex(2);
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Brands');

        $headers = ['ID', 'Name', 'Slug', 'Description', 'Margin'];
        $sheet->fromArray($headers, NULL, 'A1');

        $brands = Brand::where('status', 'Active')->get();

        foreach ($brands as $key => $brand) {
            $sheet->fromArray(
                [$brand->id, $brand->name, $brand->slug, $brand->description, $brand->margin],
                NULL,
                'A' . ($key + 2)
            );
        }

        $spreadsheet->createSheet();
    }

    protected function addSizeScale($spreadsheet)
    {
        $spreadsheet->setActiveSheetIndex(3);
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Size Scales');

        $headers = ['ID', 'Size Scale'];
        $sheet->fromArray($headers, NULL, 'A1');

        $sizeScales = SizeScale::where('status', 'Active')->get();

        foreach ($sizeScales as $key => $sizeScale) {
            $sheet->fromArray(
                [$sizeScale->id, $sizeScale->name],
                NULL,
                'A' . ($key + 2)
            );
        }

        $spreadsheet->createSheet();
    }

    protected function addSizes($spreadsheet)
    {
        $spreadsheet->setActiveSheetIndex(4);
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Sizes');

        $headers = ['ID', 'Size Scale Id', 'Size', 'New Code', 'Old Code', 'Length'];
        $sheet->fromArray($headers, NULL, 'A1');

        $sizes = Size::where('status', 'Active')->get();

        foreach ($sizes as $key => $size) {
            $sheet->fromArray(
                [$size->id, $size->size_scale_id, $size->size, $size->new_code, $size->old_code],
                NULL,
                'A' . ($key + 2)
            );
        }

        $spreadsheet->createSheet();
    }

    protected function addTags($spreadsheet)
    {
        $spreadsheet->setActiveSheetIndex(5);
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Tags');
    
        $headers = ['ID', 'Tag Name'];
        $sheet->fromArray($headers, NULL, 'A1');
    
        $tags = Tag::where('status', 'Active')->get();
    
        foreach ($tags as $key => $tag) {
            $sheet->fromArray(
                [$tag->id, $tag->name],
                NULL,
                'A' . ($key + 2)
            );
        }
    }

    public function importSizeCodes(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv',
        ]);

        Excel::import(new SizeCodeImport, $request->file('file'));

       // return back()->with('success', 'Size codes updated successfully.');
    }
    public function showSizeCodeImportForm()
    {
        return view('size-import');
    }
}
