@extends('layouts.admin')

@section('title', 'Edit Dokumentasi Perusahaan')

@section('content')

<!-- Header Bar -->
<div class="flex items-center justify-between mb-6">
  <div>
    <h1 class="text-xl font-bold text-slate-900">Edit Dokumentasi Perusahaan</h1>
    <p class="text-xs text-slate-700 font-medium">Perbarui judul, kategori, deskripsi, atau ubah foto dokumentasi</p>
  </div>

  <a href="{{ route('admin.documentations.index') }}" class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-800 text-xs font-bold border border-slate-300 transition-colors flex items-center space-x-2">
    <i class="fa-solid fa-arrow-left"></i>
    <span>Kembali</span>
  </a>
</div>

<!-- Form Card -->
<div class="bg-white rounded-2xl p-6 sm:p-8 shadow-card border border-slate-200 max-w-3xl">
  
  <form method="POST" action="{{ route('admin.documentations.update', $documentation->id) }}" enctype="multipart/form-data" class="space-y-6">
    @csrf
    @method('PUT')

    <!-- Title Field -->
    <div>
      <label for="title" class="block text-xs font-bold text-slate-800 mb-1.5">Judul Dokumentasi <span class="text-rose-500">*</span></label>
      <input 
        id="title"
        type="text" 
        name="title" 
        value="{{ old('title', $documentation->title) }}"
        class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-xs text-slate-900 font-medium placeholder-slate-400 focus:bg-white focus:border-[#14433B] focus:ring-2 focus:ring-[#14433B]/20 outline-hidden transition-all"
        placeholder="Contoh: Instalasi Kamera CCTV Outdoor" 
        required 
        autofocus
      >
      @error('title')
        <p class="text-rose-600 text-[11px] font-semibold mt-1">{{ $message }}</p>
      @enderror
    </div>

    <!-- Category & Status Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
      
      <!-- Category Badge Input -->
      <div>
        <label for="category" class="block text-xs font-bold text-slate-800 mb-1.5">Kategori / Badge Label</label>
        <div class="relative">
          <input 
            id="category"
            type="text" 
            name="category" 
            value="{{ old('category', $documentation->category) }}"
            list="category-options"
            class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-xs text-slate-900 font-medium placeholder-slate-400 focus:bg-white focus:border-[#14433B] focus:ring-2 focus:ring-[#14433B]/20 outline-hidden transition-all"
            placeholder="Contoh: Installation" 
          >
          <datalist id="category-options">
            <option value="Adjustment">
            <option value="Calibration">
            <option value="Installation">
            <option value="Result">
          </datalist>
        </div>
        <p class="text-[10px] text-slate-500 mt-1">Muncul sebagai badge di pojok gambar</p>
      </div>

      <!-- Sort Order Input -->
      <div>
        <label for="sort_order" class="block text-xs font-bold text-slate-800 mb-1.5">Urutan Tampilan</label>
        <input 
          id="sort_order"
          type="number" 
          name="sort_order" 
          value="{{ old('sort_order', $documentation->sort_order) }}"
          class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-xs text-slate-900 font-medium placeholder-slate-400 focus:bg-white focus:border-[#14433B] focus:ring-2 focus:ring-[#14433B]/20 outline-hidden transition-all"
          placeholder="0" 
        >
        <p class="text-[10px] text-slate-500 mt-1">Angka lebih kecil tampil lebih dulu</p>
      </div>

      <!-- Status Input -->
      <div>
        <label for="status" class="block text-xs font-bold text-slate-800 mb-1.5">Status Publikasi <span class="text-rose-500">*</span></label>
        <select 
          id="status" 
          name="status"
          class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-xs text-slate-900 font-bold focus:bg-white focus:border-[#14433B] focus:ring-2 focus:ring-[#14433B]/20 outline-hidden transition-all"
        >
          <option value="active" {{ old('status', $documentation->status) == 'active' ? 'selected' : '' }}>Aktif (Tampil di Website)</option>
          <option value="inactive" {{ old('status', $documentation->status) == 'inactive' ? 'selected' : '' }}>Draft (Disembunyikan)</option>
        </select>
      </div>

    </div>

    <!-- Description Field -->
    <div>
      <label for="description" class="block text-xs font-bold text-slate-800 mb-1.5">Deskripsi Dokumentasi</label>
      <textarea 
        id="description" 
        name="description" 
        rows="3"
        class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-xs text-slate-900 font-medium placeholder-slate-400 focus:bg-white focus:border-[#14433B] focus:ring-2 focus:ring-[#14433B]/20 outline-hidden transition-all"
        placeholder="Tuliskan keterangan detail mengenai foto atau kegiatan dokumentasi ini..."
      >{{ old('description', $documentation->description) }}</textarea>
    </div>

    <!-- Image Upload Field with Existing Image Preview -->
    <div>
      <label for="image" class="block text-xs font-bold text-slate-800 mb-1.5">Ubah Foto Dokumentasi <span class="text-slate-500 font-normal">(Opsional)</span></label>
      
      <div class="flex flex-col sm:flex-row items-start sm:items-center space-y-3 sm:space-y-0 sm:space-x-4">
        <!-- Preview Box -->
        <div id="image-preview-container" class="w-32 h-32 rounded-xl bg-slate-100 border border-slate-300 flex flex-col items-center justify-center text-slate-400 overflow-hidden shrink-0 shadow-xs">
          @if($documentation->image)
            <img 
              src="{{ filter_var($documentation->image, FILTER_VALIDATE_URL) ? $documentation->image : asset('storage/' . $documentation->image) }}" 
              alt="{{ $documentation->title }}"
              class="w-full h-full object-cover"
            >
          @else
            <i class="fa-solid fa-cloud-arrow-up text-2xl mb-1"></i>
            <span class="text-[10px] font-medium">Tanpa Foto</span>
          @endif
        </div>

        <div class="flex-1 w-full">
          <input 
            id="image" 
            type="file" 
            name="image" 
            accept="image/*"
            onchange="previewImage(this)"
            class="block w-full text-xs text-slate-700 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-[#14433B] file:text-white hover:file:bg-[#0B443C] file:cursor-pointer transition-colors"
          >
          <p class="text-[11px] text-slate-500 mt-1.5">Pilih file baru jika ingin mengganti foto. Maksimal 4MB (JPG, PNG, WEBP).</p>
        </div>
      </div>
      @error('image')
        <p class="text-rose-600 text-[11px] font-semibold mt-1">{{ $message }}</p>
      @enderror
    </div>

    <!-- Action Buttons -->
    <div class="pt-4 border-t border-slate-200 flex items-center justify-end space-x-3">
      <a href="{{ route('admin.documentations.index') }}" class="px-5 py-2.5 rounded-xl border border-slate-300 text-slate-700 text-xs font-bold hover:bg-slate-50 transition-colors">
        Batal
      </a>
      <button type="submit" class="px-6 py-2.5 rounded-xl bg-[#14433B] hover:bg-[#0B443C] text-white text-xs font-extrabold shadow-md shadow-[#14433B]/20 transition-all flex items-center space-x-2">
        <i class="fa-solid fa-floppy-disk text-xs"></i>
        <span>Simpan Perubahan</span>
      </button>
    </div>

  </form>

</div>

@push('scripts')
<script>
  function previewImage(input) {
    const container = document.getElementById('image-preview-container');
    if (input.files && input.files[0]) {
      const reader = new FileReader();
      reader.onload = function(e) {
        container.innerHTML = `<img src="${e.target.result}" class="w-full h-full object-cover">`;
      }
      reader.readAsDataURL(input.files[0]);
    }
  }
</script>
@endpush

@endsection
