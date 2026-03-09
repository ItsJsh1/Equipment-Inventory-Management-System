<?php

namespace App\Http\Controllers;

use App\Models\Disposal;
use App\Models\Equipment;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\DisposalExport;
use Barryvdh\DomPDF\Facade\Pdf;

class DisposalController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Disposal::with(['equipment', 'requestedBy', 'approvedBy']);

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('disposal_code', 'like', "%{$search}%")
                    ->orWhere('recipient_name', 'like', "%{$search}%");
            });
        }

        // Filter by method
        if ($request->filled('method')) {
            $query->where('method', $request->method);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $disposals = $query->latest()->paginate(15)->withQueryString();
        
        // Equipment status counts
        $equipmentForDisposalCount = Equipment::where('status', 'for_disposal')->count();
        $equipmentDisposedCount = Equipment::where('status', 'disposed')->count();
        $pendingDisposalsCount = Disposal::where('status', 'pending')->count();
        
        // Get equipment for disposal
        $disposalEquipment = Equipment::with(['brand', 'category', 'location'])
            ->whereIn('status', ['for_disposal', 'disposed'])
            ->get();

        return view('disposals.index', compact('disposals', 'equipmentForDisposalCount', 'equipmentDisposedCount', 'pendingDisposalsCount', 'disposalEquipment'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        // Get equipment that can be disposed (for_disposal, damaged, or specifically selected)
        $query = Equipment::where(function ($q) {
            $q->where('status', 'for_disposal')
              ->orWhere('condition', 'damaged');
        });

        // If equipment_id is passed, include that equipment too
        if ($request->filled('equipment_id')) {
            $selectedEquipmentId = $request->equipment_id;
            $equipments = Equipment::where('id', $selectedEquipmentId)
                ->orWhere(function ($q) {
                    $q->where('status', 'for_disposal')
                      ->orWhere('condition', 'damaged');
                })
                ->whereNotIn('status', ['disposed']) // Exclude already disposed equipment
                ->get();
        } else {
            $equipments = $query->whereNotIn('status', ['disposed'])->get();
        }

        return view('disposals.create', compact('equipments'));
    }

    /**
     * Show the form for bulk disposal
     */
    public function bulkCreate(Request $request)
    {
        $equipmentIds = $request->input('equipment_ids', []);
        
        if (empty($equipmentIds)) {
            return redirect()->route('equipment.index')
                ->with('error', 'Please select equipment to dispose.');
        }

        $equipments = Equipment::whereIn('id', $equipmentIds)
            ->whereNotIn('status', ['disposed'])
            ->get();

        if ($equipments->isEmpty()) {
            return redirect()->route('equipment.index')
                ->with('error', 'No valid equipment selected for disposal.');
        }

        return view('disposals.bulk-create', compact('equipments'));
    }

    /**
     * Store bulk disposal requests
     */
    public function bulkStore(Request $request)
    {
        $validated = $request->validate([
            'equipment_ids' => 'required|array|min:1',
            'equipment_ids.*' => 'exists:equipments,id',
            'method' => 'required|in:sale,donation,recycling,destruction,trade_in,other',
            'reason' => 'required|string',
            'disposal_date' => 'nullable|date',
            'disposal_value' => 'nullable|numeric|min:0',
            'recipient_name' => 'nullable|string|max:255',
            'recipient_contact' => 'nullable|string|max:255',
            'documentation' => 'nullable|string',
            'remarks' => 'nullable|string',
        ]);

        $count = 0;
        foreach ($validated['equipment_ids'] as $equipmentId) {
            $equipment = Equipment::find($equipmentId);
            
            if ($equipment && $equipment->status !== 'disposed') {
                // Mark equipment for disposal
                $equipment->update(['status' => 'for_disposal']);

                Disposal::create([
                    'equipment_id' => $equipmentId,
                    'method' => $validated['method'],
                    'reason' => $validated['reason'],
                    'disposal_date' => $validated['disposal_date'] ?? now(),
                    'disposal_value' => $validated['disposal_value'],
                    'recipient_name' => $validated['recipient_name'],
                    'recipient_contact' => $validated['recipient_contact'],
                    'documentation' => $validated['documentation'],
                    'remarks' => $validated['remarks'],
                    'status' => 'pending_approval',
                    'requested_by' => auth()->id(),
                ]);
                $count++;
            }
        }

        return redirect()->route('disposals.index')
            ->with('success', "{$count} disposal request(s) created successfully.");
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'equipment_id' => 'required|exists:equipments,id',
            'method' => 'required|in:sale,donation,recycling,destruction,trade_in,other',
            'reason' => 'required|string',
            'disposal_date' => 'nullable|date',
            'disposal_value' => 'nullable|numeric|min:0',
            'recipient_name' => 'nullable|string|max:255',
            'recipient_contact' => 'nullable|string|max:255',
            'documentation' => 'nullable|string',
            'remarks' => 'nullable|string',
        ]);

        // Mark equipment for disposal
        $equipment = Equipment::findOrFail($validated['equipment_id']);
        $equipment->update(['status' => 'for_disposal']);

        Disposal::create([
            ...$validated,
            'disposal_date' => $validated['disposal_date'] ?? now(),
            'status' => 'pending_approval',
            'requested_by' => auth()->id(),
        ]);

        return redirect()->route('disposals.index')
            ->with('success', 'Disposal request created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Disposal $disposal)
    {
        $disposal->load(['equipment', 'requestedBy', 'approvedBy', 'createdBy']);

        return view('disposals.show', compact('disposal'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Disposal $disposal)
    {
        $equipments = Equipment::all();

        return view('disposals.edit', compact('disposal', 'equipments'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Disposal $disposal)
    {
        $validated = $request->validate([
            'method' => 'required|in:sale,donation,recycling,destruction,trade_in,other',
            'reason' => 'required|string',
            'disposal_date' => 'required|date',
            'disposal_value' => 'nullable|numeric|min:0',
            'recipient_name' => 'nullable|string|max:255',
            'recipient_contact' => 'nullable|string|max:255',
            'documentation' => 'nullable|string',
            'remarks' => 'nullable|string',
        ]);

        $disposal->update($validated);

        return redirect()->route('disposals.index')
            ->with('success', 'Disposal record updated successfully.');
    }

    /**
     * Approve the disposal.
     */
    public function approve(Disposal $disposal)
    {
        $disposal->update([
            'status' => 'approved',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        return back()->with('success', 'Disposal approved successfully.');
    }

    /**
     * Complete the disposal.
     */
    public function complete(Disposal $disposal)
    {
        $disposal->update(['status' => 'completed']);

        // Update equipment status to disposed
        $disposal->equipment->update(['status' => 'disposed']);

        return back()->with('success', 'Disposal completed successfully.');
    }

    /**
     * Delete equipment permanently after disposal is completed.
     */
    public function deleteEquipment(Disposal $disposal)
    {
        // Only allow deletion of equipment for completed disposals
        if ($disposal->status !== 'completed') {
            return back()->with('error', 'Equipment can only be deleted after disposal is completed.');
        }

        // Store equipment info for message
        $equipmentName = $disposal->equipment ? $disposal->equipment->model_name : 'Unknown';

        // Delete the equipment
        if ($disposal->equipment) {
            $disposal->equipment->delete();
        }

        return redirect()->route('disposals.index')
            ->with('success', "Equipment '{$equipmentName}' has been permanently deleted from the system.");
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Disposal $disposal)
    {
        // If pending, set equipment back to for_disposal or previous status
        if ($disposal->status === 'pending_approval') {
            $disposal->equipment->update(['status' => 'for_disposal']);
        }

        $disposal->delete();

        return redirect()->route('disposals.index')
            ->with('success', 'Disposal record deleted successfully.');
    }

    /**
     * Export disposals.
     */
    public function export(Request $request)
    {
        $format = $request->get('format', 'xlsx');

        if ($format === 'pdf') {
            $disposals = Disposal::with('equipment')->get();
            $pdf = Pdf::loadView('disposals.export-pdf', compact('disposals'));
            return $pdf->download('disposals-list.pdf');
        }

        return Excel::download(new DisposalExport($request), 'disposals-list.' . $format);
    }
}
