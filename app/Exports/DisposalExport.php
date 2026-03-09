<?php

namespace App\Exports;

use App\Models\Disposal;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class DisposalExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function collection()
    {
        $query = Disposal::with('equipment');

        if ($this->request->filled('method')) {
            $query->where('method', $this->request->method);
        }

        if ($this->request->filled('status')) {
            $query->where('status', $this->request->status);
        }

        if ($this->request->filled('date_from')) {
            $query->whereDate('disposal_date', '>=', $this->request->date_from);
        }

        if ($this->request->filled('date_to')) {
            $query->whereDate('disposal_date', '<=', $this->request->date_to);
        }

        return $query->get();
    }

    public function headings(): array
    {
        return [
            'Disposal Code',
            'Equipment Code',
            'Equipment Name',
            'Method',
            'Disposal Date',
            'Status',
            'Disposal Value',
            'Recipient',
            'Reason',
        ];
    }

    public function map($disposal): array
    {
        return [
            $disposal->disposal_code,
            $disposal->equipment?->equipment_code,
            $disposal->equipment?->model_name,
            ucfirst(str_replace('_', ' ', $disposal->method)),
            $disposal->disposal_date?->format('Y-m-d'),
            ucfirst(str_replace('_', ' ', $disposal->status)),
            $disposal->disposal_value,
            $disposal->recipient_name,
            $disposal->reason,
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
