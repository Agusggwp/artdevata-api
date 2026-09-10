<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\Role;
use App\Services\AuditLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Display a listing of admin users.
     */
    public function index(Request $request)
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

        $users = $query->latest()->paginate(10);
        $roles = Role::all();

        return view('admin.users.index', compact('users', 'roles'));
    }

    /**
     * Show the form for creating a new admin user.
     */
    public function create()
    {
        $roles = Role::all();
        return view('admin.users.create', compact('roles'));
    }

    /**
     * Store a newly created admin user in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|max:255|unique:admins,email',
            'password' => 'required|string|min:8|confirmed',
            'role_id'  => 'required|exists:roles,id',
            'status'   => 'required|in:active,inactive,suspended',
        ], [
            'name.required'     => 'Nama lengkap wajib diisi.',
            'email.required'    => 'Alamat email wajib diisi.',
            'email.email'       => 'Format email tidak valid.',
            'email.unique'      => 'Email ini sudah terdaftar sebagai admin.',
            'password.required' => 'Password wajib diisi.',
            'password.min'      => 'Password minimal 8 karakter.',
            'password.confirmed'=> 'Konfirmasi password tidak cocok.',
            'role_id.required'  => 'Peran / Role administrator wajib dipilih.',
            'status.required'   => 'Status akun wajib dipilih.',
        ]);

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
            description: "Menambahkan administrator baru: {$admin->name} ({$admin->email})",
            newData: ['name' => $admin->name, 'email' => $admin->email, 'role_id' => $admin->role_id, 'status' => $admin->status]
        );

        return redirect()->route('admin.users.index')->with('success', 'Akun Administrator baru berhasil ditambahkan.');
    }

    /**
     * Display detailed admin profile, activity, & login history.
     */
    public function show(Admin $user)
    {
        $user->load('role', 'auditLogs', 'loginHistories');
        $recentLogs = $user->auditLogs()->latest('created_at')->take(10)->get();
        $recentLogins = $user->loginHistories()->latest('created_at')->take(10)->get();

        return view('admin.users.show', compact('user', 'recentLogs', 'recentLogins'));
    }

    /**
     * Show the form for editing the specified admin user.
     */
    public function edit(Admin $user)
    {
        $roles = Role::all();
        return view('admin.users.edit', compact('user', 'roles'));
    }

    /**
     * Update the specified admin user in storage.
     */
    public function update(Request $request, Admin $user)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|max:255|unique:admins,email,' . $user->id,
            'password' => 'nullable|string|min:8|confirmed',
            'role_id'  => 'required|exists:roles,id',
            'status'   => 'required|in:active,inactive,suspended',
        ], [
            'name.required'     => 'Nama lengkap wajib diisi.',
            'email.required'    => 'Alamat email wajib diisi.',
            'email.unique'      => 'Email ini sudah digunakan oleh admin lain.',
            'password.min'      => 'Password baru minimal 8 karakter.',
            'password.confirmed'=> 'Konfirmasi password baru tidak cocok.',
            'role_id.required'  => 'Role administrator wajib dipilih.',
        ]);

        // Safety Check: Prevent deactivating/suspending self
        if (Auth::guard('admin')->id() === $user->id && $request->status !== 'active') {
            return redirect()->back()->with('error', 'Anda tidak dapat menonaktifkan atau menangguhkan akun Anda sendiri yang sedang aktif.');
        }

        // Safety Check: Prevent deactivating the last active Super Admin
        if ($user->isSuperAdmin() && $request->status !== 'active') {
            $superAdminCount = Admin::whereHas('role', fn($q) => $q->where('slug', 'super-admin'))
                                    ->where('status', 'active')
                                    ->count();
            if ($superAdminCount <= 1) {
                return redirect()->back()->with('error', 'Sistem harus memiliki minimal satu Super Admin yang aktif.');
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
            description: "Memperbarui akun administrator: {$user->name} ({$user->email})",
            oldData: $oldData,
            newData: array_diff_key($data, ['password' => ''])
        );

        return redirect()->route('admin.users.index')->with('success', 'Data Administrator berhasil diperbarui.');
    }

    /**
     * Remove the specified admin user from storage.
     */
    public function destroy(Admin $user)
    {
        $currentAdminId = Auth::guard('admin')->id();

        if ($currentAdminId === $user->id) {
            return redirect()->back()->with('error', 'Anda tidak dapat menghapus akun Anda sendiri yang sedang aktif.');
        }

        if ($user->isSuperAdmin()) {
            $superAdminCount = Admin::whereHas('role', fn($q) => $q->where('slug', 'super-admin'))->count();
            if ($superAdminCount <= 1) {
                return redirect()->back()->with('error', 'Super Admin terakhir tidak dapat dihapus.');
            }
        }

        AuditLogger::log(
            action: 'delete',
            module: 'Admins',
            recordId: (string) $user->id,
            description: "Menghapus administrator: {$user->name} ({$user->email})",
            oldData: ['name' => $user->name, 'email' => $user->email, 'role' => $user->role?->name]
        );

        $user->delete();

        return redirect()->route('admin.users.index')->with('success', 'Akun Administrator berhasil dihapus.');
    }

    /**
     * Force logout specified administrator.
     */
    public function forceLogout(Admin $user)
    {
        if (Auth::guard('admin')->id() === $user->id) {
            return redirect()->back()->with('error', 'Anda tidak dapat melakukan force logout pada sesi Anda sendiri.');
        }

        $user->timestamps = false;
        $user->remember_token = null;
        $user->save();

        AuditLogger::log(
            action: 'force_logout',
            module: 'Admins',
            recordId: (string) $user->id,
            description: "Melakukan force logout pada administrator {$user->name} ({$user->email})"
        );

        return redirect()->back()->with('success', "Administrator {$user->name} berhasil di-force logout.");
    }
}
