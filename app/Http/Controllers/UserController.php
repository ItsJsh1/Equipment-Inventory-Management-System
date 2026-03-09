<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = User::with(['department', 'roles']);

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('firstname', 'like', "%{$search}%")
                    ->orWhere('lastname', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('employee_id', 'like', "%{$search}%");
            });
        }

        // Filter by role
        if ($request->filled('role')) {
            $query->role($request->role);
        }

        // Filter by status
        if ($request->has('status')) {
            $query->where('is_active', $request->boolean('status'));
        }

        $users = $query->latest()->paginate(15)->withQueryString();
        $roles = Role::all();
        $departments = Department::active()->get();

        return view('users.index', compact('users', 'roles', 'departments'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        if (!auth()->user()->can('create_users')) {
            abort(403, 'Unauthorized action.');
        }

        // Check max admin limit
        $maxAdmins = Setting::getValue('max_admins', 2);
        $adminCount = User::role('admin')->count();
        $canCreateAdmin = $adminCount < $maxAdmins;

        $roles = Role::where('name', '!=', 'super_admin')->get();
        $departments = Department::active()->get();

        return view('users.create', compact('roles', 'departments', 'canCreateAdmin', 'adminCount', 'maxAdmins'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if (!auth()->user()->can('create_users')) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'firstname' => 'required|string|max:255',
            'lastname' => 'required|string|max:255',
            'middlename' => 'nullable|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => ['required', 'confirmed', Password::defaults()],
            'department_id' => 'nullable|exists:departments,id',
            'employee_id' => 'nullable|string|max:50|unique:users',
            'contact_number' => 'nullable|string|max:20',
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'role' => 'required|exists:roles,name',
        ]);

        // Handle profile picture upload
        $profilePicturePath = null;
        if ($request->hasFile('profile_picture')) {
            $profilePicturePath = $request->file('profile_picture')->store('profile-pictures', 'public');
        }

        // Check admin limit
        if ($validated['role'] === 'admin') {
            $maxAdmins = Setting::getValue('max_admins', 2);
            $currentAdminCount = User::role('admin')->count();

            if ($currentAdminCount >= $maxAdmins) {
                return back()->with('error', "Maximum number of admins ({$maxAdmins}) reached.");
            }
        }

        // Prevent creating super_admin
        if ($validated['role'] === 'super_admin') {
            return back()->with('error', 'Cannot create super admin account.');
        }

        $user = User::create([
            'firstname' => $validated['firstname'],
            'lastname' => $validated['lastname'],
            'middlename' => $validated['middlename'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'department_id' => $validated['department_id'],
            'employee_id' => $validated['employee_id'],
            'contact_number' => $validated['contact_number'],
            'profile_picture' => $profilePicturePath,
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        $user->assignRole($validated['role']);

        return redirect()->route('users.index')
            ->with('success', 'User created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        $user->load(['department', 'roles']);

        return view('users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        if (!auth()->user()->can('edit_users')) {
            abort(403, 'Unauthorized action.');
        }

        // Prevent editing super admin by non-super admin
        if ($user->hasRole('super_admin') && !auth()->user()->hasRole('super_admin')) {
            abort(403);
        }

        $roles = Role::where('name', '!=', 'super_admin')->get();
        $departments = Department::active()->get();

        return view('users.edit', compact('user', 'roles', 'departments'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        if (!auth()->user()->can('edit_users')) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'firstname' => 'required|string|max:255',
            'lastname' => 'required|string|max:255',
            'middlename' => 'nullable|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'password' => ['nullable', 'confirmed', Password::defaults()],
            'department_id' => 'nullable|exists:departments,id',
            'employee_id' => 'nullable|string|max:50|unique:users,employee_id,' . $user->id,
            'contact_number' => 'nullable|string|max:20',
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'remove_picture' => 'nullable|boolean',
            'role' => 'sometimes|exists:roles,name',
            'is_active' => 'boolean',
        ]);

        // Handle profile picture
        $profilePicturePath = $user->profile_picture; // Keep existing by default
        
        if ($request->hasFile('profile_picture')) {
            // Delete old picture if exists
            if ($user->profile_picture) {
                Storage::disk('public')->delete($user->profile_picture);
            }
            $profilePicturePath = $request->file('profile_picture')->store('profile-pictures', 'public');
        } elseif ($request->boolean('remove_picture') && $user->profile_picture) {
            Storage::disk('public')->delete($user->profile_picture);
            $profilePicturePath = null;
        }

        // Check admin limit when changing role to admin
        if (isset($validated['role']) && $validated['role'] === 'admin' && !$user->hasRole('admin')) {
            $maxAdmins = Setting::getValue('max_admins', 2);
            $currentAdminCount = User::role('admin')->count();

            if ($currentAdminCount >= $maxAdmins) {
                return back()->with('error', "Maximum number of admins ({$maxAdmins}) reached.");
            }
        }

        $user->update([
            'firstname' => $validated['firstname'],
            'lastname' => $validated['lastname'],
            'middlename' => $validated['middlename'],
            'email' => $validated['email'],
            'department_id' => $validated['department_id'],
            'employee_id' => $validated['employee_id'],
            'contact_number' => $validated['contact_number'],
            'profile_picture' => $profilePicturePath,
            'is_active' => $validated['is_active'] ?? $user->is_active,
        ]);

        if (!empty($validated['password'])) {
            $user->update(['password' => Hash::make($validated['password'])]);
        }

        if (isset($validated['role']) && !$user->hasRole('super_admin')) {
            $user->syncRoles([$validated['role']]);
        }

        return redirect()->route('users.index')
            ->with('success', 'User updated successfully.');
    }

    /**
     * Toggle user status.
     */
    public function toggleStatus(User $user)
    {
        if (!auth()->user()->can('edit_users')) {
            abort(403, 'Unauthorized action.');
        }

        // Prevent disabling super admin
        if ($user->hasRole('super_admin')) {
            return back()->with('error', 'Cannot disable super admin account.');
        }

        $user->update(['is_active' => !$user->is_active]);

        $status = $user->is_active ? 'activated' : 'deactivated';

        return back()->with('success', "User {$status} successfully.");
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        if (!auth()->user()->can('delete_users')) {
            abort(403, 'Unauthorized action.');
        }

        // Prevent deleting super admin
        if ($user->hasRole('super_admin')) {
            return back()->with('error', 'Cannot delete super admin account.');
        }

        // Prevent self-deletion
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Cannot delete your own account.');
        }

        $user->delete();

        return redirect()->route('users.index')
            ->with('success', 'User deleted successfully.');
    }
}
