<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Equipment;
use App\Models\Location;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\EquipmentExport;
use Barryvdh\DomPDF\Facade\Pdf;

class EquipmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Equipment::with(['brand', 'category', 'location']);

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('equipment_code', 'like', "%{$search}%")
                    ->orWhere('model_name', 'like', "%{$search}%")
                    ->orWhere('serial_number', 'like', "%{$search}%");
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by category
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Filter by brand
        if ($request->filled('brand_id')) {
            $query->where('brand_id', $request->brand_id);
        }

        // Filter by condition
        if ($request->filled('condition')) {
            $query->where('condition', $request->condition);
        }

        $equipments = $query->latest()->paginate(15)->withQueryString();
        $brands = Brand::active()->get();
        $categories = Category::active()->get();

        return view('equipment.index', compact('equipments', 'brands', 'categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $brands = Brand::active()->get();
        $categories = Category::active()->get();
        $locations = Location::active()->get();

        return view('equipment.create', compact('brands', 'categories', 'locations'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'brand_id' => 'required|exists:brands,id',
            'category_id' => 'required|exists:categories,id',
            'location_id' => 'nullable|exists:locations,id',
            'model_name' => 'required|string|max:255',
            'serial_number' => 'nullable|string|max:255|unique:equipments',
            'specifications' => 'nullable|string',
            'acquisition_date' => 'nullable|date',
            'acquisition_cost' => 'nullable|numeric|min:0',
            'warranty_expiry' => 'nullable|date',
            'status' => 'nullable|in:available,in_use,borrowed,maintenance,for_disposal,disposed',
            'condition' => 'nullable|in:new,good,fair,poor,damaged',
            'remarks' => 'nullable|string',
            'image' => 'nullable|image|max:2048',
        ]);

        // Set defaults
        $validated['status'] = $validated['status'] ?? 'available';
        $validated['condition'] = $validated['condition'] ?? 'good';

        if ($request->hasFile('image')) {
            $validated['image_path'] = $request->file('image')->store('equipment', 'public');
        }

        Equipment::create($validated);

        return redirect()->route('equipment.index')
            ->with('success', 'Equipment created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Equipment $equipment)
    {
        $equipment->load([
            'brand', 
            'category', 
            'location', 
            'transactions' => fn($q) => $q->latest()->take(10),
            'borrowings' => fn($q) => $q->latest()->take(10),
            'maintenances' => fn($q) => $q->latest()->take(10),
        ]);

        return view('equipment.show', compact('equipment'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Equipment $equipment)
    {
        $brands = Brand::active()->get();
        $categories = Category::active()->get();
        $locations = Location::active()->get();

        return view('equipment.edit', compact('equipment', 'brands', 'categories', 'locations'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Equipment $equipment)
    {
        $validated = $request->validate([
            'brand_id' => 'required|exists:brands,id',
            'category_id' => 'required|exists:categories,id',
            'location_id' => 'nullable|exists:locations,id',
            'model_name' => 'required|string|max:255',
            'serial_number' => 'required|string|max:255|unique:equipments,serial_number,' . $equipment->id,
            'specifications' => 'nullable|string',
            'acquisition_date' => 'nullable|date',
            'acquisition_cost' => 'nullable|numeric|min:0',
            'warranty_expiry' => 'nullable|date',
            'status' => 'required|in:available,in_use,borrowed,maintenance,for_disposal,disposed',
            'condition' => 'required|in:new,good,fair,poor,damaged',
            'remarks' => 'nullable|string',
            'image' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('image')) {
            $validated['image_path'] = $request->file('image')->store('equipment', 'public');
        }

        $equipment->update($validated);

        return redirect()->route('equipment.index')
            ->with('success', 'Equipment updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     * Equipment cannot be deleted directly - it must be disposed through the disposal process.
     */
    public function destroy(Equipment $equipment)
    {
        // Equipment deletion is only allowed from the disposal process
        return back()->with('error', 'Equipment cannot be deleted directly. Please use the disposal process to remove equipment from inventory.');
    }

    /**
     * Export equipment list.
     */
    public function export(Request $request)
    {
        $format = $request->get('format', 'xlsx');

        if ($format === 'pdf') {
            $equipments = Equipment::with(['brand', 'category', 'location'])->get();
            $pdf = Pdf::loadView('equipment.export-pdf', compact('equipments'));
            return $pdf->download('equipment-list.pdf');
        }

        return Excel::download(new EquipmentExport($request), 'equipment-list.' . $format);
    }
}
