@extends('layouts.app')

@section('title', 'Schedule Maintenance')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Schedule Maintenance</h2>
            <p class="text-gray-500 dark:text-gray-400">Create a new maintenance schedule</p>
        </div>
        <a href="{{ route('maintenances.index') }}" class="btn-secondary flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Back
        </a>
    </div>
    
    <!-- Form -->
    <form method="POST" action="{{ route('maintenances.store') }}" class="card p-6 space-y-6">
        @csrf
        
        <!-- Equipment Selection -->
        <div>
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Equipment</h3>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Select Equipment <span class="text-red-500">*</span></label>
                <select name="equipment_id" class="input-field @error('equipment_id') border-red-500 @enderror" required>
                    <option value="">Select equipment</option>
                    @foreach($equipments as $equipment)
                    <option value="{{ $equipment->id }}" {{ old('equipment_id', request('equipment_id')) == $equipment->id ? 'selected' : '' }}>
                        {{ $equipment->equipment_code }} - {{ $equipment->model_name }} ({{ $equipment->brand->name }})
                    </option>
                    @endforeach
                </select>
                @error('equipment_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
        </div>
        
        <!-- Maintenance Details -->
        <div>
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Maintenance Details</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Title <span class="text-red-500">*</span></label>
                    <input type="text" name="title" value="{{ old('title') }}" class="input-field @error('title') border-red-500 @enderror" placeholder="e.g., Routine Inspection, AC Repair" required>
                    @error('title')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Type <span class="text-red-500">*</span></label>
                    <select name="type" class="input-field @error('type') border-red-500 @enderror" required>
                        <option value="preventive" {{ old('type') == 'preventive' ? 'selected' : '' }}>Preventive</option>
                        <option value="corrective" {{ old('type') == 'corrective' ? 'selected' : '' }}>Corrective</option>
                        <option value="emergency" {{ old('type') == 'emergency' ? 'selected' : '' }}>Emergency</option>
                        <option value="inspection" {{ old('type') == 'inspection' ? 'selected' : '' }}>Inspection</option>
                    </select>
                    @error('type')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Scheduled Date <span class="text-red-500">*</span></label>
                    <input type="text" name="scheduled_date" value="{{ old('scheduled_date') }}" class="input-field @error('scheduled_date') border-red-500 @enderror" placeholder="MM/DD/YYYY" required onfocus="(this.type='date')" onblur="if(!this.value)this.type='text'">
                    @error('scheduled_date')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Technician Name</label>
                    <input type="text" name="technician_name" value="{{ old('technician_name') }}" class="input-field">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Estimated Cost</label>
                    <input type="number" name="cost" value="{{ old('cost') }}" step="0.01" min="0" class="input-field" placeholder="0.00">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Description <span class="text-red-500">*</span></label>
                    <textarea name="description" rows="3" class="input-field @error('description') border-red-500 @enderror" placeholder="Describe the maintenance work..." required>{{ old('description') }}</textarea>
                    @error('description')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Remarks</label>
                    <textarea name="remarks" rows="2" class="input-field" placeholder="Additional remarks...">{{ old('remarks') }}</textarea>
                </div>
            </div>
        </div>
        
        <!-- Submit -->
        <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-200 dark:border-gray-700">
            <a href="{{ route('maintenances.index') }}" class="btn-secondary">Cancel</a>
            <button type="submit" class="btn-primary">Schedule Maintenance</button>
        </div>
    </form>
</div>
@endsection
