<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $permission
     * @return mixed
     */
    public function handle(Request $request, Closure $next, string $permission)
    {
        $admin = Auth::guard('admin')->user();

        if (!$admin) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Unauthenticated.'], 401);
            }
            return redirect()->route('admin.login');
        }

        if (!$admin::where('id', $admin->id)->where('status', 'active')->exists()) {
            Auth::guard('admin')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Your account is suspended or inactive.'], 403);
            }
            return redirect()->route('admin.login')->with('error', 'Akun Anda dinonaktifkan atau ditangguhkan.');
        }

        if ($admin->hasPermission($permission)) {
            return $next($request);
        }

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Anda tidak memiliki hak akses (permission: ' . $permission . ') untuk melakukan tindakan ini.'
            ], 403);
        }

        abort(403, 'Anda tidak memiliki hak akses (permission: ' . $permission . ') untuk mengakses halaman ini.');
    }
}
