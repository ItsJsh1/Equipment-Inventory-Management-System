<?php

namespace App\Http\Controllers;

use App\Models\Borrowing;
use App\Models\Equipment;
use App\Models\Maintenance;
use App\Models\Transaction;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Display the dashboard.
     */
    public function index()
    {
        // Equipment statistics
        $equipmentStats = [
            'total' => Equipment::count(),
            'available' => Equipment::where('status', 'available')->count(),
            'in_use' => Equipment::where('status', 'in_use')->count(),
            'borrowed' => Equipment::where('status', 'borrowed')->count(),
            'maintenance' => Equipment::where('status', 'maintenance')->count(),
            'for_disposal' => Equipment::where('status', 'for_disposal')->count(),
        ];

        // Transaction statistics
        $transactionStats = [
            'total_incoming' => Transaction::where('type', 'incoming')->count(),
            'total_outgoing' => Transaction::where('type', 'outgoing')->count(),
            'pending' => Transaction::where('status', 'pending')->count(),
            'today' => Transaction::whereDate('transaction_date', today())->count(),
        ];

        // Borrowing statistics
        $borrowingStats = [
            'active' => Borrowing::where('status', 'borrowed')->count(),
            'overdue' => Borrowing::where('status', 'borrowed')
                ->where('expected_return_date', '<', now())
                ->count(),
            'returned_today' => Borrowing::whereDate('actual_return_date', today())->count(),
        ];

        // Maintenance statistics
        $maintenanceStats = [
            'scheduled' => Maintenance::where('status', 'scheduled')->count(),
            'in_progress' => Maintenance::where('status', 'in_progress')->count(),
            'upcoming' => Maintenance::where('status', 'scheduled')
                ->whereBetween('scheduled_date', [now(), now()->addDays(7)])
                ->count(),
        ];

        // Recent transactions
        $recentTransactions = Transaction::with(['equipment', 'department'])
            ->latest()
            ->take(5)
            ->get();

        // Overdue borrowings
        $overdueBorrowings = Borrowing::with(['equipment', 'department'])
            ->where('status', 'borrowed')
            ->where('expected_return_date', '<', now())
            ->take(5)
            ->get();

        // Upcoming maintenance
        $upcomingMaintenance = Maintenance::with('equipment')
            ->where('status', 'scheduled')
            ->where('scheduled_date', '>=', now())
            ->orderBy('scheduled_date')
            ->take(5)
            ->get();

        // Equipment by status for chart
        $equipmentByStatus = Equipment::selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        // Monthly transaction data for chart (last 6 months)
        $monthlyTransactions = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $monthlyTransactions[$month->format('M Y')] = [
                'incoming' => Transaction::where('type', 'incoming')
                    ->whereMonth('transaction_date', $month->month)
                    ->whereYear('transaction_date', $month->year)
                    ->count(),
                'outgoing' => Transaction::where('type', 'outgoing')
                    ->whereMonth('transaction_date', $month->month)
                    ->whereYear('transaction_date', $month->year)
                    ->count(),
            ];
        }

        return view('dashboard', compact(
            'equipmentStats',
            'transactionStats',
            'borrowingStats',
            'maintenanceStats',
            'recentTransactions',
            'overdueBorrowings',
            'upcomingMaintenance',
            'equipmentByStatus',
            'monthlyTransactions'
        ));
    }
}
