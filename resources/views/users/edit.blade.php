@extends('layouts.app')

@section('title', 'Edit User')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Edit User</h2>
            <p class="text-gray-500 dark:text-gray-400">{{ $user->name }}</p>
        </div>
        <a href="{{ route('users.index') }}" class="btn-secondary flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Back
        </a>
    </div>
    
    <!-- Form -->
    <form method="POST" action="{{ route('users.update', $user) }}" class="card p-6 space-y-6" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        
        <!-- Profile Picture -->
        <div>
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Profile Picture</h3>
            <div class="flex items-center gap-6">
                <div class="shrink-0 relative">
                    <img id="preview-image" src="{{ $user->profile_picture_url }}" 
                         alt="{{ $user->name }}" class="h-24 w-24 rounded-full object-cover border-4 border-gray-200 dark:border-gray-700">
                    @if($user->profile_picture)
                    <label class="absolute -bottom-1 -right-1 bg-red-500 hover:bg-red-600 text-white rounded-full p-1.5 cursor-pointer" title="Remove photo">
                        <input type="checkbox" name="remove_picture" value="1" class="hidden" onchange="toggleRemovePicture(this)">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </label>
                    @endif
                </div>
                <div class="flex-1">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Upload New Photo</label>
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
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">First Name <span class="text-red-500">*</span></label>
                    <input type="text" name="firstname" value="{{ old('firstname', $user->firstname) }}" class="input-field @error('firstname') border-red-500 @enderror" required>
                    @error('firstname')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Middle Name</label>
                    <input type="text" name="middlename" value="{{ old('middlename', $user->middlename) }}" class="input-field">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Last Name <span class="text-red-500">*</span></label>
                    <input type="text" name="lastname" value="{{ old('lastname', $user->lastname) }}" class="input-field @error('lastname') border-red-500 @enderror" required>
                    @error('lastname')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
            </div>
        </div>
        
        <!-- Account Information -->
        <div>
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Account Information</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Email Address <span class="text-red-500">*</span></label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}" class="input-field @error('email') border-red-500 @enderror" required>
                    @error('email')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Employee ID</label>
                    <input type="text" name="employee_id" value="{{ old('employee_id', $user->employee_id) }}" class="input-field @error('employee_id') border-red-500 @enderror">
                    @error('employee_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Contact Number</label>
                    <input type="text" name="contact_number" value="{{ old('contact_number', $user->contact_number) }}" class="input-field">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Department</label>
                    <select name="department_id" class="input-field">
                        <option value="">Select Department</option>
                        @foreach($departments as $department)
                        <option value="{{ $department->id }}" {{ old('department_id', $user->department_id) == $department->id ? 'selected' : '' }}>{{ $department->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Role <span class="text-red-500">*</span></label>
                    <select name="role" class="input-field @error('role') border-red-500 @enderror" required>
                        @php
                        $adminCount = \App\Models\User::role('admin')->where('id', '!=', $user->id)->count();
                        $currentRole = $user->roles->first()?->name;
                        @endphp
                        @foreach($roles as $role)
                        @if($role->name === 'admin' && $adminCount >= 2 && $currentRole !== 'admin')
                        <option value="{{ $role->name }}" disabled>{{ ucfirst(str_replace('_', ' ', $role->name)) }} (Max 2 admins reached)</option>
                        @else
                        <option value="{{ $role->name }}" {{ old('role', $currentRole) == $role->name ? 'selected' : '' }}>
                            {{ ucfirst(str_replace('_', ' ', $role->name)) }}
                        </option>
                        @endif
                        @endforeach
                    </select>
                    @error('role')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
            </div>
        </div>
        
        <!-- Password -->
        <div>
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Change Password</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">Leave blank to keep the current password</p>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">New Password</label>
                    <input type="password" name="password" class="input-field @error('password') border-red-500 @enderror" placeholder="••••••••">
                    @error('password')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Confirm New Password</label>
                    <input type="password" name="password_confirmation" class="input-field" placeholder="••••••••">
                </div>
            </div>
        </div>
        
        <!-- Status -->
        <div>
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Account Status</h3>
            <div class="flex items-center gap-4">
                <label class="flex items-center gap-2">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', $user->is_active) ? 'checked' : '' }} class="rounded border-gray-300 text-gray-900 focus:ring-gray-500">
                    <span class="text-sm text-gray-700 dark:text-gray-300">Account is active</span>
                </label>
            </div>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">Inactive users cannot log in to the system</p>
        </div>
        
        <!-- Submit -->
        <div class="flex items-center justify-between pt-4 border-t border-gray-200 dark:border-gray-700">
            <div>
                @if($user->id !== auth()->id())
                <form method="POST" action="{{ route('users.destroy', $user) }}" class="inline" onsubmit="return confirm('Are you sure you want to delete this user? This action cannot be undone.')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300 text-sm">
                        Delete User
                    </button>
                </form>
                @endif
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('users.index') }}" class="btn-secondary">Cancel</a>
                <button type="submit" class="btn-primary">Update User</button>
            </div>
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

function toggleRemovePicture(checkbox) {
    if (checkbox.checked) {
        document.getElementById('preview-image').src = 'https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&color=7F9CF5&background=EBF4FF';
    } else {
        document.getElementById('preview-image').src = '{{ $user->profile_picture_url }}';
    }
}
</script>
@endsection
