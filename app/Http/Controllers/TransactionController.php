<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Department;
use App\Models\Equipment;
use App\Models\Location;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\TransactionExport;
use Barryvdh\DomPDF\Facade\Pdf;

class TransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Transaction::with(['equipment', 'department', 'processedBy']);

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('transaction_code', 'like', "%{$search}%")
                    ->orWhere('person_firstname', 'like', "%{$search}%")
                    ->orWhere('person_lastname', 'like', "%{$search}%");
            });
        }

        // Filter by type
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('transaction_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('transaction_date', '<=', $request->date_to);
        }

        $transactions = $query->latest()->paginate(15)->withQueryString();
        $departments = Department::active()->get();

        return view('transactions.index', compact('transactions', 'departments'));
    }

    /**
     * Show incoming transactions.
     */
    public function incoming(Request $request)
    {
        $query = Transaction::with(['equipment', 'department'])
            ->where('type', 'incoming');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('transaction_code', 'like', "%{$search}%")
                    ->orWhere('person_firstname', 'like', "%{$search}%")
                    ->orWhere('person_lastname', 'like', "%{$search}%");
            });
        }

        $transactions = $query->latest()->paginate(15)->withQueryString();
        $departments = Department::active()->get();
        $type = 'incoming';

        return view('transactions.index', compact('transactions', 'departments', 'type'));
    }

    /**
     * Show outgoing transactions.
     */
    public function outgoing(Request $request)
    {
        $query = Transaction::with(['equipment', 'department'])
            ->where('type', 'outgoing');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('transaction_code', 'like', "%{$search}%")
                    ->orWhere('person_firstname', 'like', "%{$search}%")
                    ->orWhere('person_lastname', 'like', "%{$search}%");
            });
        }

        $transactions = $query->latest()->paginate(15)->withQueryString();
        $departments = Department::active()->get();
        $type = 'outgoing';

        return view('transactions.index', compact('transactions', 'departments', 'type'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $type = $request->get('type', 'incoming');
        
        // For outgoing: show available equipment
        // For incoming: show all equipment (for linking to existing)
        if ($type === 'outgoing') {
            $equipments = Equipment::where('status', 'available')->get();
        } else {
            // For incoming, show all equipment that can be linked
            $equipments = Equipment::whereNotIn('status', ['disposed'])->get();
        }
        
        $departments = Department::active()->get();
        $brands = Brand::active()->get();
        $categories = Category::active()->get();
        $locations = Location::active()->get();

        return view('transactions.create', compact('equipments', 'departments', 'type', 'brands', 'categories', 'locations'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $type = $request->input('type', 'incoming');
        
        // Different validation rules based on transaction type
        if ($type === 'incoming' && $request->has('create_equipment')) {
            // Validate for creating new equipment
            $validated = $request->validate([
                'type' => 'required|in:incoming,outgoing,borrow,return,transfer',
                'model_name' => 'required|string|max:255',
                'serial_number' => 'required|string|max:255|unique:equipments,serial_number',
                'brand_id' => 'required|exists:brands,id',
                'category_id' => 'required|exists:categories,id',
                'location_id' => 'nullable|exists:locations,id',
                'condition' => 'nullable|in:good,fair,poor',
                'person_name' => 'required|string|max:255',
                'person_department' => 'nullable|string|max:255',
                'person_contact' => 'nullable|string|max:20',
                'person_email' => 'nullable|email|max:255',
                'transaction_date' => 'required|date',
                'reference_number' => 'nullable|string|max:255',
                'purpose' => 'nullable|string',
                'notes' => 'nullable|string',
            ]);
            
            // Create the equipment first
            $equipment = Equipment::create([
                'model_name' => $validated['model_name'],
                'serial_number' => $validated['serial_number'],
                'brand_id' => $validated['brand_id'],
                'category_id' => $validated['category_id'],
                'location_id' => $validated['location_id'] ?? null,
                'condition' => $validated['condition'] ?? 'good',
                'status' => 'available',
                'acquisition_date' => $validated['transaction_date'],
            ]);
            
            $equipmentId = $equipment->id;
        } else {
            // Validate for existing equipment
            $validated = $request->validate([
                'equipment_id' => 'required|exists:equipments,id',
                'type' => 'required|in:incoming,outgoing,borrow,return,transfer',
                'person_name' => 'required|string|max:255',
                'person_department' => 'nullable|string|max:255',
                'person_contact' => 'nullable|string|max:20',
                'person_email' => 'nullable|email|max:255',
                'transaction_date' => 'required|date',
                'reference_number' => 'nullable|string|max:255',
                'purpose' => 'nullable|string',
                'notes' => 'nullable|string',
            ]);
            
            $equipmentId = $validated['equipment_id'];
        }
        
        // Parse person name into first/last name
        $nameParts = explode(' ', $validated['person_name'], 2);
        $firstName = $nameParts[0];
        $lastName = $nameParts[1] ?? '';
        
        // Create the transaction
        $transaction = Transaction::create([
            'equipment_id' => $equipmentId,
            'type' => $validated['type'],
            'person_firstname' => $firstName,
            'person_lastname' => $lastName,
            'contact_number' => $validated['person_contact'] ?? null,
            'email' => $validated['person_email'] ?? null,
            'transaction_date' => $validated['transaction_date'],
            'purpose' => $validated['purpose'] ?? null,
            'remarks' => $validated['notes'] ?? null,
            'status' => 'completed',
            'processed_by' => auth()->id(),
        ]);

        // Update equipment status based on transaction type
        $equipment = Equipment::find($equipmentId);
        if ($validated['type'] === 'incoming') {
            $equipment->update(['status' => 'available']);
        } elseif ($validated['type'] === 'outgoing') {
            $equipment->update(['status' => 'in_use']);
        }

        return redirect()->route('transactions.index')
            ->with('success', 'Transaction created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Transaction $transaction)
    {
        $transaction->load(['equipment', 'department', 'processedBy', 'createdBy']);

        return view('transactions.show', compact('transaction'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Transaction $transaction)
    {
        $equipments = Equipment::all();
        $departments = Department::active()->get();

        return view('transactions.edit', compact('transaction', 'equipments', 'departments'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Transaction $transaction)
    {
        $validated = $request->validate([
            'equipment_id' => 'required|exists:equipments,id',
            'type' => 'required|in:incoming,outgoing,borrow,return,transfer',
            'person_firstname' => 'required|string|max:255',
            'person_lastname' => 'required|string|max:255',
            'person_middlename' => 'nullable|string|max:255',
            'department_id' => 'nullable|exists:departments,id',
            'contact_number' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'transaction_date' => 'required|date',
            'expected_return_date' => 'nullable|date|after:transaction_date',
            'purpose' => 'nullable|string',
            'remarks' => 'nullable|string',
            'status' => 'required|in:pending,approved,completed,cancelled,overdue',
        ]);

        $transaction->update($validated);

        return redirect()->route('transactions.index')
            ->with('success', 'Transaction updated successfully.');
    }

    /**
     * Approve the transaction.
     */
    public function approve(Transaction $transaction)
    {
        $transaction->update([
            'status' => 'approved',
            'processed_by' => auth()->id(),
            'processed_at' => now(),
        ]);

        return back()->with('success', 'Transaction approved successfully.');
    }

    /**
     * Complete the transaction.
     */
    public function complete(Transaction $transaction)
    {
        $transaction->update([
            'status' => 'completed',
            'processed_by' => auth()->id(),
            'processed_at' => now(),
        ]);

        return back()->with('success', 'Transaction completed successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Transaction $transaction)
    {
        $transaction->delete();

        return redirect()->route('transactions.index')
            ->with('success', 'Transaction deleted successfully.');
    }

    /**
     * Export transactions.
     */
    public function export(Request $request)
    {
        $format = $request->get('format', 'xlsx');

        if ($format === 'pdf') {
            $transactions = Transaction::with(['equipment', 'department'])->get();
            $pdf = Pdf::loadView('transactions.export-pdf', compact('transactions'));
            return $pdf->download('transactions-list.pdf');
        }

        return Excel::download(new TransactionExport($request), 'transactions-list.' . $format);
    }
}
