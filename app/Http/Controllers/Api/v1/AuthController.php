<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\LoginHistory;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    use ApiResponseTrait;

    /**
     * Handle Admin/User login via API.
     */
    public function login(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Validasi gagal.', 422, $validator->errors());
        }

        $admin = Admin::where('email', $request->email)->first();

        if (!$admin || !Hash::check($request->password, $admin->password)) {
            // Log failed login attempt
            LoginHistory::create([
                'admin_id'   => $admin?->id,
                'email'      => $request->email,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'status'     => 'failed',
            ]);

            return $this->errorResponse('Kredensial login tidak cocok.', 401);
        }

        if ($admin->status !== 'active') {
            return $this->errorResponse('Akun Anda telah dinonaktifkan atau ditangguhkan.', 403);
        }

        // Update login stats
        $admin->update([
            'last_login_at'    => now(),
            'last_login_ip'    => $request->ip(),
            'last_activity_at' => now(),
        ]);

        LoginHistory::create([
            'admin_id'   => $admin->id,
            'email'      => $admin->email,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'status'     => 'success',
        ]);

        // Create Sanctum Token
        $token = $admin->createToken('api_token')->plainTextToken;

        return $this->successResponse([
            'token' => $token,
            'admin' => [
                'id'       => $admin->id,
                'name'     => $admin->name,
                'email'    => $admin->email,
                'role'     => $admin->role?->name ?? 'Admin',
                'role_slug'=> $admin->role?->slug ?? 'admin',
                'is_super' => $admin->isSuperAdmin(),
            ]
        ], 'Login berhasil.');
    }

    /**
     * Get authenticated admin details & permissions.
     */
    public function me(Request $request): JsonResponse
    {
        $admin = $request->user();
        $admin->load('role.permissions');

        $permissions = $admin->isSuperAdmin()
            ? ['*']
            : ($admin->role ? $admin->role->permissions->pluck('slug')->toArray() : []);

        return $this->successResponse([
            'id'               => $admin->id,
            'name'             => $admin->name,
            'email'            => $admin->email,
            'status'           => $admin->status,
            'role'             => $admin->role?->name ?? 'Admin',
            'role_slug'        => $admin->role?->slug ?? 'admin',
            'is_super'         => $admin->isSuperAdmin(),
            'last_login_at'    => $admin->last_login_at,
            'last_login_ip'    => $admin->last_login_ip,
            'permissions'      => $permissions,
        ], 'Data profil berhasil diambil.');
    }

    /**
     * Logout and revoke tokens.
     */
    public function logout(Request $request): JsonResponse
    {
        $user = $request->user();

        if ($user && method_exists($user, 'currentAccessToken') && $user->currentAccessToken()) {
            $user->currentAccessToken()->delete();
        }

        return $this->successResponse(null, 'Berhasil logout dan token dicabut.');
    }

    /**
     * Change Password.
     */
    public function changePassword(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'current_password' => 'required',
            'new_password'     => 'required|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Validasi gagal.', 422, $validator->errors());
        }

        $admin = $request->user();

        if (!Hash::check($request->current_password, $admin->password)) {
            return $this->errorResponse('Kata sandi saat ini salah.', 422);
        }

        $admin->update([
            'password' => Hash::make($request->new_password)
        ]);

        return $this->successResponse(null, 'Kata sandi berhasil diperbarui.');
    }
}
