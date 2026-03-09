<?php

namespace App\Exports;

use App\Models\Equipment;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class EquipmentExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function collection()
    {
        $query = Equipment::with(['brand', 'category', 'location']);

        if ($this->request->filled('status')) {
            $query->where('status', $this->request->status);
        }

        if ($this->request->filled('category_id')) {
            $query->where('category_id', $this->request->category_id);
        }

        if ($this->request->filled('brand_id')) {
            $query->where('brand_id', $this->request->brand_id);
        }

        return $query->get();
    }

    public function headings(): array
    {
        return [
            'Equipment Code',
            'Brand',
            'Category',
            'Model Name',
            'Serial Number',
            'Location',
            'Status',
            'Condition',
            'Acquisition Date',
            'Acquisition Cost',
            'Warranty Expiry',
            'Remarks',
        ];
    }

    public function map($equipment): array
    {
        return [
            $equipment->equipment_code,
            $equipment->brand?->name,
            $equipment->category?->name,
            $equipment->model_name,
            $equipment->serial_number,
            $equipment->location?->name,
            ucfirst(str_replace('_', ' ', $equipment->status)),
            ucfirst($equipment->condition),
            $equipment->acquisition_date?->format('Y-m-d'),
            $equipment->acquisition_cost,
            $equipment->warranty_expiry?->format('Y-m-d'),
            $equipment->remarks,
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
