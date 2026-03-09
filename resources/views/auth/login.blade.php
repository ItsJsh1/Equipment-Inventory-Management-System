@extends('layouts.guest')

@section('title', 'Login')

@section('content')
<div class="card p-8">
    <!-- Logo -->
    <div class="text-center mb-8">
        <div class="w-16 h-16 bg-black dark:bg-white rounded-xl flex items-center justify-center mx-auto mb-4">
            <span class="text-white dark:text-black font-bold text-2xl">E</span>
        </div>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Welcome to EIMS</h1>
        <p class="text-gray-500 dark:text-gray-400 mt-1">Equipment Inventory Management System</p>
    </div>
    
    <!-- Login Form -->
    <form method="POST" action="{{ route('login') }}" class="space-y-6">
        @csrf
        
        <!-- Email -->
        <div>
            <label for="email" class="label">Email Address</label>
            <input type="email" 
                   id="email" 
                   name="email" 
                   value="{{ old('email') }}" 
                   class="input-field @error('email') border-red-500 @enderror" 
                   placeholder="Enter your email"
                   required 
                   autofocus>
            @error('email')
                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
            @enderror
        </div>
        
        <!-- Password -->
        <div>
            <label for="password" class="label">Password</label>
            <div x-data="{ show: false }" class="relative">
                <input x-bind:type="show ? 'text' : 'password'" 
                       id="password" 
                       name="password" 
                       class="input-field pr-10 @error('password') border-red-500 @enderror" 
                       placeholder="Enter your password"
                       required>
                <button type="button" 
                        @click="show = !show" 
                        class="absolute inset-y-0 right-0 px-3 flex items-center text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                    <svg x-show="!show" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                    <svg x-show="show" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                    </svg>
                </button>
            </div>
            @error('password')
                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
            @enderror
        </div>
        
        <!-- Remember Me -->
        <div class="flex items-center justify-between">
            <label class="flex items-center">
                <input type="checkbox" 
                       name="remember" 
                       class="w-4 h-4 rounded border-gray-300 text-black focus:ring-black dark:border-gray-600 dark:bg-gray-700 dark:focus:ring-white">
                <span class="ml-2 text-sm text-gray-600 dark:text-gray-400">Remember me</span>
            </label>
        </div>
        
        <!-- Submit Button -->
        <button type="submit" class="w-full btn-primary py-3 font-medium">
            Sign In
        </button>
    </form>
</div>

<!-- Footer -->
<p class="text-center text-sm text-gray-500 dark:text-gray-400 mt-6">
    &copy; {{ date('Y') }} EIMS. All rights reserved.
</p>
@endsection
