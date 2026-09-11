<?php

namespace App\Http\Controllers\Api\v1\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Services\AuditLogger;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    use ApiResponseTrait;

    public function index(Request $request): JsonResponse
    {
        $query = Admin::with('role');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('role_id')) {
            $query->where('role_id', $request->role_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $perPage = $request->get('per_page', 15);
        $users = $query->latest()->paginate($perPage);

        return $this->paginatedResponse($users, 'Daftar pengelola admin berhasil diambil.');
    }

    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|max:255|unique:admins,email',
            'password' => 'required|string|min:8|confirmed',
            'role_id'  => 'required|exists:roles,id',
            'status'   => 'required|in:active,inactive,suspended',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Validasi gagal.', 422, $validator->errors());
        }

        $admin = Admin::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'role_id'  => $request->role_id,
            'status'   => $request->status,
        ]);

        AuditLogger::log(
            action: 'create',
            module: 'Admins',
            recordId: (string) $admin->id,
            description: "Menambahkan administrator baru (API): {$admin->name} ({$admin->email})",
            newData: ['name' => $admin->name, 'email' => $admin->email, 'role_id' => $admin->role_id, 'status' => $admin->status]
        );

        return $this->successResponse($admin->load('role'), 'Akun administrator baru berhasil ditambahkan.', 201);
    }

    public function show(string $id): JsonResponse
    {
        $admin = Admin::with('role', 'auditLogs', 'loginHistories')->find($id);

        if (!$admin) {
            return $this->errorResponse('Pengelola admin tidak ditemukan.', 404);
        }

        return $this->successResponse($admin, 'Detail pengelola admin berhasil diambil.');
    }

    public function update(Request $request, string $id): JsonResponse
    {
        $user = Admin::find($id);

        if (!$user) {
            return $this->errorResponse('Pengelola admin tidak ditemukan.', 404);
        }

        $validator = Validator::make($request->all(), [
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|max:255|unique:admins,email,' . $user->id,
            'password' => 'nullable|string|min:8|confirmed',
            'role_id'  => 'required|exists:roles,id',
            'status'   => 'required|in:active,inactive,suspended',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Validasi gagal.', 422, $validator->errors());
        }

        if ($request->user()?->id === $user->id && $request->status !== 'active') {
            return $this->errorResponse('Anda tidak dapat menonaktifkan atau menangguhkan akun Anda sendiri.', 422);
        }

        if ($user->isSuperAdmin() && $request->status !== 'active') {
            $superAdminCount = Admin::whereHas('role', fn($q) => $q->where('slug', 'super-admin'))
                                    ->where('status', 'active')
                                    ->count();
            if ($superAdminCount <= 1) {
                return $this->errorResponse('Sistem harus memiliki minimal satu Super Admin yang aktif.', 422);
            }
        }

        $oldData = [
            'name'    => $user->name,
            'email'   => $user->email,
            'role_id' => $user->role_id,
            'status'  => $user->status,
        ];

        $data = [
            'name'    => $request->name,
            'email'   => $request->email,
            'role_id' => $request->role_id,
            'status'  => $request->status,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        AuditLogger::log(
            action: 'update',
            module: 'Admins',
            recordId: (string) $user->id,
            description: "Memperbarui akun administrator (API): {$user->name}",
            oldData: $oldData,
            newData: array_diff_key($data, ['password' => ''])
        );

        return $this->successResponse($user->load('role'), 'Data administrator berhasil diperbarui.');
    }

    public function destroy(Request $request, string $id): JsonResponse
    {
        $user = Admin::find($id);

        if (!$user) {
            return $this->errorResponse('Pengelola admin tidak ditemukan.', 404);
        }

        if ($request->user()?->id === $user->id) {
            return $this->errorResponse('Anda tidak dapat menghapus akun Anda sendiri.', 422);
        }

        if ($user->isSuperAdmin()) {
            $superAdminCount = Admin::whereHas('role', fn($q) => $q->where('slug', 'super-admin'))->count();
            if ($superAdminCount <= 1) {
                return $this->errorResponse('Super Admin terakhir tidak dapat dihapus.', 422);
            }
        }

        AuditLogger::log(
            action: 'delete',
            module: 'Admins',
            recordId: (string) $user->id,
            description: "Menghapus administrator (API): {$user->name} ({$user->email})",
            oldData: ['name' => $user->name, 'email' => $user->email]
        );

        $user->delete();

        return $this->successResponse(null, 'Administrator berhasil dihapus.');
    }

    public function forceLogout(Request $request, string $id): JsonResponse
    {
        $user = Admin::find($id);

        if (!$user) {
            return $this->errorResponse('Pengelola admin tidak ditemukan.', 404);
        }

        if ($request->user()?->id === $user->id) {
            return $this->errorResponse('Anda tidak dapat melakukan force logout pada sesi Anda sendiri.', 422);
        }

        // Revoke all tokens for this user
        if (method_exists($user, 'tokens')) {
            $user->tokens()->delete();
        }

        $user->timestamps = false;
        $user->remember_token = null;
        $user->save();

        AuditLogger::log(
            action: 'force_logout',
            module: 'Admins',
            recordId: (string) $user->id,
            description: "Melakukan force logout (API) pada administrator {$user->name}"
        );

        return $this->successResponse(null, "Administrator {$user->name} berhasil di-force logout.");
    }
}
