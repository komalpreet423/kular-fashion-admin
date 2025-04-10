<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Support\Collection;

class FilteredDataExport implements FromCollection, WithHeadings
{
    protected $data;

    public function __construct($data)
    {
        $this->data = collect($data);
    }

    public function collection()
    {
        return $this->data->map(function ($item) {
            return [
                'Product Name' => $item['name'],
                'Brand' => $item['brand']['name'] ?? 'N/A',
                'Category' => $item['category']['name'] ?? 'N/A',
                'Created At' => \Carbon\Carbon::parse($item['created_at'])->format('d-m-Y'),
            ];
        });
    }

    public function headings(): array
    {
        return ['Product Name', 'Brand', 'Category', 'Created At'];
    }
}
