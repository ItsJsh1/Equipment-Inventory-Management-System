@extends('layouts.app')

@section('title', 'Add User')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Add User</h2>
            <p class="text-gray-500 dark:text-gray-400">Create a new system user</p>
        </div>
        <a href="{{ route('users.index') }}" class="btn-secondary flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Back
        </a>
    </div>
    
    <!-- Form -->
    <form method="POST" action="{{ route('users.store') }}" class="card p-6 space-y-6" enctype="multipart/form-data">
        @csrf
        
        <!-- Profile Picture -->
        <div>
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Profile Picture</h3>
            <div class="flex items-center gap-6">
                <div class="shrink-0">
                    <img id="preview-image" src="https://ui-avatars.com/api/?name=New+User&color=7F9CF5&background=EBF4FF" 
                         alt="Profile preview" class="h-24 w-24 rounded-full object-cover border-4 border-gray-200 dark:border-gray-700">
                </div>
                <div class="flex-1">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Upload Photo</label>
                    <input type="file" name="profile_picture" id="profile_picture" accept="image/*" 
                           class="input-field @error('profile_picture') border-red-500 @enderror"
                           onchange="previewImage(event)">
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">JPG, PNG, GIF up to 2MB</p>
                    @error('profile_picture')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
            </div>
        </div>
        
        <!-- Personal Information -->
        <div>
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Personal Information</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">First Name <span class="text-red-500">*</span></label>
                    <input type="text" name="firstname" value="{{ old('firstname') }}" class="input-field @error('firstname') border-red-500 @enderror" required>
                    @error('firstname')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Last Name <span class="text-red-500">*</span></label>
                    <input type="text" name="lastname" value="{{ old('lastname') }}" class="input-field @error('lastname') border-red-500 @enderror" required>
                    @error('lastname')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Middle Name</label>
                    <input type="text" name="middlename" value="{{ old('middlename') }}" class="input-field">
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
            </div>
        </div>
        
        <!-- Account Information -->
        <div>
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Account Information</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Email <span class="text-red-500">*</span></label>
                    <input type="email" name="email" value="{{ old('email') }}" class="input-field @error('email') border-red-500 @enderror" required>
                    @error('email')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Password <span class="text-red-500">*</span></label>
                    <input type="password" name="password" class="input-field @error('password') border-red-500 @enderror" required>
                    @error('password')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Confirm Password <span class="text-red-500">*</span></label>
                    <input type="password" name="password_confirmation" class="input-field" required>
                </div>
            </div>
        </div>
        
        <!-- Role & Status -->
        <div>
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Role & Status</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Role <span class="text-red-500">*</span></label>
                    <select name="role" class="input-field @error('role') border-red-500 @enderror" required>
                        <option value="">Select Role</option>
                        @foreach($roles as $role)
                        @if($role->name != 'super_admin' || auth()->user()->hasRole('super_admin'))
                        <option value="{{ $role->name }}" {{ old('role') == $role->name ? 'selected' : '' }}>
                            {{ ucwords(str_replace('_', ' ', $role->name)) }}
                            @if($role->name == 'admin')
                            ({{ $adminCount }}/{{ $maxAdmins }} max)
                            @endif
                        </option>
                        @endif
                        @endforeach
                    </select>
                    @error('role')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Status</label>
                    <select name="is_active" class="input-field">
                        <option value="1" {{ old('is_active', '1') == '1' ? 'selected' : '' }}>Active</option>
                        <option value="0" {{ old('is_active') == '0' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
            </div>
        </div>
        
        <!-- Submit -->
        <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-200 dark:border-gray-700">
            <a href="{{ route('users.index') }}" class="btn-secondary">Cancel</a>
            <button type="submit" class="btn-primary">Create User</button>
        </div>
    </form>
</div>

<script>
function previewImage(event) {
    const reader = new FileReader();
    reader.onload = function() {
        document.getElementById('preview-image').src = reader.result;
    };
    reader.readAsDataURL(event.target.files[0]);
}
</script>
@endsection
