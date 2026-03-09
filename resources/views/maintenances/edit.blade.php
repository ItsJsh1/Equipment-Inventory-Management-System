@extends('layouts.app')

@section('title', 'Edit Maintenance')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Edit Maintenance</h2>
            <p class="text-gray-500 dark:text-gray-400">{{ $maintenance->maintenance_code }}</p>
        </div>
        <a href="{{ route('maintenances.index') }}" class="btn-secondary flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Back
        </a>
    </div>
    
    <!-- Form -->
    <form method="POST" action="{{ route('maintenances.update', $maintenance) }}" class="card p-6 space-y-6">
        @csrf
        @method('PUT')
        
        <!-- Equipment Info -->
        <div>
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Equipment</h3>
            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                <p class="font-medium text-gray-900 dark:text-white">{{ $maintenance->equipment->equipment_code }} - {{ $maintenance->equipment->model_name }}</p>
                <p class="text-sm text-gray-500 dark:text-gray-400">{{ $maintenance->equipment->brand->name }}</p>
            </div>
        </div>
        
        <!-- Maintenance Details -->
        <div>
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Maintenance Details</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Type <span class="text-red-500">*</span></label>
                    <select name="type" class="input-field @error('type') border-red-500 @enderror" required>
                        <option value="preventive" {{ old('type', $maintenance->type) == 'preventive' ? 'selected' : '' }}>Preventive</option>
                        <option value="corrective" {{ old('type', $maintenance->type) == 'corrective' ? 'selected' : '' }}>Corrective</option>
                        <option value="emergency" {{ old('type', $maintenance->type) == 'emergency' ? 'selected' : '' }}>Emergency</option>
                    </select>
                    @error('type')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Status <span class="text-red-500">*</span></label>
                    <select name="status" class="input-field @error('status') border-red-500 @enderror" required>
                        <option value="scheduled" {{ old('status', $maintenance->status) == 'scheduled' ? 'selected' : '' }}>Scheduled</option>
                        <option value="in_progress" {{ old('status', $maintenance->status) == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                        <option value="completed" {{ old('status', $maintenance->status) == 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="cancelled" {{ old('status', $maintenance->status) == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                    @error('status')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Scheduled Date <span class="text-red-500">*</span></label>
                    <input type="date" name="scheduled_date" value="{{ old('scheduled_date', $maintenance->scheduled_date->format('Y-m-d')) }}" class="input-field @error('scheduled_date') border-red-500 @enderror" placeholder="MM/DD/YYYY" required>
                    @error('scheduled_date')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Started Date</label>
                    <input type="{{ old('started_date', $maintenance->started_date) ? 'date' : 'text' }}" name="started_date" value="{{ old('started_date', $maintenance->started_date?->format('Y-m-d')) }}" class="input-field" placeholder="MM/DD/YYYY" onfocus="(this.type='date')" onblur="if(!this.value)this.type='text'">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Completed Date</label>
                    <input type="{{ old('completed_date', $maintenance->completed_date) ? 'date' : 'text' }}" name="completed_date" value="{{ old('completed_date', $maintenance->completed_date?->format('Y-m-d')) }}" class="input-field" placeholder="MM/DD/YYYY" onfocus="(this.type='date')" onblur="if(!this.value)this.type='text'">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Technician Name</label>
                    <input type="text" name="technician_name" value="{{ old('technician_name', $maintenance->technician_name) }}" class="input-field">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Cost</label>
                    <input type="number" name="cost" value="{{ old('cost', $maintenance->cost) }}" step="0.01" min="0" class="input-field" placeholder="0.00">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Description <span class="text-red-500">*</span></label>
                    <textarea name="description" rows="3" class="input-field @error('description') border-red-500 @enderror" required>{{ old('description', $maintenance->description) }}</textarea>
                    @error('description')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Notes</label>
                    <textarea name="notes" rows="2" class="input-field">{{ old('notes', $maintenance->notes) }}</textarea>
                </div>
            </div>
        </div>
        
        <!-- Submit -->
        <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-200 dark:border-gray-700">
            <a href="{{ route('maintenances.index') }}" class="btn-secondary">Cancel</a>
            <button type="submit" class="btn-primary">Update Maintenance</button>
        </div>
    </form>
</div>
@endsection
