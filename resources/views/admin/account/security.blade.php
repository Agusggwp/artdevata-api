@extends('layouts.admin')

@section('title', 'Keamanan Akun Saya')

@section('content')

<!-- Header Bar -->
<div class="flex items-center justify-between mb-6">
  <div>
    <h1 class="text-xl font-bold text-slate-900">Keamanan Akun Saya (My Account Security)</h1>
    <p class="text-xs text-slate-700 font-medium">Ubah password akun Anda, kelola sesi aktif, dan atur proteksi akses</p>
  </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

  <!-- Change Password Card -->
  <div class="bg-white rounded-2xl p-6 sm:p-8 shadow-card border border-slate-200">
    <h2 class="text-sm font-bold text-slate-900 mb-1 flex items-center space-x-2">
      <i class="fa-solid fa-[#14433B] fa-key"></i>
      <span>Ubah Password Akun</span>
    </h2>
    <p class="text-xs text-slate-600 mb-6">Pastikan password baru Anda kuat, unik, dan minimal 8 karakter.</p>

    <form method="POST" action="{{ route('admin.account.security.update-password') }}" class="space-y-4">
      @csrf
      @method('PUT')

      <!-- Current Password -->
      <div>
        <label for="current_password" class="block text-xs font-bold text-slate-800 mb-1.5">Password Saat Ini <span class="text-rose-500">*</span></label>
        <div class="relative">
          <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-[#14433B] text-xs">
            <i class="fa-solid fa-lock"></i>
          </div>
          <input 
            id="current_password"
            type="password" 
            name="current_password" 
            class="w-full pl-9 pr-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-xs text-slate-900 font-bold focus:bg-white focus:border-[#14433B] outline-hidden transition-all"
            required
          >
        </div>
        @error('current_password')
          <p class="text-rose-600 text-[11px] font-semibold mt-1">{{ $message }}</p>
        @enderror
      </div>

      <!-- New Password -->
      <div>
        <label for="password" class="block text-xs font-bold text-slate-800 mb-1.5">Password Baru <span class="text-rose-500">*</span></label>
        <div class="relative">
          <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-[#14433B] text-xs">
            <i class="fa-solid fa-key"></i>
          </div>
          <input 
            id="password"
            type="password" 
            name="password" 
            class="w-full pl-9 pr-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-xs text-slate-900 font-bold focus:bg-white focus:border-[#14433B] outline-hidden transition-all"
            placeholder="Minimal 8 karakter"
            required
          >
        </div>
        @error('password')
          <p class="text-rose-600 text-[11px] font-semibold mt-1">{{ $message }}</p>
        @enderror
      </div>

      <!-- Confirm New Password -->
      <div>
        <label for="password_confirmation" class="block text-xs font-bold text-slate-800 mb-1.5">Konfirmasi Password Baru <span class="text-rose-500">*</span></label>
        <div class="relative">
          <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-[#14433B] text-xs">
            <i class="fa-solid fa-key"></i>
          </div>
          <input 
            id="password_confirmation"
            type="password" 
            name="password_confirmation" 
            class="w-full pl-9 pr-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-xs text-slate-900 font-bold focus:bg-white focus:border-[#14433B] outline-hidden transition-all"
            placeholder="Ulangi password baru"
            required
          >
        </div>
      </div>

      <div class="pt-3">
        <button type="submit" class="w-full px-5 py-2.5 rounded-xl bg-[#14433B] hover:bg-[#0B443C] text-white text-xs font-extrabold shadow-md transition-all flex items-center justify-center space-x-2">
          <i class="fa-solid fa-shield-halved text-xs"></i>
          <span>Perbarui Password</span>
        </button>
      </div>

    </form>
  </div>

  <!-- Active Sessions & Logout Other Devices Card -->
  <div class="bg-white rounded-2xl p-6 sm:p-8 shadow-card border border-slate-200 flex flex-col justify-between">
    <div>
      <h2 class="text-sm font-bold text-slate-900 mb-1 flex items-center space-x-2">
        <i class="fa-solid fa-[#14433B] fa-desktop"></i>
        <span>Sesi Perangkat Aktif (Active Sessions)</span>
      </h2>
      <p class="text-xs text-slate-600 mb-6">Informasi sesi perangkat yang saat ini terhubung dengan akun Anda.</p>

      <!-- Active Device Item -->
      <div class="p-4 rounded-xl bg-slate-50 border border-slate-200 mb-6 flex items-center justify-between">
        <div class="flex items-center space-x-3">
          <div class="w-10 h-10 rounded-xl bg-[#14433B]/10 text-[#14433B] flex items-center justify-center text-lg font-bold">
            <i class="fa-solid fa-laptop"></i>
          </div>
          <div>
            <div class="text-xs font-bold text-slate-900">Perangkat Saat Ini</div>
            <div class="text-[11px] text-slate-500 font-mono">IP: {{ Request::ip() }}</div>
          </div>
        </div>

        <span class="px-2.5 py-0.5 rounded-full bg-emerald-100 text-emerald-800 text-[10px] font-extrabold border border-emerald-300">
          Sesi Ini Aktif
        </span>
      </div>

      <div class="p-4 rounded-xl bg-amber-50 border border-amber-200 text-xs text-amber-900">
        <h4 class="font-bold mb-1 flex items-center space-x-1.5">
          <i class="fa-solid fa-triangle-exclamation text-amber-600"></i>
          <span>Keluar Dari Seluruh Perangkat Lain</span>
        </h4>
        <p class="text-[11px] text-amber-800 leading-relaxed mb-3">Jika Anda curiga akun Anda diakses dari perangkat yang tidak dikenal, Anda dapat mengakhiri seluruh sesi di perangkat lain secara instan.</p>

        <form method="POST" action="{{ route('admin.account.security.logout-others') }}" class="space-y-3">
          @csrf
          <div>
            <input 
              type="password" 
              name="password" 
              class="w-full px-3 py-2 bg-white border border-amber-300 rounded-xl text-xs text-slate-900 font-bold placeholder-slate-400 focus:border-[#14433B] outline-hidden"
              placeholder="Masukkan password Anda untuk konfirmasi"
              required
            >
          </div>
          <button type="submit" class="px-4 py-2 rounded-xl bg-amber-600 hover:bg-amber-700 text-white text-xs font-bold shadow-xs transition-colors">
            Keluarkan Perangkat Lain
          </button>
        </form>
      </div>

    </div>
  </div>

</div>

@endsection
