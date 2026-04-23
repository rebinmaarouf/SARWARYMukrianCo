<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // create permissions
        $permissions = [
            'view_dashboard',
            'manage_exchange',
            'manage_hawala',
            'manage_vaults',
            'manage_users',
            'manage_accounts',
            'view_reports',
            'delete_records',
            'edit_past_records'
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        // 1. Cashier Role (Daily Operations Only)
        $roleCashier = Role::firstOrCreate(['name' => 'Cashier', 'guard_name' => 'web']);
        $roleCashier->syncPermissions(['view_dashboard', 'manage_exchange']);

        // 2. Accountant Role (Financial Focus)
        $roleAccountant = Role::firstOrCreate(['name' => 'Accountant', 'guard_name' => 'web']);
        $roleAccountant->syncPermissions(['view_dashboard', 'manage_exchange', 'manage_hawala', 'manage_vaults', 'manage_accounts', 'view_reports']);

        // 3. Manager Role (Supervisory)
        $roleManager = Role::firstOrCreate(['name' => 'Manager', 'guard_name' => 'web']);
        $roleManager->syncPermissions(['view_dashboard', 'manage_exchange', 'manage_hawala', 'manage_vaults', 'manage_accounts', 'view_reports', 'edit_past_records']);

        // 4. Super Admin (God Mode)
        $roleAdmin = Role::firstOrCreate(['name' => 'Super Admin', 'guard_name' => 'web']);
        $roleAdmin->syncPermissions(Permission::all());

        // Create a default Super Admin user
        $admin = User::firstOrCreate([
            'email' => 'admin@sarwary.com',
        ], [
            'name' => 'بەڕێوەبەری سەرەکی',
            'password' => Hash::make('password'),
        ]);
        
        $admin->assignRole('Super Admin');
    }
}
