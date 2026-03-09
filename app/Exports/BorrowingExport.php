<?php

namespace App\Exports;

use App\Models\Borrowing;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class BorrowingExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function collection()
    {
        $query = Borrowing::with(['equipment', 'department']);

        if ($this->request->filled('status')) {
            $query->where('status', $this->request->status);
        }

        if ($this->request->filled('date_from')) {
            $query->whereDate('borrow_date', '>=', $this->request->date_from);
        }

        if ($this->request->filled('date_to')) {
            $query->whereDate('borrow_date', '<=', $this->request->date_to);
        }

        return $query->get();
    }

    public function headings(): array
    {
        return [
            'Borrowing Code',
            'Equipment Code',
            'Equipment Name',
            'Borrower Name',
            'ID Number',
            'Department',
            'Borrow Date',
            'Expected Return',
            'Actual Return',
            'Status',
            'Purpose',
        ];
    }

    public function map($borrowing): array
    {
        return [
            $borrowing->borrowing_code,
            $borrowing->equipment?->equipment_code,
            $borrowing->equipment?->model_name,
            $borrowing->borrower_full_name,
            $borrowing->id_number,
            $borrowing->department?->name,
            $borrowing->borrow_date?->format('Y-m-d'),
            $borrowing->expected_return_date?->format('Y-m-d'),
            $borrowing->actual_return_date?->format('Y-m-d'),
            ucfirst($borrowing->status),
            $borrowing->purpose,
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
