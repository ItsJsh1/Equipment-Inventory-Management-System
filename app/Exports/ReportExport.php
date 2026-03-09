<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Illuminate\Support\Collection;

class ReportExport implements FromCollection, WithHeadings, WithTitle, WithStyles
{
    protected $type;
    protected $data;
    protected $summary;

    public function __construct(string $type, $data, array $summary)
    {
        $this->type = $type;
        $this->data = $data;
        $this->summary = $summary;
    }

    public function collection(): Collection
    {
        return $this->data->map(function ($item) {
            return $this->mapItem($item);
        });
    }

    protected function mapItem($item): array
    {
        return match ($this->type) {
            'equipment' => [
                $item->equipment_code,
                $item->brand?->name,
                $item->category?->name,
                $item->model_name,
                $item->serial_number,
                ucfirst(str_replace('_', ' ', $item->status)),
                ucfirst($item->condition),
                $item->acquisition_cost,
            ],
            'transactions' => [
                $item->transaction_code,
                ucfirst($item->type),
                $item->equipment?->equipment_code,
                $item->person_full_name,
                $item->department?->name,
                $item->transaction_date?->format('Y-m-d'),
                ucfirst($item->status),
            ],
            'borrowings' => [
                $item->borrowing_code,
                $item->equipment?->equipment_code,
                $item->borrower_full_name,
                $item->department?->name,
                $item->borrow_date?->format('Y-m-d'),
                $item->expected_return_date?->format('Y-m-d'),
                ucfirst($item->status),
            ],
            'maintenances' => [
                $item->maintenance_code,
                $item->equipment?->equipment_code,
                ucfirst($item->type),
                $item->title,
                $item->scheduled_date?->format('Y-m-d'),
                ucfirst(str_replace('_', ' ', $item->status)),
                $item->cost,
            ],
            'disposals' => [
                $item->disposal_code,
                $item->equipment?->equipment_code,
                ucfirst(str_replace('_', ' ', $item->method)),
                $item->disposal_date?->format('Y-m-d'),
                ucfirst(str_replace('_', ' ', $item->status)),
                $item->disposal_value,
            ],
            default => [],
        };
    }

    public function headings(): array
    {
        return match ($this->type) {
            'equipment' => ['Code', 'Brand', 'Category', 'Model', 'Serial Number', 'Status', 'Condition', 'Cost'],
            'transactions' => ['Code', 'Type', 'Equipment', 'Person', 'Department', 'Date', 'Status'],
            'borrowings' => ['Code', 'Equipment', 'Borrower', 'Department', 'Borrow Date', 'Return Date', 'Status'],
            'maintenances' => ['Code', 'Equipment', 'Type', 'Title', 'Scheduled Date', 'Status', 'Cost'],
            'disposals' => ['Code', 'Equipment', 'Method', 'Date', 'Status', 'Value'],
            default => [],
        };
    }

    public function title(): string
    {
        return ucfirst($this->type) . ' Report';
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
