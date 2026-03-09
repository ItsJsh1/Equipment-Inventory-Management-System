@extends('layouts.app')

@section('title', 'Request Disposal')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Request Disposal</h2>
            <p class="text-gray-500 dark:text-gray-400">Submit equipment for disposal</p>
        </div>
        <a href="{{ route('disposals.index') }}" class="btn-secondary flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Back
        </a>
    </div>
    
    <!-- Form -->
    <form method="POST" action="{{ route('disposals.store') }}" class="card p-6 space-y-6">
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
        
        <!-- Disposal Details -->
        <div>
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Disposal Details</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Disposal Date</label>
                    <input type="text" name="disposal_date" value="{{ old('disposal_date') }}" class="input-field @error('disposal_date') border-red-500 @enderror" placeholder="MM/DD/YYYY" onfocus="(this.type='date')" onblur="if(!this.value)this.type='text'">
                    @error('disposal_date')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Recipient Name</label>
                    <input type="text" name="recipient_name" value="{{ old('recipient_name') }}" class="input-field" placeholder="Name of recipient (if applicable)">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Reason for Disposal <span class="text-red-500">*</span></label>
                    <textarea name="reason" rows="3" class="input-field @error('reason') border-red-500 @enderror" placeholder="Explain why this equipment needs to be disposed..." required>{{ old('reason') }}</textarea>
                    @error('reason')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Remarks</label>
                    <textarea name="remarks" rows="2" class="input-field" placeholder="Additional remarks...">{{ old('remarks') }}</textarea>
                </div>
            </div>
        </div>
        
        <!-- Submit -->
        <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-200 dark:border-gray-700">
            <a href="{{ route('disposals.index') }}" class="btn-secondary">Cancel</a>
            <button type="submit" class="btn-primary">Submit Request</button>
        </div>
    </form>
</div>
@endsection
