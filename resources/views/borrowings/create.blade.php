@extends('layouts.app')

@section('title', 'Lend Equipment')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Lend Equipment</h2>
            <p class="text-gray-500 dark:text-gray-400">Record equipment borrowing</p>
        </div>
        <a href="{{ route('borrowings.index') }}" class="btn-secondary flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Back
        </a>
    </div>
    
    <!-- Form -->
    <form method="POST" action="{{ route('borrowings.store') }}" class="card p-6 space-y-6">
        @csrf
        
        <!-- Equipment Selection -->
        <div>
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Equipment</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Select Equipment <span class="text-red-500">*</span></label>
                    <select name="equipment_id" class="input-field @error('equipment_id') border-red-500 @enderror" required>
                        <option value="">Select available equipment</option>
                        @foreach($equipments as $equipment)
                        <option value="{{ $equipment->id }}" {{ old('equipment_id', request('equipment_id')) == $equipment->id ? 'selected' : '' }}>
                            {{ $equipment->equipment_code }} - {{ $equipment->model_name }} ({{ $equipment->brand->name }})
                        </option>
                        @endforeach
                    </select>
                    @error('equipment_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Condition on Borrow <span class="text-red-500">*</span></label>
                    <select name="condition_on_borrow" class="input-field @error('condition_on_borrow') border-red-500 @enderror" required>
                        <option value="new" {{ old('condition_on_borrow') == 'new' ? 'selected' : '' }}>New</option>
                        <option value="good" {{ old('condition_on_borrow', 'good') == 'good' ? 'selected' : '' }}>Good</option>
                        <option value="fair" {{ old('condition_on_borrow') == 'fair' ? 'selected' : '' }}>Fair</option>
                        <option value="poor" {{ old('condition_on_borrow') == 'poor' ? 'selected' : '' }}>Poor</option>
                    </select>
                    @error('condition_on_borrow')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
            </div>
        </div>
        
        <!-- Borrower Information -->
        <div>
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Borrower Information</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">First Name <span class="text-red-500">*</span></label>
                    <input type="text" name="borrower_firstname" value="{{ old('borrower_firstname') }}" class="input-field @error('borrower_firstname') border-red-500 @enderror" required>
                    @error('borrower_firstname')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Last Name <span class="text-red-500">*</span></label>
                    <input type="text" name="borrower_lastname" value="{{ old('borrower_lastname') }}" class="input-field @error('borrower_lastname') border-red-500 @enderror" required>
                    @error('borrower_lastname')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Middle Name</label>
                    <input type="text" name="borrower_middlename" value="{{ old('borrower_middlename') }}" class="input-field">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">ID Number</label>
                    <input type="text" name="id_number" value="{{ old('id_number') }}" class="input-field" placeholder="Student/Employee ID">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Department</label>
                    <select name="department_id" class="input-field">
                        <option value="">Select Department</option>
                        @foreach($departments as $department)
                        <option value="{{ $department->id }}" {{ old('department_id') == $department->id ? 'selected' : '' }}>{{ $department->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Contact Number</label>
                    <input type="text" name="contact_number" value="{{ old('contact_number') }}" class="input-field">
                </div>
                <div class="md:col-span-3">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Email</label>
                    <input type="email" name="email" value="{{ old('email') }}" class="input-field">
                </div>
            </div>
        </div>
        
        <!-- Borrowing Details -->
        <div>
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Borrowing Details</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Borrow Date <span class="text-red-500">*</span></label>
                    <input type="text" name="borrow_date" value="{{ old('borrow_date') }}" class="input-field @error('borrow_date') border-red-500 @enderror" placeholder="MM/DD/YYYY" required onfocus="(this.type='date')" onblur="if(!this.value)this.type='text'">
                    @error('borrow_date')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Expected Return Date <span class="text-red-500">*</span></label>
                    <input type="text" name="expected_return_date" value="{{ old('expected_return_date') }}" class="input-field @error('expected_return_date') border-red-500 @enderror" placeholder="MM/DD/YYYY" required onfocus="(this.type='date')" onblur="if(!this.value)this.type='text'">
                    @error('expected_return_date')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Purpose <span class="text-red-500">*</span></label>
                    <textarea name="purpose" rows="2" class="input-field @error('purpose') border-red-500 @enderror" placeholder="Purpose of borrowing..." required>{{ old('purpose') }}</textarea>
                    @error('purpose')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Remarks</label>
                    <textarea name="remarks" rows="2" class="input-field" placeholder="Additional remarks...">{{ old('remarks') }}</textarea>
                </div>
            </div>
        </div>
        
        <!-- Submit -->
        <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-200 dark:border-gray-700">
            <a href="{{ route('borrowings.index') }}" class="btn-secondary">Cancel</a>
            <button type="submit" class="btn-primary">Create Borrowing</button>
        </div>
    </form>
</div>
@endsection
