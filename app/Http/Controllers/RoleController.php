<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    private const PROTECTED_ROLE = 'super-admin';

    public function index()
    {
        $roles = Role::withCount(['users', 'permissions'])->orderBy('name')->get();
        return view('roles.index', compact('roles'));
    }

    public function create()
    {
        $permissions = Permission::orderBy('name')->get();
        return view('roles.create', compact('permissions'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'          => 'required|string|max:255|regex:/^[a-z0-9\-]+$/|unique:roles,name',
            'description'   => 'nullable|string|max:255',
            'permissions'   => 'array',
            'permissions.*' => 'exists:permissions,name',
        ], [
            'name.regex' => 'Role name may only contain lowercase letters, numbers, and hyphens (e.g. content-writer).',
        ]);

        $role = Role::create([
            'name'        => $data['name'],
            'description' => $data['description'] ?? null,
            'guard_name'  => 'web',
        ]);
        $role->syncPermissions($data['permissions'] ?? []);

        return redirect()->route('roles.index')->with('success', 'Role created successfully.');
    }

    public function edit(Role $role)
    {
        $permissions = Permission::orderBy('name')->get();
        return view('roles.edit', compact('role', 'permissions'));
    }

    public function update(Request $request, Role $role)
    {
        $data = $request->validate([
            'description'   => 'nullable|string|max:255',
            'permissions'   => 'array',
            'permissions.*' => 'exists:permissions,name',
        ]);

        $role->update(['description' => $data['description'] ?? null]);
        $role->syncPermissions($data['permissions'] ?? []);

        return redirect()->route('roles.index')->with('success', 'Role updated successfully.');
    }

    public function destroy(Role $role)
    {
        if ($role->name === self::PROTECTED_ROLE) {
            return back()->with('error', 'The Super Admin role cannot be deleted.');
        }
        if ($role->users()->count() > 0) {
            return back()->with('error', 'Cannot delete a role that is still assigned to users.');
        }

        $role->delete();
        return redirect()->route('roles.index')->with('success', 'Role deleted.');
    }
}
