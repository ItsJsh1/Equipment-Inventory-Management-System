<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Define permissions
        $permissions = [
            // Dashboard
            'view_dashboard',
            
            // Equipment permissions
            'view_equipment',
            'create_equipment',
            'edit_equipment',
            'delete_equipment',
            'export_equipment',
            
            // Transaction permissions (Incoming/Outgoing)
            'view_transactions',
            'create_transactions',
            'edit_transactions',
            'delete_transactions',
            'approve_transactions',
            'export_transactions',
            
            // Borrowing permissions
            'view_borrowings',
            'create_borrowings',
            'edit_borrowings',
            'delete_borrowings',
            'approve_borrowings',
            'export_borrowings',
            
            // Maintenance permissions
            'view_maintenance',
            'create_maintenance',
            'edit_maintenance',
            'delete_maintenance',
            'export_maintenance',
            
            // Disposal permissions
            'view_disposals',
            'create_disposals',
            'edit_disposals',
            'delete_disposals',
            'approve_disposals',
            'export_disposals',
            
            // Master data permissions
            'view_brands',
            'manage_brands',
            'view_categories',
            'manage_categories',
            'view_departments',
            'manage_departments',
            'view_locations',
            'manage_locations',
            
            // User management permissions
            'view_users',
            'create_users',
            'edit_users',
            'delete_users',
            
            // Reports permissions
            'view_reports',
            'export_reports',
            
            // Audit trail
            'view_audit_trail',
            
            // Settings
            'manage_settings',
        ];

        // Create permissions
        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Create roles and assign permissions
        
        // Super Admin - has all permissions
        $superAdminRole = Role::create(['name' => 'super_admin']);
        $superAdminRole->givePermissionTo(Permission::all());

        // Admin - has most permissions except user management and settings
        $adminRole = Role::create(['name' => 'admin']);
        $adminRole->givePermissionTo([
            'view_dashboard',
            'view_equipment', 'create_equipment', 'edit_equipment', 'delete_equipment', 'export_equipment',
            'view_transactions', 'create_transactions', 'edit_transactions', 'delete_transactions', 'approve_transactions', 'export_transactions',
            'view_borrowings', 'create_borrowings', 'edit_borrowings', 'delete_borrowings', 'approve_borrowings', 'export_borrowings',
            'view_maintenance', 'create_maintenance', 'edit_maintenance', 'delete_maintenance', 'export_maintenance',
            'view_disposals', 'create_disposals', 'edit_disposals', 'delete_disposals', 'approve_disposals', 'export_disposals',
            'view_brands', 'manage_brands',
            'view_categories', 'manage_categories',
            'view_departments', 'manage_departments',
            'view_locations', 'manage_locations',
            'view_reports', 'export_reports',
            'view_audit_trail',
        ]);

        // Staff - limited permissions (view and create only)
        $staffRole = Role::create(['name' => 'staff']);
        $staffRole->givePermissionTo([
            'view_dashboard',
            'view_equipment',
            'view_transactions', 'create_transactions',
            'view_borrowings', 'create_borrowings',
            'view_maintenance',
            'view_disposals',
            'view_brands',
            'view_categories',
            'view_departments',
            'view_locations',
            'view_reports',
        ]);
    }
}
