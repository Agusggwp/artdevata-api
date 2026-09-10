<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    /**
     * Display a listing of admin users.
     */
    public function index()
    {
        $users = Admin::latest()->paginate(10);
        return view('admin.users.index', compact('users'));
    }

    /**
     * Show the form for creating a new admin user.
     */
    public function create()
    {
        return view('admin.users.create');
    }

    /**
     * Store a newly created admin user in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|max:255|unique:admins,email',
            'password' => 'required|string|min:6|confirmed',
        ], [
            'name.required'     => 'Nama lengkap wajib diisi.',
            'email.required'    => 'Alamat email wajib diisi.',
            'email.email'       => 'Format email tidak valid.',
            'email.unique'      => 'Email ini sudah terdaftar sebagai admin.',
            'password.required' => 'Password wajib diisi.',
            'password.min'      => 'Password minimal 6 karakter.',
            'password.confirmed'=> 'Konfirmasi password tidak cocok.',
        ]);

        Admin::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('admin.users.index')->with('success', 'Akun Administrator baru berhasil ditambahkan!');
    }

    /**
     * Show the form for editing the specified admin user.
     */
    public function edit(Admin $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    /**
     * Update the specified admin user in storage.
     */
    public function update(Request $request, Admin $user)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|max:255|unique:admins,email,' . $user->id,
            'password' => 'nullable|string|min:6|confirmed',
        ], [
            'name.required'     => 'Nama lengkap wajib diisi.',
            'email.required'    => 'Alamat email wajib diisi.',
            'email.unique'      => 'Email ini sudah digunakan oleh admin lain.',
            'password.min'      => 'Password baru minimal 6 karakter.',
            'password.confirmed'=> 'Konfirmasi password baru tidak cocok.',
        ]);

        $data = [
            'name'  => $request->name,
            'email' => $request->email,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return redirect()->route('admin.users.index')->with('success', 'Data Administrator berhasil diperbarui!');
    }

    /**
     * Remove the specified admin user from storage.
     */
    public function destroy(Admin $user)
    {
        if (Auth::guard('admin')->id() === $user->id) {
            return redirect()->back()->with('error', 'Anda tidak dapat menghapus akun Anda sendiri yang sedang aktif!');
        }

        $user->delete();

        return redirect()->route('admin.users.index')->with('success', 'Akun Administrator berhasil dihapus!');
    }
}
