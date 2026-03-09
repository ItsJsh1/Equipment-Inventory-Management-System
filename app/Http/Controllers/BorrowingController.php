<?php

namespace App\Http\Controllers;

use App\Models\Borrowing;
use App\Models\Department;
use App\Models\Equipment;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\BorrowingExport;
use Barryvdh\DomPDF\Facade\Pdf;

class BorrowingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Borrowing::with(['equipment', 'department', 'approvedBy']);

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('borrowing_code', 'like', "%{$search}%")
                    ->orWhere('borrower_firstname', 'like', "%{$search}%")
                    ->orWhere('borrower_lastname', 'like', "%{$search}%")
                    ->orWhere('id_number', 'like', "%{$search}%");
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by department
        if ($request->filled('department_id')) {
            $query->where('department_id', $request->department_id);
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('borrow_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('borrow_date', '<=', $request->date_to);
        }

        $borrowings = $query->latest()->paginate(15)->withQueryString();
        $departments = Department::active()->get();
        
        // Equipment status counts
        $borrowedEquipmentCount = Equipment::where('status', 'borrowed')->count();
        $activeBorrowingsCount = Borrowing::where('status', 'borrowed')->count();
        
        // Get borrowed equipment (for display when no borrowing record exists)
        $borrowedEquipment = Equipment::with(['brand', 'category', 'location'])
            ->where('status', 'borrowed')
            ->get();

        return view('borrowings.index', compact('borrowings', 'departments', 'borrowedEquipmentCount', 'activeBorrowingsCount', 'borrowedEquipment'));
    }

    /**
     * Show overdue borrowings.
     */
    public function overdue()
    {
        $borrowings = Borrowing::with(['equipment', 'department'])
            ->where('status', 'borrowed')
            ->where('expected_return_date', '<', now())
            ->latest()
            ->paginate(15);

        $overdue = true;
        $departments = Department::active()->get();
        
        // Equipment status counts
        $borrowedEquipmentCount = Equipment::where('status', 'borrowed')->count();
        $activeBorrowingsCount = Borrowing::where('status', 'borrowed')->count();
        
        // Get borrowed equipment
        $borrowedEquipment = Equipment::with(['brand', 'category', 'location'])
            ->where('status', 'borrowed')
            ->get();

        return view('borrowings.index', compact('borrowings', 'overdue', 'departments', 'borrowedEquipmentCount', 'activeBorrowingsCount', 'borrowedEquipment'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $equipments = Equipment::where('status', 'available')->get();
        $departments = Department::active()->get();

        return view('borrowings.create', compact('equipments', 'departments'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'equipment_id' => 'required|exists:equipments,id',
            'borrower_firstname' => 'required|string|max:255',
            'borrower_lastname' => 'required|string|max:255',
            'borrower_middlename' => 'nullable|string|max:255',
            'department_id' => 'nullable|exists:departments,id',
            'contact_number' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'id_number' => 'nullable|string|max:50',
            'borrow_date' => 'required|date',
            'expected_return_date' => 'required|date|after:borrow_date',
            'purpose' => 'required|string',
            'remarks' => 'nullable|string',
            'condition_on_borrow' => 'required|in:new,good,fair,poor',
        ]);

        // Check if equipment is available
        $equipment = Equipment::findOrFail($validated['equipment_id']);
        if ($equipment->status !== 'available') {
            return back()->with('error', 'Equipment is not available for borrowing.');
        }

        $borrowing = Borrowing::create($validated);

        // Update equipment status
        $equipment->update(['status' => 'borrowed']);

        return redirect()->route('borrowings.index')
            ->with('success', 'Borrowing record created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Borrowing $borrowing)
    {
        $borrowing->load(['equipment', 'department', 'approvedBy', 'receivedBy', 'createdBy']);

        return view('borrowings.show', compact('borrowing'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Borrowing $borrowing)
    {
        $equipments = Equipment::all();
        $departments = Department::active()->get();

        return view('borrowings.edit', compact('borrowing', 'equipments', 'departments'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Borrowing $borrowing)
    {
        $validated = $request->validate([
            'borrower_firstname' => 'required|string|max:255',
            'borrower_lastname' => 'required|string|max:255',
            'borrower_middlename' => 'nullable|string|max:255',
            'department_id' => 'nullable|exists:departments,id',
            'contact_number' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'id_number' => 'nullable|string|max:50',
            'expected_return_date' => 'required|date',
            'purpose' => 'required|string',
            'remarks' => 'nullable|string',
        ]);

        $borrowing->update($validated);

        return redirect()->route('borrowings.index')
            ->with('success', 'Borrowing record updated successfully.');
    }

    /**
     * Process return of borrowed equipment.
     */
    public function processReturn(Request $request, Borrowing $borrowing)
    {
        $validated = $request->validate([
            'actual_return_date' => 'required|date',
            'condition_on_return' => 'required|in:new,good,fair,poor,damaged',
            'remarks' => 'nullable|string',
        ]);

        $borrowing->update([
            'actual_return_date' => $validated['actual_return_date'],
            'condition_on_return' => $validated['condition_on_return'],
            'remarks' => $validated['remarks'] ?? $borrowing->remarks,
            'status' => 'returned',
            'received_by' => auth()->id(),
        ]);

        // Update equipment status and condition
        $borrowing->equipment->update([
            'status' => 'available',
            'condition' => $validated['condition_on_return'],
        ]);

        return redirect()->route('borrowings.index')
            ->with('success', 'Equipment returned successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Borrowing $borrowing)
    {
        // If borrowed, return equipment to available
        if ($borrowing->status === 'borrowed') {
            $borrowing->equipment->update(['status' => 'available']);
        }

        $borrowing->delete();

        return redirect()->route('borrowings.index')
            ->with('success', 'Borrowing record deleted successfully.');
    }

    /**
     * Export borrowings.
     */
    public function export(Request $request)
    {
        $format = $request->get('format', 'xlsx');

        if ($format === 'pdf') {
            $borrowings = Borrowing::with(['equipment', 'department'])->get();
            $pdf = Pdf::loadView('borrowings.export-pdf', compact('borrowings'));
            return $pdf->download('borrowings-list.pdf');
        }

        return Excel::download(new BorrowingExport($request), 'borrowings-list.' . $format);
    }
}
