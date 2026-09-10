@extends('layouts.admin')

@section('title', 'Tambah Layanan Baru')

@section('content')

<div class="max-w-3xl mx-auto">
  
  <div class="mb-6 flex items-center justify-between">
    <div>
      <h1 class="text-xl font-bold text-slate-900">Tambah Layanan Baru</h1>
      <p class="text-xs text-slate-500">Buat paket layanan baru untuk penawaran jasa ARTDEVATA</p>
    </div>
    <a href="{{ route('admin.services.index') }}" class="px-3.5 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold transition-colors flex items-center space-x-1.5">
      <i class="fa-solid fa-arrow-left text-xs"></i>
      <span>Kembali</span>
    </a>
  </div>

  <div class="bg-white rounded-2xl p-6 sm:p-8 shadow-card border border-slate-100">
    <form method="POST" action="{{ route('admin.services.store') }}" enctype="multipart/form-data" class="space-y-5">
      @csrf

      <!-- Title -->
      <div>
        <label for="title" class="block text-xs font-semibold text-slate-700 mb-1.5">Judul Layanan <span class="text-rose-500">*</span></label>
        <input 
          id="title"
          type="text" 
          name="title"
          value="{{ old('title') }}"
          class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 placeholder-slate-400 focus:bg-white focus:border-[#21C9A4] focus:ring-2 focus:ring-[#21C9A4]/20 outline-hidden transition-all duration-200"
          placeholder="Contoh: Pengembangan Web Application SaaS"
          required
        >
      </div>

      <!-- Description -->
      <div>
        <label for="description" class="block text-xs font-semibold text-slate-700 mb-1.5">Deskripsi Layanan <span class="text-rose-500">*</span></label>
        <textarea 
          id="description"
          name="description" 
          rows="4"
          class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 placeholder-slate-400 focus:bg-white focus:border-[#21C9A4] focus:ring-2 focus:ring-[#21C9A4]/20 outline-hidden transition-all duration-200"
          placeholder="Jelaskan secara mendalam tentang layanan ini..."
          required
        >{{ old('description') }}</textarea>
      </div>

      <!-- Features (Dynamic) -->
      <div>
        <div class="flex items-center justify-between mb-1.5">
          <label class="block text-xs font-semibold text-slate-700">Fitur & Fasilitas Utama</label>
          <span class="text-[11px] text-slate-400">Dapat ditambahkan dinamis</span>
        </div>

        <div id="feature-list" class="space-y-2.5 mb-3">
          <!-- Features injected here -->
        </div>

        <button type="button" id="add-feature" class="px-3.5 py-2 rounded-xl bg-emerald-50 hover:bg-emerald-100 text-[#14433B] text-xs font-semibold transition-colors flex items-center space-x-1.5">
          <i class="fa-solid fa-plus text-xs text-[#21C9A4]"></i>
          <span>Tambah Poin Fitur</span>
        </button>
      </div>

      <!-- Image Upload -->
      <div>
        <label class="block text-xs font-semibold text-slate-700 mb-1.5">Gambar / Banner Layanan (Opsional)</label>
        <input 
          type="file" 
          name="image" 
          accept="image/*"
          class="w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-[#14433B] file:text-white hover:file:bg-[#0B443C] file:cursor-pointer border border-slate-200 rounded-xl bg-slate-50 p-1"
        >
      </div>

      <!-- Form Action Buttons -->
      <div class="pt-4 border-t border-slate-100 flex items-center justify-end space-x-3">
        <a href="{{ route('admin.services.index') }}" class="px-5 py-2.5 rounded-xl border border-slate-200 text-slate-700 text-xs font-semibold hover:bg-slate-50 transition-colors">
          Batal
        </a>
        <button type="submit" class="px-6 py-2.5 rounded-xl bg-[#14433B] hover:bg-[#0B443C] text-white text-xs font-semibold shadow-md shadow-[#14433B]/20 transition-all">
          Simpan Layanan
        </button>
      </div>

    </form>
  </div>

</div>

@endsection

@push('scripts')
<script>
  const featureList = document.getElementById("feature-list");
  const addFeatureBtn = document.getElementById("add-feature");

  function createFeatureInput(value = '') {
    const div = document.createElement("div");
    div.className = "flex items-center space-x-2";
    div.innerHTML = `
      <input type="text" name="features[]" value="${value}" placeholder="Contoh: Gratis Konsultasi 1 Bulan"
             class="flex-1 px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 placeholder-slate-400 focus:bg-white focus:border-[#21C9A4] focus:ring-2 focus:ring-[#21C9A4]/20 outline-hidden">
      <button type="button" class="remove-feature p-2 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-xl transition-colors">
        <i class="fa-solid fa-xmark text-sm"></i>
      </button>
    `;
    featureList.appendChild(div);
    div.querySelector(".remove-feature").addEventListener("click", () => div.remove());
  }

  addFeatureBtn.addEventListener("click", () => createFeatureInput());

  // Initial default row
  createFeatureInput();
</script>
@endpush
