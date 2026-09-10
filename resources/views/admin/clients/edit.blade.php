@extends('layouts.admin')

@section('title', 'Edit Data Klien')

@section('content')

<div class="max-w-4xl mx-auto">
  
  <div class="mb-6 flex items-center justify-between">
    <div>
      <h1 class="text-xl font-bold text-slate-900">Edit Data Klien</h1>
      <p class="text-xs text-slate-500">Perbarui informasi kontak & profil mitra {{ $client->name }}</p>
    </div>
    <a href="{{ route('admin.clients.index') }}" class="px-3.5 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold transition-colors flex items-center space-x-1.5">
      <i class="fa-solid fa-arrow-left text-xs"></i>
      <span>Kembali</span>
    </a>
  </div>

  <div class="bg-white rounded-2xl p-6 sm:p-8 shadow-card border border-slate-100">
    <form action="{{ route('admin.clients.update', $client) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
      @csrf
      @method('PUT')

      <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
        
        <!-- Nama -->
        <div>
          <label for="name" class="block text-xs font-semibold text-slate-700 mb-1.5">Nama Klien / Kontak <span class="text-rose-500">*</span></label>
          <input 
            id="name"
            type="text" 
            name="name" 
            value="{{ old('name', $client->name) }}" 
            class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 placeholder-slate-400 focus:bg-white focus:border-[#21C9A4] focus:ring-2 focus:ring-[#21C9A4]/20 outline-hidden transition-all duration-200"
            required
          >
        </div>

        <!-- Perusahaan -->
        <div>
          <label for="company" class="block text-xs font-semibold text-slate-700 mb-1.5">Nama Perusahaan / Organisasi</label>
          <input 
            id="company"
            type="text" 
            name="company" 
            value="{{ old('company', $client->company) }}" 
            class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 placeholder-slate-400 focus:bg-white focus:border-[#21C9A4] focus:ring-2 focus:ring-[#21C9A4]/20 outline-hidden transition-all duration-200"
          >
        </div>

        <!-- Email -->
        <div>
          <label for="email" class="block text-xs font-semibold text-slate-700 mb-1.5">Email Klien</label>
          <input 
            id="email"
            type="email" 
            name="email" 
            value="{{ old('email', $client->email) }}" 
            class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 placeholder-slate-400 focus:bg-white focus:border-[#21C9A4] focus:ring-2 focus:ring-[#21C9A4]/20 outline-hidden transition-all duration-200"
          >
        </div>

        <!-- Telepon -->
        <div>
          <label for="phone" class="block text-xs font-semibold text-slate-700 mb-1.5">Nomor Telepon / WhatsApp</label>
          <input 
            id="phone"
            type="text" 
            name="phone" 
            value="{{ old('phone', $client->phone) }}" 
            class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 placeholder-slate-400 focus:bg-white focus:border-[#21C9A4] focus:ring-2 focus:ring-[#21C9A4]/20 outline-hidden transition-all duration-200"
          >
        </div>

        <!-- Status -->
        <div>
          <label for="status" class="block text-xs font-semibold text-slate-700 mb-1.5">Status Kerjasama <span class="text-rose-500">*</span></label>
          <select 
            id="status"
            name="status" 
            class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:bg-white focus:border-[#21C9A4] focus:ring-2 focus:ring-[#21C9A4]/20 outline-hidden transition-all duration-200"
            required
          >
            <option value="active" {{ old('status', $client->status) === 'active' ? 'selected' : '' }}>Aktif</option>
            <option value="inactive" {{ old('status', $client->status) === 'inactive' ? 'selected' : '' }}>Tidak Aktif</option>
          </select>
        </div>

        <!-- Logo Saat Ini & Upload -->
        <div>
          <label class="block text-xs font-semibold text-slate-700 mb-1.5">Logo Perusahaan</label>
          @if($client->logo)
            <div class="flex items-center space-x-3 mb-2 p-2 bg-slate-50 border border-slate-200 rounded-xl">
              <img src="{{ Storage::url($client->logo) }}" alt="Preview" class="w-10 h-10 object-cover rounded-lg border border-slate-200">
              <span class="text-[11px] text-slate-400">Logo saat ini. Unggah file baru untuk mengganti.</span>
            </div>
          @endif
          <input 
            type="file" 
            name="logo" 
            accept="image/*"
            class="w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-[#14433B] file:text-white hover:file:bg-[#0B443C] file:cursor-pointer border border-slate-200 rounded-xl bg-slate-50 p-1"
          >
        </div>

        <!-- Alamat -->
        <div class="md:col-span-2">
          <label for="address" class="block text-xs font-semibold text-slate-700 mb-1.5">Alamat Lengkap Perusahaan</label>
          <textarea 
            id="address"
            name="address" 
            rows="3" 
            class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 placeholder-slate-400 focus:bg-white focus:border-[#21C9A4] focus:ring-2 focus:ring-[#21C9A4]/20 outline-hidden transition-all duration-200"
          >{{ old('address', $client->address) }}</textarea>
        </div>

        <!-- Catatan -->
        <div class="md:col-span-2">
          <label for="notes" class="block text-xs font-semibold text-slate-700 mb-1.5">Catatan Tambahan (Internal)</label>
          <textarea 
            id="notes"
            name="notes" 
            rows="3" 
            class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 placeholder-slate-400 focus:bg-white focus:border-[#21C9A4] focus:ring-2 focus:ring-[#21C9A4]/20 outline-hidden transition-all duration-200"
          >{{ old('notes', $client->notes) }}</textarea>
        </div>

      </div>

      <!-- Action Buttons -->
      <div class="pt-6 border-t border-slate-100 flex items-center justify-end space-x-3">
        <a href="{{ route('admin.clients.index') }}" class="px-5 py-2.5 rounded-xl border border-slate-200 text-slate-700 text-xs font-semibold hover:bg-slate-50 transition-colors">
          Batal
        </a>
        <button type="submit" class="px-6 py-2.5 rounded-xl bg-[#14433B] hover:bg-[#0B443C] text-white text-xs font-semibold shadow-md shadow-[#14433B]/20 transition-all flex items-center space-x-2">
          <i class="fa-solid fa-floppy-disk text-xs"></i>
          <span>Perbarui Data Klien</span>
        </button>
      </div>

    </form>
  </div>

</div>

@endsection
