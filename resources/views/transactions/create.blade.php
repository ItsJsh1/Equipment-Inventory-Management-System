@extends('layouts.app')

@section('title', 'New ' . ucfirst($type ?? 'Incoming') . ' Transaction')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">
                New {{ ucfirst($type ?? 'Incoming') }} Transaction
            </h2>
            <p class="text-gray-500 dark:text-gray-400">
                @if(($type ?? 'incoming') == 'incoming')
                    Record equipment received into inventory
                @else
                    Record equipment released from inventory
                @endif
            </p>
        </div>
        <a href="{{ route('transactions.index') }}" class="btn-secondary flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Back
        </a>
    </div>
    
    <!-- Form -->
    <form method="POST" action="{{ route('transactions.store') }}" class="card p-6 space-y-6">
        @csrf
        <input type="hidden" name="type" value="{{ $type ?? 'incoming' }}">
        
        <!-- Transaction Type Badge -->
        <div class="flex items-center gap-2">
            <span class="inline-flex items-center gap-2 px-3 py-1.5 text-sm font-medium rounded-full {{ ($type ?? 'incoming') == 'incoming' ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400' : 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400' }}">
                @if(($type ?? 'incoming') == 'incoming')
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"/>
                </svg>
                Incoming Transaction
                @else
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"/>
                </svg>
                Outgoing Transaction
                @endif
            </span>
        </div>
        
        @if(($type ?? 'incoming') == 'incoming')
        <!-- Incoming Equipment Section -->
        <div x-data="{ createNew: {{ old('create_equipment', true) ? 'true' : 'false' }} }">
            <!-- Equipment Selection -->
            <div class="mb-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Equipment Details</h3>
                <div class="md:col-span-2 p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                    <label class="flex items-center gap-2 mb-3">
                        <input type="checkbox" name="create_equipment" value="1" x-model="createNew" {{ old('create_equipment', true) ? 'checked' : '' }} class="rounded border-gray-300 dark:border-gray-600">
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Create new equipment record</span>
                    </label>
                    <p class="text-xs text-gray-500 dark:text-gray-400">If unchecked, you can link to an existing equipment record</p>
                    
                    <!-- Existing Equipment Dropdown (shown when createNew is false) -->
                    <div x-show="!createNew" x-transition class="mt-4">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Existing Equipment <span class="text-red-500">*</span></label>
                        <select name="equipment_id" class="input-field" x-bind:required="!createNew">
                            <option value="">Select Equipment</option>
                            @foreach($equipments as $equipment)
                            <option value="{{ $equipment->id }}" {{ old('equipment_id') == $equipment->id ? 'selected' : '' }}>
                                {{ $equipment->equipment_code }} - {{ $equipment->model_name }} ({{ $equipment->brand->name }})
                            </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
            
            <!-- New Equipment Details (shown when createNew is true) -->
            <div x-show="createNew" x-transition>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">New Equipment Information</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Model Name <span class="text-red-500">*</span></label>
                        <input type="text" name="model_name" value="{{ old('model_name') }}" class="input-field @error('model_name') border-red-500 @enderror" x-bind:required="createNew">
                        @error('model_name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Serial Number <span class="text-red-500">*</span></label>
                        <input type="text" name="serial_number" value="{{ old('serial_number') }}" class="input-field @error('serial_number') border-red-500 @enderror" x-bind:required="createNew">
                        @error('serial_number')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Brand <span class="text-red-500">*</span></label>
                        <select name="brand_id" class="input-field @error('brand_id') border-red-500 @enderror" x-bind:required="createNew">
                            <option value="">Select Brand</option>
                            @foreach($brands as $brand)
                            <option value="{{ $brand->id }}" {{ old('brand_id') == $brand->id ? 'selected' : '' }}>{{ $brand->name }}</option>
                            @endforeach
                        </select>
                        @error('brand_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Category <span class="text-red-500">*</span></label>
                        <select name="category_id" class="input-field @error('category_id') border-red-500 @enderror" x-bind:required="createNew">
                            <option value="">Select Category</option>
                            @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                            @endforeach
                        </select>
                        @error('category_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Location</label>
                        <select name="location_id" class="input-field">
                            <option value="">Select Location</option>
                            @foreach($locations as $location)
                            <option value="{{ $location->id }}" {{ old('location_id') == $location->id ? 'selected' : '' }}>{{ $location->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Condition</label>
                        <select name="condition" class="input-field">
                            <option value="good" {{ old('condition') == 'good' ? 'selected' : '' }}>Good</option>
                            <option value="fair" {{ old('condition') == 'fair' ? 'selected' : '' }}>Fair</option>
                            <option value="poor" {{ old('condition') == 'poor' ? 'selected' : '' }}>Poor</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
        @else
        <!-- Outgoing Equipment Selection -->
        <div>
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Equipment Details</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Equipment <span class="text-red-500">*</span></label>
                    <select name="equipment_id" class="input-field @error('equipment_id') border-red-500 @enderror" required>
                        <option value="">Select Equipment</option>
                        @foreach($equipments as $equipment)
                        <option value="{{ $equipment->id }}" {{ old('equipment_id', request('equipment_id')) == $equipment->id ? 'selected' : '' }}>
                            {{ $equipment->equipment_code }} - {{ $equipment->model_name }} ({{ $equipment->brand->name }})
                        </option>
                        @endforeach
                    </select>
                    @error('equipment_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    @if($equipments->isEmpty())
                    <p class="text-yellow-600 dark:text-yellow-400 text-xs mt-1">No available equipment found. Equipment must have "Available" status to be selected for outgoing transactions.</p>
                    @endif
                </div>
            </div>
        </div>
        @endif
        
        <!-- Person Information -->
        <div>
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                @if(($type ?? 'incoming') == 'incoming')
                    Source / Supplier Information
                @else
                    Recipient Information
                @endif
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        {{ ($type ?? 'incoming') == 'incoming' ? 'Supplier/Source Name' : 'Recipient Name' }} <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="person_name" value="{{ old('person_name') }}" class="input-field @error('person_name') border-red-500 @enderror" required>
                    @error('person_name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Department/Organization</label>
                    <input type="text" name="person_department" value="{{ old('person_department') }}" class="input-field">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Contact Number</label>
                    <input type="text" name="person_contact" value="{{ old('person_contact') }}" class="input-field">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Email</label>
                    <input type="email" name="person_email" value="{{ old('person_email') }}" class="input-field">
                </div>
            </div>
        </div>
        
        <!-- Transaction Details -->
        <div>
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Transaction Details</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Transaction Date <span class="text-red-500">*</span></label>
                    <input type="text" name="transaction_date" value="{{ old('transaction_date') }}" class="input-field @error('transaction_date') border-red-500 @enderror" placeholder="MM/DD/YYYY" required onfocus="(this.type='date')" onblur="if(!this.value)this.type='text'">
                    @error('transaction_date')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Reference Number</label>
                    <input type="text" name="reference_number" value="{{ old('reference_number') }}" class="input-field" placeholder="PO/Invoice/Document No.">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Purpose/Reason</label>
                    <textarea name="purpose" rows="2" class="input-field" placeholder="Purpose of this transaction...">{{ old('purpose') }}</textarea>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Notes</label>
                    <textarea name="notes" rows="3" class="input-field" placeholder="Additional notes...">{{ old('notes') }}</textarea>
                </div>
            </div>
        </div>
        
        <!-- Submit -->
        <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-200 dark:border-gray-700">
            <a href="{{ route('transactions.index') }}" class="btn-secondary">Cancel</a>
            <button type="submit" class="btn-primary">Create Transaction</button>
        </div>
    </form>
</div>

@push('scripts')
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.store('createEquipment', true);
    });
</script>
@endpush
@endsection
