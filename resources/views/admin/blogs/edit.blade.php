@extends('layouts.admin')

@section('title', 'Edit Artikel Blog')

@section('content')

<div class="max-w-4xl mx-auto">
  
  <div class="mb-6 flex items-center justify-between">
    <div>
      <h1 class="text-xl font-bold text-slate-900">Edit Artikel Blog</h1>
      <p class="text-xs text-slate-500">Perbarui konten artikel {{ $blog->title }}</p>
    </div>
    <a href="{{ route('admin.blogs.index') }}" class="px-3.5 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold transition-colors flex items-center space-x-1.5">
      <i class="fa-solid fa-arrow-left text-xs"></i>
      <span>Kembali</span>
    </a>
  </div>

  <div class="bg-white rounded-2xl p-6 sm:p-8 shadow-card border border-slate-100">
    <form method="POST" action="{{ route('admin.blogs.update', $blog) }}" enctype="multipart/form-data" class="space-y-5">
      @csrf
      @method('PUT')

      <!-- Judul -->
      <div>
        <label for="title" class="block text-xs font-semibold text-slate-700 mb-1.5">Judul Artikel <span class="text-rose-500">*</span></label>
        <input 
          id="title"
          type="text" 
          name="title" 
          value="{{ old('title', $blog->title) }}" 
          class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 placeholder-slate-400 focus:bg-white focus:border-[#21C9A4] focus:ring-2 focus:ring-[#21C9A4]/20 outline-hidden transition-all duration-200"
          required
        >
      </div>

      <!-- Kategori -->
      <div>
        <label for="category" class="block text-xs font-semibold text-slate-700 mb-1.5">Kategori Blog</label>
        <input 
          id="category"
          type="text" 
          name="category" 
          value="{{ old('category', $blog->category) }}" 
          class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 placeholder-slate-400 focus:bg-white focus:border-[#21C9A4] focus:ring-2 focus:ring-[#21C9A4]/20 outline-hidden transition-all duration-200"
        >
      </div>

      <!-- Isi Artikel -->
      <div>
        <label for="content" class="block text-xs font-semibold text-slate-700 mb-1.5">Isi Artikel Blog <span class="text-rose-500">*</span></label>
        <textarea 
          id="content"
          name="content" 
          rows="10" 
          class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 placeholder-slate-400 focus:bg-white focus:border-[#21C9A4] focus:ring-2 focus:ring-[#21C9A4]/20 outline-hidden transition-all duration-200"
          required
        >{{ old('content', $blog->content) }}</textarea>
      </div>

      <!-- Gambar Saat Ini & Upload -->
      <div>
        <label class="block text-xs font-semibold text-slate-700 mb-1.5">Gambar / Cover Artikel</label>
        
        @if($blog->image)
          <div class="flex items-center space-x-4 mb-3 p-3 bg-slate-50 border border-slate-200 rounded-xl">
            <img src="{{ Storage::url($blog->image) }}" alt="Preview" class="w-20 h-20 object-cover rounded-lg border border-slate-200 shadow-xs">
            <div>
              <span class="text-xs font-semibold text-slate-700 block">Gambar Terpasang</span>
              <span class="text-[11px] text-slate-400">Unggah berkas baru di bawah jika ingin mengganti cover.</span>
            </div>
          </div>
        @endif

        <input 
          type="file" 
          name="image" 
          accept="image/*"
          class="w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-[#14433B] file:text-white hover:file:bg-[#0B443C] file:cursor-pointer border border-slate-200 rounded-xl bg-slate-50 p-1"
        >
      </div>

      <!-- Action Buttons -->
      <div class="pt-4 border-t border-slate-100 flex items-center justify-end space-x-3">
        <a href="{{ route('admin.blogs.index') }}" class="px-5 py-2.5 rounded-xl border border-slate-200 text-slate-700 text-xs font-semibold hover:bg-slate-50 transition-colors">
          Batal
        </a>
        <button type="submit" class="px-6 py-2.5 rounded-xl bg-[#14433B] hover:bg-[#0B443C] text-white text-xs font-semibold shadow-md shadow-[#14433B]/20 transition-all flex items-center space-x-2">
          <i class="fa-solid fa-floppy-disk text-xs"></i>
          <span>Perbarui Artikel</span>
        </button>
      </div>

    </form>
  </div>

</div>

@endsection