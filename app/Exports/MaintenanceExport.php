<?php

namespace App\Exports;

use App\Models\Maintenance;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class MaintenanceExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function collection()
    {
        $query = Maintenance::with('equipment');

        if ($this->request->filled('type')) {
            $query->where('type', $this->request->type);
        }

        if ($this->request->filled('status')) {
            $query->where('status', $this->request->status);
        }

        if ($this->request->filled('date_from')) {
            $query->whereDate('scheduled_date', '>=', $this->request->date_from);
        }

        if ($this->request->filled('date_to')) {
            $query->whereDate('scheduled_date', '<=', $this->request->date_to);
        }

        return $query->get();
    }

    public function headings(): array
    {
        return [
            'Maintenance Code',
            'Equipment Code',
            'Equipment Name',
            'Type',
            'Title',
            'Scheduled Date',
            'Completion Date',
            'Status',
            'Technician',
            'Cost',
            'Actions Taken',
        ];
    }

    public function map($maintenance): array
    {
        return [
            $maintenance->maintenance_code,
            $maintenance->equipment?->equipment_code,
            $maintenance->equipment?->model_name,
            ucfirst($maintenance->type),
            $maintenance->title,
            $maintenance->scheduled_date?->format('Y-m-d'),
            $maintenance->completion_date?->format('Y-m-d'),
            ucfirst(str_replace('_', ' ', $maintenance->status)),
            $maintenance->technician_name,
            $maintenance->cost,
            $maintenance->actions_taken,
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
