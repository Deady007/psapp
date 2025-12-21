<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreRoleRequest;
use App\Http\Requests\UpdateRoleRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:roles.view')->only(['index', 'show']);
        $this->middleware('permission:roles.create')->only(['create', 'store']);
        $this->middleware('permission:roles.edit')->only(['edit', 'update']);
        $this->middleware('permission:roles.delete')->only(['destroy']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $roles = Role::with('permissions')
            ->orderBy('name')
            ->paginate(10);

        return view('admin.roles.index', [
            'roles' => $roles,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('admin.roles.create', [
            'permissionsMatrix' => $this->permissionMatrix(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRoleRequest $request): RedirectResponse
    {
        $role = Role::create([
            'name' => $request->string('name')->toString(),
            'guard_name' => 'web',
        ]);

        if ($request->filled('permissions')) {
            $role->syncPermissions($request->input('permissions', []));
        }

        return redirect()
            ->route('admin.roles.show', $role)
            ->with('success', 'Role created.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Role $role): View
    {
        $role->load('permissions');

        return view('admin.roles.show', [
            'role' => $role,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Role $role): View
    {
        $role->load('permissions');

        return view('admin.roles.edit', [
            'role' => $role,
            'permissionsMatrix' => $this->permissionMatrix(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRoleRequest $request, Role $role): RedirectResponse
    {
        if ($role->name === 'admin') {
            return back()->with('error', 'Cannot rename the admin role.');
        }

        $role->update([
            'name' => $request->string('name')->toString(),
        ]);

        $role->syncPermissions($request->input('permissions', []));

        return redirect()
            ->route('admin.roles.show', $role)
            ->with('success', 'Role updated.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Role $role): RedirectResponse
    {
        if (in_array($role->name, ['admin', 'user'], true)) {
            return back()->with('error', 'System roles cannot be deleted.');
        }

        $role->delete();

        return redirect()
            ->route('admin.roles.index')
            ->with('success', 'Role deleted.');
    }

    private function permissionMatrix(): array
    {
        return [
            'customers' => ['label' => 'Customers', 'actions' => ['view', 'create', 'edit', 'delete']],
            'contacts' => ['label' => 'Contacts', 'actions' => ['view', 'create', 'edit', 'delete']],
            'projects' => ['label' => 'Projects', 'actions' => ['view', 'create', 'edit', 'delete']],
            'project_kickoffs' => ['label' => 'Project Kickoffs', 'actions' => ['view', 'create', 'edit', 'delete']],
            'project_requirements' => ['label' => 'Project Requirements', 'actions' => ['view', 'create', 'edit', 'delete']],
            'project_documents' => ['label' => 'Project Documents', 'actions' => ['view', 'create', 'edit', 'delete']],
            'users' => ['label' => 'Users', 'actions' => ['view', 'create', 'edit', 'delete']],
            'roles' => ['label' => 'Roles', 'actions' => ['view', 'create', 'edit', 'delete']],
            'permissions' => ['label' => 'Permissions', 'actions' => ['view', 'create', 'edit', 'delete']],
        ];
    }
}
