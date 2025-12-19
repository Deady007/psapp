<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\PermissionRegistrar;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RbacSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $permissions = [
            // Customers
            'customers.view',
            'customers.create',
            'customers.edit',
            'customers.delete',
            // Contacts
            'contacts.view',
            'contacts.create',
            'contacts.edit',
            'contacts.delete',
            // Projects
            'projects.view',
            'projects.create',
            'projects.edit',
            'projects.delete',
            // Users
            'users.view',
            'users.create',
            'users.edit',
            'users.delete',
            // Roles
            'roles.view',
            'roles.create',
            'roles.edit',
            'roles.delete',
            // Permissions
            'permissions.view',
            'permissions.create',
            'permissions.edit',
            'permissions.delete',
        ];

        $permissionModels = collect($permissions)->map(function (string $permission) {
            return Permission::firstOrCreate(
                ['name' => $permission, 'guard_name' => 'web'],
                ['name' => $permission, 'guard_name' => 'web'],
            );
        });

        $adminRole = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $userRole = Role::firstOrCreate(['name' => 'user', 'guard_name' => 'web']);

        $adminRole->syncPermissions($permissionModels);

        $userPermissions = $permissionModels->filter(function (Permission $permission) {
            return str_starts_with($permission->name, 'customers.')
                || str_starts_with($permission->name, 'contacts.')
                || str_starts_with($permission->name, 'projects.');
        });

        $userRole->syncPermissions($userPermissions);

        $admin = User::updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin',
                'password' => Hash::make('Admin@12345'),
            ],
        );
        $admin->syncRoles([$adminRole]);

        $user = User::updateOrCreate(
            ['email' => 'user@example.com'],
            [
                'name' => 'User',
                'password' => Hash::make('User@12345'),
            ],
        );
        $user->syncRoles([$userRole]);
    }
}
