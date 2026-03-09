<?php

namespace App\Http\Controllers;

use App\Models\Borrowing;
use App\Models\Equipment;
use App\Models\Maintenance;
use App\Models\Transaction;
use App\Models\Disposal;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ReportExport;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportController extends Controller
{
    /**
     * Display the reports page.
     */
    public function index()
    {
        return view('reports.index');
    }

    /**
     * Equipment report.
     */
    public function equipment(Request $request)
    {
        $query = Equipment::with(['brand', 'category', 'location']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('acquisition_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('acquisition_date', '<=', $request->date_to);
        }

        $equipments = $query->get();

        // Summary statistics
        $summary = [
            'total' => $equipments->count(),
            'total_value' => $equipments->sum('acquisition_cost'),
            'by_status' => $equipments->groupBy('status')->map->count(),
            'by_condition' => $equipments->groupBy('condition')->map->count(),
            'by_category' => $equipments->groupBy('category.name')->map->count(),
        ];

        if ($request->has('export')) {
            return $this->exportReport('equipment', $equipments, $summary, $request->get('format', 'pdf'));
        }

        return view('reports.equipment', compact('equipments', 'summary'));
    }

    /**
     * Transaction report.
     */
    public function transactions(Request $request)
    {
        $query = Transaction::with(['equipment', 'department']);

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('transaction_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('transaction_date', '<=', $request->date_to);
        }

        $transactions = $query->get();

        $summary = [
            'total' => $transactions->count(),
            'incoming' => $transactions->where('type', 'incoming')->count(),
            'outgoing' => $transactions->where('type', 'outgoing')->count(),
            'by_status' => $transactions->groupBy('status')->map->count(),
            'by_department' => $transactions->groupBy('department.name')->map->count(),
        ];

        if ($request->has('export')) {
            return $this->exportReport('transactions', $transactions, $summary, $request->get('format', 'pdf'));
        }

        return view('reports.transactions', compact('transactions', 'summary'));
    }

    /**
     * Borrowing report.
     */
    public function borrowings(Request $request)
    {
        $query = Borrowing::with(['equipment', 'department']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('borrow_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('borrow_date', '<=', $request->date_to);
        }

        $borrowings = $query->get();

        $summary = [
            'total' => $borrowings->count(),
            'active' => $borrowings->where('status', 'borrowed')->count(),
            'returned' => $borrowings->where('status', 'returned')->count(),
            'overdue' => $borrowings->where('status', 'borrowed')
                ->where('expected_return_date', '<', now())->count(),
            'by_department' => $borrowings->groupBy('department.name')->map->count(),
        ];

        if ($request->has('export')) {
            return $this->exportReport('borrowings', $borrowings, $summary, $request->get('format', 'pdf'));
        }

        return view('reports.borrowings', compact('borrowings', 'summary'));
    }

    /**
     * Maintenance report.
     */
    public function maintenances(Request $request)
    {
        $query = Maintenance::with('equipment');

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('scheduled_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('scheduled_date', '<=', $request->date_to);
        }

        $maintenances = $query->get();

        $summary = [
            'total' => $maintenances->count(),
            'total_cost' => $maintenances->sum('cost'),
            'by_type' => $maintenances->groupBy('type')->map->count(),
            'by_status' => $maintenances->groupBy('status')->map->count(),
        ];

        if ($request->has('export')) {
            return $this->exportReport('maintenances', $maintenances, $summary, $request->get('format', 'pdf'));
        }

        return view('reports.maintenances', compact('maintenances', 'summary'));
    }

    /**
     * Disposal report.
     */
    public function disposals(Request $request)
    {
        $query = Disposal::with('equipment');

        if ($request->filled('method')) {
            $query->where('method', $request->method);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('disposal_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('disposal_date', '<=', $request->date_to);
        }

        $disposals = $query->get();

        $summary = [
            'total' => $disposals->count(),
            'total_value' => $disposals->sum('disposal_value'),
            'by_method' => $disposals->groupBy('method')->map->count(),
            'by_status' => $disposals->groupBy('status')->map->count(),
        ];

        if ($request->has('export')) {
            return $this->exportReport('disposals', $disposals, $summary, $request->get('format', 'pdf'));
        }

        return view('reports.disposals', compact('disposals', 'summary'));
    }

    /**
     * Export report.
     */
    protected function exportReport(string $type, $data, array $summary, string $format)
    {
        $viewName = "reports.export.{$type}";
        $filename = "{$type}-report-" . now()->format('Y-m-d');

        if ($format === 'pdf') {
            $pdf = Pdf::loadView($viewName, compact('data', 'summary'));
            return $pdf->download("{$filename}.pdf");
        }

        return Excel::download(new ReportExport($type, $data, $summary), "{$filename}.xlsx");
    }
}
