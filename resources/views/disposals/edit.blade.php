@extends('layouts.app')

@section('title', 'Edit Disposal')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Edit Disposal</h2>
            <p class="text-gray-500 dark:text-gray-400">{{ $disposal->disposal_code }}</p>
        </div>
        <a href="{{ route('disposals.index') }}" class="btn-secondary flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Back
        </a>
    </div>
    
    <!-- Form -->
    <form method="POST" action="{{ route('disposals.update', $disposal) }}" class="card p-6 space-y-6">
        @csrf
        @method('PUT')
        
        <!-- Equipment Info -->
        <div>
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Equipment</h3>
            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                <p class="font-medium text-gray-900 dark:text-white">{{ $disposal->equipment->equipment_code }} - {{ $disposal->equipment->model_name }}</p>
                <p class="text-sm text-gray-500 dark:text-gray-400">{{ $disposal->equipment->brand->name }}</p>
            </div>
        </div>
        
        <!-- Disposal Details -->
        <div>
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Disposal Details</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Method <span class="text-red-500">*</span></label>
                    <select name="method" class="input-field @error('method') border-red-500 @enderror" required>
                        <option value="sale" {{ old('method', $disposal->method) == 'sale' ? 'selected' : '' }}>Sale</option>
                        <option value="donation" {{ old('method', $disposal->method) == 'donation' ? 'selected' : '' }}>Donation</option>
                        <option value="recycling" {{ old('method', $disposal->method) == 'recycling' ? 'selected' : '' }}>Recycling</option>
                        <option value="destruction" {{ old('method', $disposal->method) == 'destruction' ? 'selected' : '' }}>Destruction</option>
                        <option value="trade_in" {{ old('method', $disposal->method) == 'trade_in' ? 'selected' : '' }}>Trade-in</option>
                        <option value="other" {{ old('method', $disposal->method) == 'other' ? 'selected' : '' }}>Other</option>
                    </select>
                    @error('method')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Status <span class="text-red-500">*</span></label>
                    <select name="status" class="input-field @error('status') border-red-500 @enderror" required>
                        <option value="pending_approval" {{ old('status', $disposal->status) == 'pending_approval' ? 'selected' : '' }}>Pending Approval</option>
                        <option value="approved" {{ old('status', $disposal->status) == 'approved' ? 'selected' : '' }}>Approved</option>
                        <option value="completed" {{ old('status', $disposal->status) == 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="cancelled" {{ old('status', $disposal->status) == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                    @error('status')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Disposal Value</label>
                    <input type="number" name="disposal_value" value="{{ old('disposal_value', $disposal->disposal_value) }}" step="0.01" min="0" class="input-field" placeholder="0.00">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Disposal Date</label>
                    <input type="{{ old('disposal_date', $disposal->disposal_date) ? 'date' : 'text' }}" name="disposal_date" value="{{ old('disposal_date', $disposal->disposal_date?->format('Y-m-d')) }}" class="input-field" placeholder="MM/DD/YYYY" onfocus="(this.type='date')" onblur="if(!this.value)this.type='text'">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Reason <span class="text-red-500">*</span></label>
                    <textarea name="reason" rows="3" class="input-field @error('reason') border-red-500 @enderror" required>{{ old('reason', $disposal->reason) }}</textarea>
                    @error('reason')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Notes</label>
                    <textarea name="notes" rows="2" class="input-field">{{ old('notes', $disposal->notes) }}</textarea>
                </div>
            </div>
        </div>
        
        <!-- Submit -->
        <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-200 dark:border-gray-700">
            <a href="{{ route('disposals.index') }}" class="btn-secondary">Cancel</a>
            <button type="submit" class="btn-primary">Update Disposal</button>
        </div>
    </form>
</div>
@endsection
