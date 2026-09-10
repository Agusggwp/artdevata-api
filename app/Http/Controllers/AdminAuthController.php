<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\LoginHistory;
use App\Services\AuditLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

class AdminAuthController extends Controller
{
    /**
     * Show registration form.
     */
    public function showRegister()
    {
        return view('admin.register');
    }

    /**
     * Handle registration.
     */
    public function register(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|max:255|unique:admins,email',
            'password' => 'required|string|min:8|confirmed',
        ], [
            'name.required'     => 'Nama lengkap wajib diisi.',
            'email.required'    => 'Alamat email wajib diisi.',
            'email.unique'      => 'Email ini telah digunakan.',
            'password.required' => 'Password wajib diisi.',
            'password.min'      => 'Password minimal 8 karakter.',
            'password.confirmed'=> 'Konfirmasi password tidak cocok.',
        ]);

        $admin = Admin::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'status'   => 'active',
        ]);

        AuditLogger::log(
            action: 'register',
            module: 'Auth',
            recordId: (string) $admin->id,
            description: "Pendaftaran admin baru {$admin->email}",
            newData: ['name' => $admin->name, 'email' => $admin->email]
        );

        return redirect()->route('admin.login')->with('success', 'Akun administrator berhasil dibuat. Silakan login.');
    }

    /**
     * Show login form.
     */
    public function showLogin()
    {
        if (Auth::guard('admin')->check()) {
            return redirect()->route('admin.panel');
        }
        return view('admin.login');
    }

    /**
     * Handle login with Rate Limiting, Audit Logging & Login History tracking.
     */
    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        $throttleKey = 'admin_login:' . Str::lower($request->input('email')) . '|' . $request->ip();

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            
            LoginHistory::create([
                'admin_id'   => null,
                'email'      => $request->input('email'),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'status'     => 'failed',
                'created_at' => now(),
            ]);

            return back()->with('error', "Terlalu banyak percobaan login. Silakan coba lagi dalam {$seconds} detik.");
        }

        $credentials = $request->only('email', 'password');

        if (Auth::guard('admin')->attempt($credentials, $request->boolean('remember'))) {
            $admin = Auth::guard('admin')->user();

            // Check if admin account is active
            if ($admin->status !== 'active') {
                Auth::guard('admin')->logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                LoginHistory::create([
                    'admin_id'   => $admin->id,
                    'email'      => $admin->email,
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                    'status'     => 'failed',
                    'created_at' => now(),
                ]);

                AuditLogger::log(
                    action: 'failed_login',
                    module: 'Auth',
                    recordId: (string) $admin->id,
                    description: "Percobaan login pada akun yang nonaktif ({$admin->email})"
                );

                return back()->with('error', 'Email atau password tidak valid.');
            }

            // Clear Rate Limiter on success
            RateLimiter::clear($throttleKey);

            // Update login timestamps
            $admin->timestamps = false;
            $admin->last_login_at = now();
            $admin->last_login_ip = $request->ip();
            $admin->last_activity_at = now();
            $admin->save();

            // Regenerate session ID for security
            $request->session()->regenerate();

            // Record Login History & Audit Log
            LoginHistory::create([
                'admin_id'   => $admin->id,
                'email'      => $admin->email,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'status'     => 'success',
                'created_at' => now(),
            ]);

            AuditLogger::log(
                action: 'login',
                module: 'Auth',
                recordId: (string) $admin->id,
                description: "Administrator {$admin->name} ({$admin->email}) berhasil login"
            );

            return redirect()->intended(route('admin.panel'));
        }

        // Login failed
        RateLimiter::hit($throttleKey, 60);

        LoginHistory::create([
            'admin_id'   => null,
            'email'      => $request->input('email'),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'status'     => 'failed',
            'created_at' => now(),
        ]);

        AuditLogger::log(
            action: 'failed_login',
            module: 'Auth',
            description: "Gagal login untuk email: " . $request->input('email')
        );

        // Generic error message for security (does not disclose if email exists)
        return back()->with('error', 'Email atau password tidak valid.')->withInput($request->only('email'));
    }

    /**
     * Show Admin Dashboard.
     */
    public function panel()
    {
        return app(\App\Http\Controllers\Admin\PanelController::class)->index();
    }

    /**
     * Handle Logout.
     */
    public function logout(Request $request)
    {
        $admin = Auth::guard('admin')->user();

        if ($admin) {
            LoginHistory::create([
                'admin_id'   => $admin->id,
                'email'      => $admin->email,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'status'     => 'logout',
                'created_at' => now(),
            ]);

            AuditLogger::log(
                action: 'logout',
                module: 'Auth',
                recordId: (string) $admin->id,
                description: "Administrator {$admin->name} logout"
            );
        }

        Auth::guard('admin')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login')->with('success', 'Anda telah berhasil keluar.');
    }
}