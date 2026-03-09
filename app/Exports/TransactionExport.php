<?php

namespace App\Exports;

use App\Models\Transaction;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class TransactionExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function collection()
    {
        $query = Transaction::with(['equipment', 'department']);

        if ($this->request->filled('type')) {
            $query->where('type', $this->request->type);
        }

        if ($this->request->filled('status')) {
            $query->where('status', $this->request->status);
        }

        if ($this->request->filled('date_from')) {
            $query->whereDate('transaction_date', '>=', $this->request->date_from);
        }

        if ($this->request->filled('date_to')) {
            $query->whereDate('transaction_date', '<=', $this->request->date_to);
        }

        return $query->get();
    }

    public function headings(): array
    {
        return [
            'Transaction Code',
            'Type',
            'Equipment Code',
            'Equipment Name',
            'Person Name',
            'Department',
            'Transaction Date',
            'Status',
            'Purpose',
            'Remarks',
        ];
    }

    public function map($transaction): array
    {
        return [
            $transaction->transaction_code,
            ucfirst($transaction->type),
            $transaction->equipment?->equipment_code,
            $transaction->equipment?->model_name,
            $transaction->person_full_name,
            $transaction->department?->name,
            $transaction->transaction_date?->format('Y-m-d'),
            ucfirst($transaction->status),
            $transaction->purpose,
            $transaction->remarks,
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
