<?php

namespace App\Http\Controllers;

use App\Models\Equipment;
use App\Models\Maintenance;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\MaintenanceExport;
use Barryvdh\DomPDF\Facade\Pdf;

class MaintenanceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Maintenance::with(['equipment', 'createdBy']);

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('maintenance_code', 'like', "%{$search}%")
                    ->orWhere('title', 'like', "%{$search}%")
                    ->orWhere('technician_name', 'like', "%{$search}%");
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
            $query->whereDate('scheduled_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('scheduled_date', '<=', $request->date_to);
        }

        $maintenances = $query->latest()->paginate(15)->withQueryString();
        
        // Equipment status counts
        $equipmentInMaintenanceCount = Equipment::where('status', 'maintenance')->count();
        $activeMaintenanceCount = Maintenance::whereIn('status', ['scheduled', 'in_progress'])->count();
        
        // Get equipment under maintenance
        $maintenanceEquipment = Equipment::with(['brand', 'category', 'location'])
            ->where('status', 'maintenance')
            ->get();

        return view('maintenances.index', compact('maintenances', 'equipmentInMaintenanceCount', 'activeMaintenanceCount', 'maintenanceEquipment'));
    }

    /**
     * Show scheduled maintenance.
     */
    public function scheduled()
    {
        $maintenances = Maintenance::with('equipment')
            ->where('status', 'scheduled')
            ->orderBy('scheduled_date')
            ->paginate(15);

        $scheduled = true;
        
        // Equipment status counts
        $equipmentInMaintenanceCount = Equipment::where('status', 'maintenance')->count();
        $activeMaintenanceCount = Maintenance::whereIn('status', ['scheduled', 'in_progress'])->count();
        
        // Get equipment under maintenance
        $maintenanceEquipment = Equipment::with(['brand', 'category', 'location'])
            ->where('status', 'maintenance')
            ->get();

        return view('maintenances.index', compact('maintenances', 'scheduled', 'equipmentInMaintenanceCount', 'activeMaintenanceCount', 'maintenanceEquipment'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $equipments = Equipment::whereIn('status', ['available', 'in_use', 'borrowed'])->get();

        return view('maintenances.create', compact('equipments'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'equipment_id' => 'required|exists:equipments,id',
            'type' => 'required|in:preventive,corrective,emergency,inspection',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'scheduled_date' => 'required|date',
            'technician_name' => 'nullable|string|max:255',
            'vendor_name' => 'nullable|string|max:255',
            'cost' => 'nullable|numeric|min:0',
            'equipment_condition_before' => 'nullable|in:good,fair,poor,damaged',
            'remarks' => 'nullable|string',
        ]);

        $maintenance = Maintenance::create($validated);

        // Update equipment status to maintenance if starting now
        if ($request->boolean('start_now')) {
            $maintenance->update([
                'status' => 'in_progress',
                'start_date' => now(),
            ]);
            $maintenance->equipment->update(['status' => 'maintenance']);
        }

        return redirect()->route('maintenances.index')
            ->with('success', 'Maintenance record created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Maintenance $maintenance)
    {
        $maintenance->load(['equipment', 'createdBy', 'updatedBy']);

        return view('maintenances.show', compact('maintenance'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Maintenance $maintenance)
    {
        $equipments = Equipment::all();

        return view('maintenances.edit', compact('maintenance', 'equipments'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Maintenance $maintenance)
    {
        $validated = $request->validate([
            'type' => 'required|in:preventive,corrective,emergency,inspection',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'issues_found' => 'nullable|string',
            'actions_taken' => 'nullable|string',
            'parts_replaced' => 'nullable|string',
            'scheduled_date' => 'nullable|date',
            'next_maintenance_date' => 'nullable|date',
            'cost' => 'nullable|numeric|min:0',
            'technician_name' => 'nullable|string|max:255',
            'vendor_name' => 'nullable|string|max:255',
            'status' => 'required|in:scheduled,in_progress,completed,cancelled',
            'equipment_condition_before' => 'nullable|in:good,fair,poor,damaged',
            'equipment_condition_after' => 'nullable|in:good,fair,poor,damaged',
            'remarks' => 'nullable|string',
        ]);

        $maintenance->update($validated);

        return redirect()->route('maintenances.index')
            ->with('success', 'Maintenance record updated successfully.');
    }

    /**
     * Start maintenance.
     */
    public function start(Maintenance $maintenance)
    {
        $maintenance->update([
            'status' => 'in_progress',
            'start_date' => now(),
        ]);

        // Update equipment status (if equipment still exists)
        if ($maintenance->equipment) {
            $maintenance->equipment->update(['status' => 'maintenance']);
        }

        return back()->with('success', 'Maintenance started.');
    }

    /**
     * Complete maintenance.
     */
    public function complete(Request $request, Maintenance $maintenance)
    {
        $validated = $request->validate([
            'actions_taken' => 'nullable|string',
            'parts_replaced' => 'nullable|string',
            'cost' => 'nullable|numeric|min:0',
            'equipment_condition_after' => 'nullable|in:good,fair,poor,damaged',
            'next_maintenance_date' => 'nullable|date',
            'remarks' => 'nullable|string',
        ]);

        $maintenance->update([
            'actions_taken' => $validated['actions_taken'] ?? 'Maintenance completed',
            'parts_replaced' => $validated['parts_replaced'] ?? null,
            'cost' => $validated['cost'] ?? null,
            'status' => 'completed',
            'completion_date' => now(),
        ]);

        // Update equipment status and condition (if equipment still exists)
        if ($maintenance->equipment) {
            $maintenance->equipment->update([
                'status' => 'available',
                'condition' => $validated['equipment_condition_after'] ?? $maintenance->equipment->condition,
            ]);
        }

        return redirect()->route('maintenances.index')
            ->with('success', 'Maintenance completed successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Maintenance $maintenance)
    {
        // If maintenance is in progress, set equipment back to available
        if ($maintenance->status === 'in_progress') {
            $maintenance->equipment->update(['status' => 'available']);
        }

        $maintenance->delete();

        return redirect()->route('maintenances.index')
            ->with('success', 'Maintenance record deleted successfully.');
    }

    /**
     * Export maintenances.
     */
    public function export(Request $request)
    {
        $format = $request->get('format', 'xlsx');

        if ($format === 'pdf') {
            $maintenances = Maintenance::with('equipment')->get();
            $pdf = Pdf::loadView('maintenances.export-pdf', compact('maintenances'));
            return $pdf->download('maintenances-list.pdf');
        }

        return Excel::download(new MaintenanceExport($request), 'maintenances-list.' . $format);
    }
}
