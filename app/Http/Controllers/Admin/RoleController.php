<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use App\Models\Role;
use App\Services\AuditLogger;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    /**
     * Display listing of roles.
     */
    public function index()
    {
        $roles = Role::withCount(['admins', 'permissions'])->get();
        return view('admin.roles.index', compact('roles'));
    }

    /**
     * Show form for creating a new role.
     */
    public function create()
    {
        $permissions = Permission::all()->groupBy('module');
        return view('admin.roles.create', compact('permissions'));
    }

    /**
     * Store newly created role.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'        => 'required|string|max:255',
            'slug'        => 'required|string|max:255|unique:roles,slug',
            'description' => 'nullable|string',
            'permissions' => 'array',
        ], [
            'name.required' => 'Nama role wajib diisi.',
            'slug.required' => 'Slug role wajib diisi.',
            'slug.unique'   => 'Slug ini sudah digunakan.',
        ]);

        $role = Role::create([
            'name'        => $request->name,
            'slug'        => strtolower($request->slug),
            'description' => $request->description,
        ]);

        if ($request->has('permissions')) {
            $role->permissions()->sync($request->permissions);
        }

        AuditLogger::log(
            action: 'create',
            module: 'Roles',
            recordId: (string) $role->id,
            description: "Membuat peran/role baru: {$role->name}",
            newData: ['name' => $role->name, 'slug' => $role->slug, 'permissions' => $request->permissions]
        );

        return redirect()->route('admin.roles.index')->with('success', 'Role baru berhasil dibuat.');
    }

    /**
     * Show form for editing role.
     */
    public function edit(Role $role)
    {
        $permissions = Permission::all()->groupBy('module');
        $rolePermissions = $role->permissions->pluck('id')->toArray();
        return view('admin.roles.edit', compact('role', 'permissions', 'rolePermissions'));
    }

    /**
     * Update role.
     */
    public function update(Request $request, Role $role)
    {
        $request->validate([
            'name'        => 'required|string|max:255',
            'slug'        => 'required|string|max:255|unique:roles,slug,' . $role->id,
            'description' => 'nullable|string',
            'permissions' => 'array',
        ], [
            'name.required' => 'Nama role wajib diisi.',
            'slug.required' => 'Slug role wajib diisi.',
            'slug.unique'   => 'Slug ini sudah digunakan oleh role lain.',
        ]);

        $oldData = [
            'name'        => $role->name,
            'slug'        => $role->slug,
            'permissions' => $role->permissions->pluck('id')->toArray(),
        ];

        $role->update([
            'name'        => $request->name,
            'slug'        => strtolower($request->slug),
            'description' => $request->description,
        ]);

        $role->permissions()->sync($request->permissions ?? []);

        AuditLogger::log(
            action: 'update',
            module: 'Roles',
            recordId: (string) $role->id,
            description: "Memperbarui peran/role: {$role->name}",
            oldData: $oldData,
            newData: ['name' => $role->name, 'slug' => $role->slug, 'permissions' => $request->permissions]
        );

        return redirect()->route('admin.roles.index')->with('success', 'Peran/role berhasil diperbarui.');
    }

    /**
     * Delete role.
     */
    public function destroy(Role $role)
    {
        if ($role->slug === 'super-admin') {
            return redirect()->back()->with('error', 'Role Super Admin bawaan sistem tidak dapat dihapus.');
        }

        if ($role->admins()->count() > 0) {
            return redirect()->back()->with('error', 'Role ini masih digunakan oleh ' . $role->admins()->count() . ' administrator dan tidak dapat dihapus.');
        }

        AuditLogger::log(
            action: 'delete',
            module: 'Roles',
            recordId: (string) $role->id,
            description: "Menghapus role: {$role->name}",
            oldData: ['name' => $role->name, 'slug' => $role->slug]
        );

        $role->delete();

        return redirect()->route('admin.roles.index')->with('success', 'Role berhasil dihapus.');
    }
}
