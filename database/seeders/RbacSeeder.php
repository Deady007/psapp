<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

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
            // Project Kickoffs
            'project_kickoffs.view',
            'project_kickoffs.create',
            'project_kickoffs.edit',
            'project_kickoffs.delete',
            // Project Requirements
            'project_requirements.view',
            'project_requirements.create',
            'project_requirements.edit',
            'project_requirements.delete',
            // Drive Documents
            'documents.view',
            'documents.create',
            'documents.edit',
            'documents.delete',
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
            // Kanban
            'kanban.view',
            'kanban.create',
            'kanban.edit',
        ];

        $permissionModels = collect($permissions)->map(function (string $permission) {
            return Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        });

        $adminRole = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $userRole = Role::firstOrCreate(['name' => 'user', 'guard_name' => 'web']);
        $developerRole = Role::firstOrCreate(['name' => 'developer', 'guard_name' => 'web']);
        $testerRole = Role::firstOrCreate(['name' => 'tester', 'guard_name' => 'web']);

        $adminRole->syncPermissions($permissionModels);

        $userPermissions = $permissionModels->filter(function (Permission $permission) {
            return str_starts_with($permission->name, 'customers.')
                || str_starts_with($permission->name, 'contacts.')
                || str_starts_with($permission->name, 'projects.')
                || str_starts_with($permission->name, 'project_kickoffs.')
                || str_starts_with($permission->name, 'project_requirements.')
                || str_starts_with($permission->name, 'documents.');
        });

        $kanbanPermissions = $permissionModels->filter(function (Permission $permission) {
            return str_starts_with($permission->name, 'kanban.');
        });

        $userRole->syncPermissions($userPermissions->merge(
            $kanbanPermissions->filter(fn (Permission $permission) => $permission->name === 'kanban.view')
        ));

        $developerRole->syncPermissions($userPermissions->merge($kanbanPermissions));
        $testerRole->syncPermissions($userPermissions->merge($kanbanPermissions));

        collect([
            [
                'name' => 'Admin',
                'email' => 'parmarviral397@gmail.com',
                'password' => 'Admin@12345',
                'role' => $adminRole,
            ],
            [
                'name' => 'User',
                'email' => 'user@example.com',
                'password' => 'User@12345',
                'role' => $userRole,
            ],
            [
                'name' => 'Developer',
                'email' => 'developer@example.com',
                'password' => 'Developer@12345',
                'role' => $developerRole,
            ],
            [
                'name' => 'Tester',
                'email' => 'tester@example.com',
                'password' => 'Tester@12345',
                'role' => $testerRole,
            ],
        ])->each(function (array $userData): void {
            $user = User::updateOrCreate(
                ['email' => $userData['email']],
                [
                    'name' => $userData['name'],
                    'password' => Hash::make($userData['password']),
                ],
            );

            $user->syncRoles([$userData['role']]);
        });
    }
}
