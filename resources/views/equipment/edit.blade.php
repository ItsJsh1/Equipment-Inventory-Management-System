@extends('layouts.app')

@section('title', 'Edit Equipment')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Edit Equipment</h2>
            <p class="text-gray-500 dark:text-gray-400">{{ $equipment->equipment_code }}</p>
        </div>
        <a href="{{ route('equipment.show', $equipment) }}" class="btn-secondary flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Back
        </a>
    </div>
    
    <!-- Form -->
    <form method="POST" action="{{ route('equipment.update', $equipment) }}" class="card p-6 space-y-6">
        @csrf
        @method('PUT')
        
        <!-- Basic Information -->
        <div>
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Basic Information</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Equipment Code</label>
                    <input type="text" value="{{ $equipment->equipment_code }}" class="input-field bg-gray-100 dark:bg-gray-700" readonly>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Status</label>
                    <select name="status" class="input-field">
                        <option value="available" {{ $equipment->status == 'available' ? 'selected' : '' }}>Available</option>
                        <option value="in_use" {{ $equipment->status == 'in_use' ? 'selected' : '' }}>In Use</option>
                        <option value="borrowed" {{ $equipment->status == 'borrowed' ? 'selected' : '' }}>Borrowed</option>
                        <option value="maintenance" {{ $equipment->status == 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                        <option value="for_disposal" {{ $equipment->status == 'for_disposal' ? 'selected' : '' }}>For Disposal</option>
                        <option value="disposed" {{ $equipment->status == 'disposed' ? 'selected' : '' }}>Disposed</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Model Name <span class="text-red-500">*</span></label>
                    <input type="text" name="model_name" value="{{ old('model_name', $equipment->model_name) }}" class="input-field @error('model_name') border-red-500 @enderror" required>
                    @error('model_name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Serial Number <span class="text-red-500">*</span></label>
                    <input type="text" name="serial_number" value="{{ old('serial_number', $equipment->serial_number) }}" class="input-field @error('serial_number') border-red-500 @enderror" required>
                    @error('serial_number')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Brand <span class="text-red-500">*</span></label>
                    <select name="brand_id" class="input-field @error('brand_id') border-red-500 @enderror" required>
                        <option value="">Select Brand</option>
                        @foreach($brands as $brand)
                        <option value="{{ $brand->id }}" {{ old('brand_id', $equipment->brand_id) == $brand->id ? 'selected' : '' }}>{{ $brand->name }}</option>
                        @endforeach
                    </select>
                    @error('brand_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Category <span class="text-red-500">*</span></label>
                    <select name="category_id" class="input-field @error('category_id') border-red-500 @enderror" required>
                        <option value="">Select Category</option>
                        @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ old('category_id', $equipment->category_id) == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                        @endforeach
                    </select>
                    @error('category_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
            </div>
        </div>
        
        <!-- Additional Details -->
        <div>
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Additional Details</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Location</label>
                    <select name="location_id" class="input-field">
                        <option value="">Select Location</option>
                        @foreach($locations as $location)
                        <option value="{{ $location->id }}" {{ old('location_id', $equipment->location_id) == $location->id ? 'selected' : '' }}>{{ $location->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Condition <span class="text-red-500">*</span></label>
                    <select name="condition" class="input-field" required>
                        <option value="good" {{ old('condition', $equipment->condition) == 'good' ? 'selected' : '' }}>Good</option>
                        <option value="fair" {{ old('condition', $equipment->condition) == 'fair' ? 'selected' : '' }}>Fair</option>
                        <option value="poor" {{ old('condition', $equipment->condition) == 'poor' ? 'selected' : '' }}>Poor</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Acquisition Date</label>
                    <input type="{{ old('acquisition_date', $equipment->acquisition_date) ? 'date' : 'text' }}" name="acquisition_date" value="{{ old('acquisition_date', $equipment->acquisition_date?->format('Y-m-d')) }}" class="input-field" placeholder="MM/DD/YYYY" onfocus="(this.type='date')" onblur="if(!this.value)this.type='text'">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Acquisition Cost</label>
                    <input type="number" name="acquisition_cost" value="{{ old('acquisition_cost', $equipment->acquisition_cost) }}" step="0.01" min="0" class="input-field">
                </div>
                <div x-data="{ hasWarranty: {{ $equipment->warranty_expiry ? 'true' : (old('has_warranty') ? 'true' : 'false') }} }">
                    <div class="flex items-center gap-2 mb-2">
                        <input type="checkbox" name="has_warranty" id="has_warranty" x-model="hasWarranty" class="rounded border-gray-300 dark:border-gray-600 text-blue-600 focus:ring-blue-500 dark:bg-gray-700" value="1" {{ $equipment->warranty_expiry || old('has_warranty') ? 'checked' : '' }}>
                        <label for="has_warranty" class="text-sm font-medium text-gray-700 dark:text-gray-300">Has Warranty</label>
                    </div>
                    <div x-show="hasWarranty" x-transition>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Warranty Expiry</label>
                        <input type="{{ old('warranty_expiry', $equipment->warranty_expiry) ? 'date' : 'text' }}" name="warranty_expiry" value="{{ old('warranty_expiry', $equipment->warranty_expiry?->format('Y-m-d')) }}" class="input-field" placeholder="MM/DD/YYYY" onfocus="(this.type='date')" onblur="if(!this.value)this.type='text'">
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Specifications -->
        <div>
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Specifications</h3>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Specifications</label>
                <textarea name="specifications" rows="4" class="input-field">{{ old('specifications', $equipment->specifications) }}</textarea>
            </div>
        </div>
        
        <!-- Remarks -->
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Remarks</label>
            <textarea name="remarks" rows="3" class="input-field">{{ old('remarks', $equipment->remarks) }}</textarea>
        </div>
        
        <!-- Submit -->
        <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-200 dark:border-gray-700">
            <a href="{{ route('equipment.show', $equipment) }}" class="btn-secondary">Cancel</a>
            <button type="submit" class="btn-primary">Update Equipment</button>
        </div>
    </form>
</div>
@endsection
