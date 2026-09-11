<?php

namespace App\Http\Controllers\Api\v1\Admin;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use App\Models\Role;
use App\Services\AuditLogger;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RoleController extends Controller
{
    use ApiResponseTrait;

    public function index(Request $request): JsonResponse
    {
        $roles = Role::withCount(['admins', 'permissions'])->with('permissions')->get();
        return $this->successResponse($roles, 'Daftar role & permission berhasil diambil.');
    }

    public function permissions(): JsonResponse
    {
        $permissions = Permission::all()->groupBy('module');
        return $this->successResponse($permissions, 'Daftar semua permission berhasil diambil.');
    }

    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name'        => 'required|string|max:255',
            'slug'        => 'required|string|max:255|unique:roles,slug',
            'description' => 'nullable|string',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Validasi gagal.', 422, $validator->errors());
        }

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
            description: "Membuat peran/role baru (API): {$role->name}",
            newData: ['name' => $role->name, 'slug' => $role->slug, 'permissions' => $request->permissions]
        );

        return $this->successResponse($role->load('permissions'), 'Role baru berhasil dibuat.', 201);
    }

    public function show(string $id): JsonResponse
    {
        $role = Role::with('permissions', 'admins:id,name,email')->find($id);

        if (!$role) {
            return $this->errorResponse('Role tidak ditemukan.', 404);
        }

        return $this->successResponse($role, 'Detail role berhasil diambil.');
    }

    public function update(Request $request, string $id): JsonResponse
    {
        $role = Role::find($id);

        if (!$role) {
            return $this->errorResponse('Role tidak ditemukan.', 404);
        }

        $validator = Validator::make($request->all(), [
            'name'        => 'required|string|max:255',
            'slug'        => 'required|string|max:255|unique:roles,slug,' . $role->id,
            'description' => 'nullable|string',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Validasi gagal.', 422, $validator->errors());
        }

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
            description: "Memperbarui peran/role (API): {$role->name}",
            oldData: $oldData,
            newData: ['name' => $role->name, 'slug' => $role->slug]
        );

        return $this->successResponse($role->load('permissions'), 'Role berhasil diperbarui.');
    }

    public function destroy(string $id): JsonResponse
    {
        $role = Role::find($id);

        if (!$role) {
            return $this->errorResponse('Role tidak ditemukan.', 404);
        }

        if ($role->slug === 'super-admin') {
            return $this->errorResponse('Role Super Admin tidak dapat dihapus.', 422);
        }

        if ($role->admins()->count() > 0) {
            return $this->errorResponse('Role ini masih digunakan oleh ' . $role->admins()->count() . ' administrator.', 422);
        }

        AuditLogger::log(
            action: 'delete',
            module: 'Roles',
            recordId: (string) $role->id,
            description: "Menghapus role (API): {$role->name}",
            oldData: ['name' => $role->name, 'slug' => $role->slug]
        );

        $role->delete();

        return $this->successResponse(null, 'Role berhasil dihapus.');
    }
}
