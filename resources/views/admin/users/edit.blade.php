@extends('layouts.admin')

@section('title', 'Edit Administrator')

@section('content')

<!-- Header Bar -->
<div class="flex items-center justify-between mb-6">
  <div>
    <h1 class="text-xl font-bold text-slate-900">Edit Akun Administrator</h1>
    <p class="text-xs text-slate-700 font-medium">Perbarui profil dan akses administrator ARTDEVATA</p>
  </div>

  <a href="{{ route('admin.users.index') }}" class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-800 text-xs font-bold border border-slate-300 transition-colors flex items-center space-x-2">
    <i class="fa-solid fa-arrow-left"></i>
    <span>Kembali</span>
  </a>
</div>

<!-- Form Card -->
<div class="bg-white rounded-2xl p-6 sm:p-8 shadow-card border border-slate-200 max-w-2xl">
  
  <form method="POST" action="{{ route('admin.users.update', $user->id) }}" class="space-y-6">
    @csrf
    @method('PUT')

    <!-- Name Field -->
    <div>
      <label for="name" class="block text-xs font-bold text-slate-800 mb-1.5">Nama Lengkap <span class="text-rose-500">*</span></label>
      <div class="relative">
        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-[#14433B] text-xs">
          <i class="fa-solid fa-user"></i>
        </div>
        <input 
          id="name"
          type="text" 
          name="name" 
          value="{{ old('name', $user->name) }}"
          class="w-full pl-9 pr-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-xs text-slate-900 font-medium placeholder-slate-500 focus:bg-white focus:border-[#14433B] focus:ring-2 focus:ring-[#14433B]/20 outline-hidden transition-all duration-200"
          placeholder="Contoh: Budi Santoso" 
          required 
          autofocus
        >
      </div>
      @error('name')
        <p class="text-rose-600 text-[11px] font-semibold mt-1">{{ $message }}</p>
      @enderror
    </div>

    <!-- Email Field -->
    <div>
      <label for="email" class="block text-xs font-bold text-slate-800 mb-1.5">Email Administrator <span class="text-rose-500">*</span></label>
      <div class="relative">
        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-[#14433B] text-xs">
          <i class="fa-solid fa-envelope"></i>
        </div>
        <input 
          id="email"
          type="email" 
          name="email" 
          value="{{ old('email', $user->email) }}"
          class="w-full pl-9 pr-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-xs text-slate-900 font-medium placeholder-slate-500 focus:bg-white focus:border-[#14433B] focus:ring-2 focus:ring-[#14433B]/20 outline-hidden transition-all duration-200"
          placeholder="budi@artdevata.com" 
          required
        >
      </div>
      @error('email')
        <p class="text-rose-600 text-[11px] font-semibold mt-1">{{ $message }}</p>
      @enderror
    </div>

    <!-- Password Field (Optional) -->
    <div class="border-t border-slate-200 pt-5">
      <div class="mb-4">
        <h2 class="text-xs font-bold text-slate-900">Ubah Password <span class="text-slate-500 font-normal">(Opsional)</span></h2>
        <p class="text-[11px] text-slate-600">Kosongkan bidang di bawah jika Anda tidak ingin mengganti password akun ini.</p>
      </div>

      <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
          <label for="password" class="block text-xs font-bold text-slate-800 mb-1.5">Password Baru</label>
          <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-[#14433B] text-xs">
              <i class="fa-solid fa-lock"></i>
            </div>
            <input 
              id="password"
              type="password" 
              name="password" 
              class="w-full pl-9 pr-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-xs text-slate-900 font-bold placeholder-slate-500 focus:bg-white focus:border-[#14433B] focus:ring-2 focus:ring-[#14433B]/20 outline-hidden transition-all duration-200"
              placeholder="Minimal 6 karakter" 
            >
          </div>
          @error('password')
            <p class="text-rose-600 text-[11px] font-semibold mt-1">{{ $message }}</p>
          @enderror
        </div>

        <div>
          <label for="password_confirmation" class="block text-xs font-bold text-slate-800 mb-1.5">Konfirmasi Password Baru</label>
          <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-[#14433B] text-xs">
              <i class="fa-solid fa-lock"></i>
            </div>
            <input 
              id="password_confirmation"
              type="password" 
              name="password_confirmation" 
              class="w-full pl-9 pr-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-xs text-slate-900 font-bold placeholder-slate-500 focus:bg-white focus:border-[#14433B] focus:ring-2 focus:ring-[#14433B]/20 outline-hidden transition-all duration-200"
              placeholder="Ulangi password baru" 
            >
          </div>
        </div>
      </div>
    </div>

    <!-- Action Buttons -->
    <div class="pt-4 border-t border-slate-200 flex items-center justify-end space-x-3">
      <a href="{{ route('admin.users.index') }}" class="px-5 py-2.5 rounded-xl border border-slate-300 text-slate-700 text-xs font-bold hover:bg-slate-50 transition-colors">
        Batal
      </a>
      <button type="submit" class="px-6 py-2.5 rounded-xl bg-[#14433B] hover:bg-[#0B443C] text-white text-xs font-extrabold shadow-md shadow-[#14433B]/20 transition-all flex items-center space-x-2">
        <i class="fa-solid fa-floppy-disk text-xs"></i>
        <span>Simpan Perubahan</span>
      </button>
    </div>

  </form>

</div>

@endsection
