@extends('layouts.admin')

@section('title', 'Tambah Portfolio')

@section('content')

<div class="max-w-4xl mx-auto">
  
  <div class="mb-6 flex items-center justify-between">
    <div>
      <h1 class="text-xl font-bold text-slate-900">Tambah Portfolio Baru</h1>
      <p class="text-xs text-slate-500">Publikasikan karya dan hasil pengerjaan proyek ARTDEVATA</p>
    </div>
    <a href="{{ route('admin.portfolios.index') }}" class="px-3.5 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold transition-colors flex items-center space-x-1.5">
      <i class="fa-solid fa-arrow-left text-xs"></i>
      <span>Kembali</span>
    </a>
  </div>

  <div class="bg-white rounded-2xl p-6 sm:p-8 shadow-card border border-slate-100">
    <form method="POST" action="{{ route('admin.portfolios.store') }}" enctype="multipart/form-data" class="space-y-6">
      @csrf

      <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
        
        <!-- Judul -->
        <div class="md:col-span-2">
          <label for="title" class="block text-xs font-semibold text-slate-700 mb-1.5">Judul Portfolio / Nama Proyek <span class="text-rose-500">*</span></label>
          <input 
            id="title"
            type="text" 
            name="title" 
            value="{{ old('title') }}" 
            placeholder="Contoh: Platform E-Commerce ArtDevata Store"
            class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 placeholder-slate-400 focus:bg-white focus:border-[#21C9A4] focus:ring-2 focus:ring-[#21C9A4]/20 outline-hidden transition-all duration-200"
            required
          >
        </div>

        <!-- Deskripsi -->
        <div class="md:col-span-2">
          <label for="description" class="block text-xs font-semibold text-slate-700 mb-1.5">Deskripsi Proyek <span class="text-rose-500">*</span></label>
          <textarea 
            id="description"
            name="description" 
            rows="4" 
            placeholder="Jelaskan ringkasan proyek, konsep, serta dampak solusinya..."
            class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 placeholder-slate-400 focus:bg-white focus:border-[#21C9A4] focus:ring-2 focus:ring-[#21C9A4]/20 outline-hidden transition-all duration-200"
            required
          >{{ old('description') }}</textarea>
        </div>

        <!-- Kategori -->
        <div>
          <label for="category" class="block text-xs font-semibold text-slate-700 mb-1.5">Kategori Proyek</label>
          <input 
            id="category"
            type="text" 
            name="category" 
            value="{{ old('category') }}" 
            placeholder="Contoh: Web App, Mobile, Design System"
            class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 placeholder-slate-400 focus:bg-white focus:border-[#21C9A4] focus:ring-2 focus:ring-[#21C9A4]/20 outline-hidden transition-all duration-200"
          >
        </div>

        <!-- Client -->
        <div>
          <label for="client" class="block text-xs font-semibold text-slate-700 mb-1.5">Nama Client / Mitra</label>
          <input 
            id="client"
            type="text" 
            name="client" 
            value="{{ old('client') }}" 
            placeholder="Contoh: PT Medika Nusantara"
            class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 placeholder-slate-400 focus:bg-white focus:border-[#21C9A4] focus:ring-2 focus:ring-[#21C9A4]/20 outline-hidden transition-all duration-200"
          >
        </div>

        <!-- Tanggal -->
        <div>
          <label for="date" class="block text-xs font-semibold text-slate-700 mb-1.5">Tanggal Selesai Proyek</label>
          <input 
            id="date"
            type="text" 
            name="date" 
            value="{{ old('date') }}" 
            placeholder="Contoh: 2025-11-30"
            class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 placeholder-slate-400 focus:bg-white focus:border-[#21C9A4] focus:ring-2 focus:ring-[#21C9A4]/20 outline-hidden transition-all duration-200"
          >
        </div>

        <!-- Durasi -->
        <div>
          <label for="duration" class="block text-xs font-semibold text-slate-700 mb-1.5">Durasi Pengerjaan</label>
          <input 
            id="duration"
            type="text" 
            name="duration" 
            value="{{ old('duration') }}" 
            placeholder="Contoh: 2 Bulan 1 Minggu"
            class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 placeholder-slate-400 focus:bg-white focus:border-[#21C9A4] focus:ring-2 focus:ring-[#21C9A4]/20 outline-hidden transition-all duration-200"
          >
        </div>

        <!-- Challenge -->
        <div>
          <label for="challenge" class="block text-xs font-semibold text-slate-700 mb-1.5">Tantangan (Challenge)</label>
          <textarea 
            id="challenge"
            name="challenge" 
            rows="3" 
            placeholder="Tantangan teknis atau bisnis yang dihadapi..."
            class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 placeholder-slate-400 focus:bg-white focus:border-[#21C9A4] focus:ring-2 focus:ring-[#21C9A4]/20 outline-hidden transition-all duration-200"
          >{{ old('challenge') }}</textarea>
        </div>

        <!-- Solution -->
        <div>
          <label for="solution" class="block text-xs font-semibold text-slate-700 mb-1.5">Solusi (Solution)</label>
          <textarea 
            id="solution"
            name="solution" 
            rows="3" 
            placeholder="Solusi dan inovasi yang diterapkan oleh ARTDEVATA..."
            class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 placeholder-slate-400 focus:bg-white focus:border-[#21C9A4] focus:ring-2 focus:ring-[#21C9A4]/20 outline-hidden transition-all duration-200"
          >{{ old('solution') }}</textarea>
        </div>

        <!-- Results -->
        <div class="md:col-span-2 space-y-2">
          <label class="block text-xs font-semibold text-slate-700">Hasil / Impact Pengerjaan</label>
          <input type="text" name="results[]" value="{{ old('results.0') }}" placeholder="Contoh: Meningkatkan transaksi online hingga 40%" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:bg-white focus:border-[#21C9A4] outline-hidden">
          <input type="text" name="results[]" value="{{ old('results.1') }}" placeholder="Contoh: 10,000+ pengguna aktif bulanan" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:bg-white focus:border-[#21C9A4] outline-hidden">
        </div>

        <!-- Technologies -->
        <div class="md:col-span-2 space-y-2">
          <label class="block text-xs font-semibold text-slate-700">Teknologi yang Digunakan (Tech Stack)</label>
          <input type="text" name="technologies[]" value="{{ old('technologies.0') }}" placeholder="Contoh: Laravel 12" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:bg-white focus:border-[#21C9A4] outline-hidden">
          <input type="text" name="technologies[]" value="{{ old('technologies.1') }}" placeholder="Contoh: Tailwind CSS & Vue.js" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:bg-white focus:border-[#21C9A4] outline-hidden">
        </div>

        <!-- Tautan -->
        <div class="md:col-span-2">
          <label for="link" class="block text-xs font-semibold text-slate-700 mb-1.5">Tautan Website Live / Demo (Opsional)</label>
          <input 
            id="link"
            type="url" 
            name="link" 
            value="{{ old('link') }}" 
            placeholder="https://contoh-client.com"
            class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 placeholder-slate-400 focus:bg-white focus:border-[#21C9A4] focus:ring-2 focus:ring-[#21C9A4]/20 outline-hidden transition-all duration-200"
          >
        </div>

        <!-- Gambar Utama -->
        <div>
          <label class="block text-xs font-semibold text-slate-700 mb-1.5">Gambar Cover Utama (Maks 2MB)</label>
          <input 
            type="file" 
            name="image" 
            accept="image/*"
            class="w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-[#14433B] file:text-white hover:file:bg-[#0B443C] file:cursor-pointer border border-slate-200 rounded-xl bg-slate-50 p-1"
          >
        </div>

        <!-- Gallery Images -->
        <div>
          <label class="block text-xs font-semibold text-slate-700 mb-1.5">Galeri Tangkapan Layar (Multiple Upload)</label>
          <input 
            type="file" 
            name="images[]" 
            accept="image/*" 
            multiple
            class="w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-[#14433B] file:text-white hover:file:bg-[#0B443C] file:cursor-pointer border border-slate-200 rounded-xl bg-slate-50 p-1"
          >
        </div>

      </div>

      <!-- Action Buttons -->
      <div class="pt-6 border-t border-slate-100 flex items-center justify-end space-x-3">
        <a href="{{ route('admin.portfolios.index') }}" class="px-5 py-2.5 rounded-xl border border-slate-200 text-slate-700 text-xs font-semibold hover:bg-slate-50 transition-colors">
          Batal
        </a>
        <button type="submit" class="px-6 py-2.5 rounded-xl bg-[#14433B] hover:bg-[#0B443C] text-white text-xs font-semibold shadow-md shadow-[#14433B]/20 transition-all flex items-center space-x-2">
          <i class="fa-solid fa-floppy-disk text-xs"></i>
          <span>Simpan Portfolio</span>
        </button>
      </div>

    </form>
  </div>

</div>

@endsection
