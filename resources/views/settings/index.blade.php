@extends('layouts.app')

@section('title', 'Settings')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Header -->
    <div>
        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Settings</h2>
        <p class="text-gray-500 dark:text-gray-400">Configure system settings</p>
    </div>
    
    <form method="POST" action="{{ route('settings.update') }}" class="space-y-6">
        @csrf
        @method('PUT')
        
        <!-- System Information -->
        <div class="card p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">System Information</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">System Name</label>
                    <input type="text" name="system_name" value="{{ old('system_name', $settings['system_name'] ?? 'EIMS') }}" class="input-field">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Organization Name</label>
                    <input type="text" name="organization_name" value="{{ old('organization_name', $settings['organization_name'] ?? '') }}" class="input-field">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Contact Email</label>
                    <input type="email" name="contact_email" value="{{ old('contact_email', $settings['contact_email'] ?? '') }}" class="input-field">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Contact Phone</label>
                    <input type="text" name="contact_phone" value="{{ old('contact_phone', $settings['contact_phone'] ?? '') }}" class="input-field">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Address</label>
                    <textarea name="address" rows="2" class="input-field">{{ old('address', $settings['address'] ?? '') }}</textarea>
                </div>
            </div>
        </div>
        
        <!-- Code Prefixes -->
        <div class="card p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Code Prefixes</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">Configure prefixes for auto-generated codes</p>
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Equipment Code</label>
                    <input type="text" name="equipment_code_prefix" value="{{ old('equipment_code_prefix', $settings['equipment_code_prefix'] ?? 'EQP') }}" class="input-field" maxlength="5">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Transaction Code</label>
                    <input type="text" name="transaction_code_prefix" value="{{ old('transaction_code_prefix', $settings['transaction_code_prefix'] ?? 'TRX') }}" class="input-field" maxlength="5">
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Format: PREFIX-YYYY-MM-DD-0001</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Borrowing Code</label>
                    <input type="text" name="borrowing_code_prefix" value="{{ old('borrowing_code_prefix', $settings['borrowing_code_prefix'] ?? 'BRW') }}" class="input-field" maxlength="5">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Maintenance Code</label>
                    <input type="text" name="maintenance_code_prefix" value="{{ old('maintenance_code_prefix', $settings['maintenance_code_prefix'] ?? 'MNT') }}" class="input-field" maxlength="5">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Disposal Code</label>
                    <input type="text" name="disposal_code_prefix" value="{{ old('disposal_code_prefix', $settings['disposal_code_prefix'] ?? 'DSP') }}" class="input-field" maxlength="5">
                </div>
            </div>
        </div>
        
        <!-- User Management -->
        <div class="card p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">User Management</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Maximum Admin Accounts</label>
                    <input type="number" name="max_admins" value="{{ old('max_admins', $settings['max_admins'] ?? 2) }}" min="1" max="10" class="input-field">
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Maximum number of admin accounts allowed (excluding super admin)</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Default Borrowing Duration (days)</label>
                    <input type="number" name="default_borrowing_days" value="{{ old('default_borrowing_days', $settings['default_borrowing_days'] ?? 7) }}" min="1" class="input-field">
                </div>
            </div>
        </div>
        
        <!-- Submit -->
        <div class="flex items-center justify-end gap-3">
            <button type="submit" class="btn-primary">Save Settings</button>
        </div>
    </form>
</div>
@endsection
