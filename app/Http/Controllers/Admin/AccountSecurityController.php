<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\AuditLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AccountSecurityController extends Controller
{
    /**
     * Show My Account Security Page.
     */
    public function show()
    {
        $admin = Auth::guard('admin')->user();
        return view('admin.account.security', compact('admin'));
    }

    /**
     * Update Password.
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required|string',
            'password'         => 'required|string|min:8|confirmed|different:current_password',
        ], [
            'current_password.required' => 'Password saat ini wajib diisi.',
            'password.required'         => 'Password baru wajib diisi.',
            'password.min'              => 'Password baru minimal 8 karakter.',
            'password.confirmed'        => 'Konfirmasi password baru tidak cocok.',
            'password.different'        => 'Password baru harus berbeda dengan password saat ini.',
        ]);

        $admin = Auth::guard('admin')->user();

        if (!Hash::check($request->current_password, $admin->password)) {
            return back()->withErrors(['current_password' => 'Password saat ini tidak cocok.']);
        }

        $admin->timestamps = false;
        $admin->password = Hash::make($request->password);
        $admin->remember_token = null;
        $admin->save();

        AuditLogger::log(
            action: 'password_change',
            module: 'Account',
            recordId: (string) $admin->id,
            description: "Administrator {$admin->name} mengubah password akunnya"
        );

        return redirect()->route('admin.account.security')->with('success', 'Password akun Anda berhasil diperbarui.');
    }

    /**
     * Invalidate other sessions (Logout all other devices).
     */
    public function logoutOtherSessions(Request $request)
    {
        $request->validate([
            'password' => 'required|string',
        ], [
            'password.required' => 'Password wajib dimasukkan untuk mengonfirmasi tindakan ini.',
        ]);

        $admin = Auth::guard('admin')->user();

        if (!Hash::check($request->password, $admin->password)) {
            return back()->withErrors(['password' => 'Password yang Anda masukkan salah.']);
        }

        // Re-hash remember token to invalidate other remember-me tokens
        $admin->timestamps = false;
        $admin->remember_token = null;
        $admin->save();

        AuditLogger::log(
            action: 'force_logout',
            module: 'Account',
            recordId: (string) $admin->id,
            description: "Administrator {$admin->name} mengakhiri seluruh sesi perangkat lain"
        );

        return redirect()->route('admin.account.security')->with('success', 'Seluruh sesi di perangkat lain berhasil diakhiri.');
    }
}
