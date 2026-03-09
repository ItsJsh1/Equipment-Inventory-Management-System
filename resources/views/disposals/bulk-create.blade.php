@extends('layouts.app')

@section('title', 'Bulk Disposal Request')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Bulk Disposal Request</h2>
            <p class="text-gray-500 dark:text-gray-400">Submit multiple equipment for disposal</p>
        </div>
        <a href="{{ route('equipment.index') }}" class="btn-secondary flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Back
        </a>
    </div>
    
    <!-- Form -->
    <form method="POST" action="{{ route('disposals.bulk-store') }}" class="card p-6 space-y-6">
        @csrf
        
        <!-- Selected Equipment -->
        <div>
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Selected Equipment ({{ $equipments->count() }})</h3>
            <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4 max-h-64 overflow-y-auto">
                <table class="w-full text-sm">
                    <thead class="text-xs text-gray-500 dark:text-gray-400 uppercase">
                        <tr>
                            <th class="text-left py-2">Code</th>
                            <th class="text-left py-2">Equipment</th>
                            <th class="text-left py-2">Brand</th>
                            <th class="text-left py-2">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-600">
                        @foreach($equipments as $equipment)
                        <tr>
                            <td class="py-2">
                                <input type="hidden" name="equipment_ids[]" value="{{ $equipment->id }}">
                                <span class="font-medium text-gray-900 dark:text-white">{{ $equipment->equipment_code }}</span>
                            </td>
                            <td class="py-2 text-gray-600 dark:text-gray-300">{{ $equipment->model_name }}</td>
                            <td class="py-2 text-gray-600 dark:text-gray-300">{{ $equipment->brand->name }}</td>
                            <td class="py-2">
                                <span class="px-2 py-1 text-xs font-medium rounded-full {{ $equipment->status_badge }}">
                                    {{ ucfirst(str_replace('_', ' ', $equipment->status)) }}
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @error('equipment_ids')<p class="text-red-500 text-xs mt-2">{{ $message }}</p>@enderror
        </div>
        
        <!-- Disposal Details -->
        <div>
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Disposal Details</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">These details will apply to all selected equipment.</p>
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
                    <textarea name="reason" rows="3" class="input-field @error('reason') border-red-500 @enderror" placeholder="Explain why these equipment need to be disposed..." required>{{ old('reason') }}</textarea>
                    @error('reason')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Remarks</label>
                    <textarea name="remarks" rows="2" class="input-field" placeholder="Additional remarks...">{{ old('remarks') }}</textarea>
                </div>
            </div>
        </div>
        
        <!-- Warning -->
        <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg p-4">
            <div class="flex items-start gap-3">
                <svg class="w-5 h-5 text-yellow-600 dark:text-yellow-500 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
                <div>
                    <h4 class="text-sm font-medium text-yellow-800 dark:text-yellow-200">Confirm Bulk Disposal</h4>
                    <p class="text-sm text-yellow-700 dark:text-yellow-300 mt-1">
                        You are about to create disposal requests for <strong>{{ $equipments->count() }}</strong> equipment items. 
                        This action will mark all selected equipment as "For Disposal".
                    </p>
                </div>
            </div>
        </div>
        
        <!-- Submit -->
        <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-200 dark:border-gray-700">
            <a href="{{ route('equipment.index') }}" class="btn-secondary">Cancel</a>
            <button type="submit" class="btn-primary bg-red-600 hover:bg-red-700">
                Submit {{ $equipments->count() }} Disposal Requests
            </button>
        </div>
    </form>
</div>
@endsection
