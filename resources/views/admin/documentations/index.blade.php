@extends('layouts.admin')

@section('title', 'Dokumentasi Perusahaan')

@section('content')

<!-- Header Bar -->
<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
  <div>
    <h1 class="text-xl font-bold text-slate-900">Dokumentasi Perusahaan</h1>
    <p class="text-xs text-slate-700 font-medium">Kelola foto kegiatan, proyek, dan pencapaian operasional perusahaan</p>
  </div>

  <a href="{{ route('admin.documentations.create') }}" class="px-4 py-2.5 rounded-xl bg-[#14433B] hover:bg-[#0B443C] text-white text-xs font-extrabold shadow-md shadow-[#14433B]/20 transition-all flex items-center justify-center space-x-2 shrink-0">
    <i class="fa-solid fa-plus text-xs"></i>
    <span>Tambah Dokumentasi</span>
  </a>
</div>

<!-- Filter & Search Bar -->
<div class="bg-white rounded-2xl p-4 shadow-card border border-slate-200 mb-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
  <form method="GET" action="{{ route('admin.documentations.index') }}" class="flex-1 flex flex-col sm:flex-row items-center gap-3">
    <!-- Search Input -->
    <div class="relative w-full sm:w-72">
      <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400 text-xs">
        <i class="fa-solid fa-magnifying-glass"></i>
      </div>
      <input 
        type="text" 
        name="search" 
        value="{{ request('search') }}"
        class="w-full pl-9 pr-3.5 py-2 bg-slate-50 border border-slate-300 rounded-xl text-xs text-slate-900 font-medium placeholder-slate-400 focus:bg-white focus:border-[#14433B] focus:ring-2 focus:ring-[#14433B]/20 outline-hidden transition-all"
        placeholder="Cari judul atau kategori..."
      >
    </div>

    <!-- Category Filter -->
    <div class="w-full sm:w-48">
      <select name="category" onchange="this.form.submit()" class="w-full py-2 px-3 bg-slate-50 border border-slate-300 rounded-xl text-xs text-slate-900 font-semibold focus:bg-white focus:border-[#14433B] outline-hidden">
        <option value="">Semua Kategori</option>
        <option value="Adjustment" {{ request('category') == 'Adjustment' ? 'selected' : '' }}>Adjustment</option>
        <option value="Calibration" {{ request('category') == 'Calibration' ? 'selected' : '' }}>Calibration</option>
        <option value="Installation" {{ request('category') == 'Installation' ? 'selected' : '' }}>Installation</option>
        <option value="Result" {{ request('category') == 'Result' ? 'selected' : '' }}>Result</option>
      </select>
    </div>

    <button type="submit" class="w-full sm:w-auto px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-800 text-xs font-bold rounded-xl border border-slate-300 transition-colors">
      Filter
    </button>

    @if(request()->hasAny(['search', 'category']))
      <a href="{{ route('admin.documentations.index') }}" class="text-xs text-rose-600 hover:text-rose-800 font-semibold">
        Reset
      </a>
    @endif
  </form>

  <div class="text-xs font-semibold text-slate-600">
    Total: <span class="text-slate-900 font-bold">{{ $documentations->total() }}</span> Dokumentasi
  </div>
</div>

<!-- Documentation Grid -->
@if($documentations->count() > 0)
  <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
    @foreach($documentations as $doc)
      <div class="bg-white rounded-2xl overflow-hidden shadow-card border border-slate-200 flex flex-col group hover:shadow-lg transition-all duration-200">
        
        <!-- Image & Category Badge Container -->
        <div class="relative h-56 bg-slate-100 overflow-hidden">
          @if($doc->image)
            <img 
              src="{{ filter_var($doc->image, FILTER_VALIDATE_URL) ? $doc->image : asset('storage/' . $doc->image) }}" 
              alt="{{ $doc->title }}"
              class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
            >
          @else
            <div class="w-full h-full flex flex-col items-center justify-center text-slate-400 bg-slate-100">
              <i class="fa-solid fa-image text-3xl mb-1"></i>
              <span class="text-[11px] font-medium">Tanpa Gambar</span>
            </div>
          @endif

          <!-- Category Badge (Top-Left overlay) -->
          @if($doc->category)
            <div class="absolute top-3 left-3 px-3 py-1 rounded-full bg-[#21C9A4] text-[#14433B] text-[11px] font-bold shadow-md tracking-wide">
              {{ $doc->category }}
            </div>
          @endif

          <!-- Status Indicator Badge (Top-Right) -->
          <div class="absolute top-3 right-3">
            @if($doc->status === 'active')
              <span class="px-2.5 py-0.5 rounded-full bg-emerald-500/90 text-white text-[10px] font-extrabold shadow-xs backdrop-blur-xs">
                Aktif
              </span>
            @else
              <span class="px-2.5 py-0.5 rounded-full bg-slate-600/90 text-white text-[10px] font-extrabold shadow-xs backdrop-blur-xs">
                Draft
              </span>
            @endif
          </div>
        </div>

        <!-- Content Details -->
        <div class="p-4 flex-1 flex flex-col justify-between space-y-3">
          <div>
            <h3 class="text-sm font-bold text-slate-900 line-clamp-1 group-hover:text-[#14433B] transition-colors" title="{{ $doc->title }}">
              {{ $doc->title }}
            </h3>
            
            @if($doc->description)
              <p class="text-xs text-slate-600 mt-1 line-clamp-2 leading-relaxed">
                {{ $doc->description }}
              </p>
            @else
              <p class="text-xs text-slate-400 italic mt-1">Tidak ada deskripsi</p>
            @endif
          </div>

          <!-- Card Footer Info & Actions -->
          <div class="pt-3 border-t border-slate-100 flex items-center justify-between">
            <span class="text-[10px] text-slate-500 font-semibold">
              Urutan: #{{ $doc->sort_order }}
            </span>

            <div class="flex items-center space-x-1.5">
              <a 
                href="{{ route('admin.documentations.edit', $doc->id) }}" 
                class="w-8 h-8 rounded-lg bg-amber-50 text-amber-700 hover:bg-amber-100 flex items-center justify-center text-xs font-bold transition-colors border border-amber-200"
                title="Edit Dokumentasi"
              >
                <i class="fa-solid fa-pen-to-square"></i>
              </a>

              <button 
                type="button"
                onclick="confirmDelete('{{ route('admin.documentations.destroy', $doc->id) }}', 'Apakah Anda yakin ingin menghapus dokumentasi {{ e($doc->title) }}?')"
                class="w-8 h-8 rounded-lg bg-rose-50 text-rose-700 hover:bg-rose-100 flex items-center justify-center text-xs font-bold transition-colors border border-rose-200"
                title="Hapus Dokumentasi"
              >
                <i class="fa-solid fa-trash-can"></i>
              </button>
            </div>
          </div>

        </div>

      </div>
    @endforeach
  </div>

  <!-- Pagination -->
  <div class="mt-6">
    {{ $documentations->withQueryString()->links() }}
  </div>
@else
  <!-- Empty State -->
  <div class="bg-white rounded-2xl p-12 text-center shadow-card border border-slate-200 max-w-md mx-auto my-8">
    <div class="w-16 h-16 rounded-2xl bg-emerald-50 text-[#14433B] flex items-center justify-center mx-auto mb-4 border border-emerald-100">
      <i class="fa-solid fa-images text-2xl"></i>
    </div>
    <h3 class="text-base font-bold text-slate-900 mb-1">Belum Ada Dokumentasi</h3>
    <p class="text-xs text-slate-600 mb-6">Belum ada foto kegiatan atau dokumentasi perusahaan yang ditambahkan.</p>
    <a href="{{ route('admin.documentations.create') }}" class="px-5 py-2.5 rounded-xl bg-[#14433B] hover:bg-[#0B443C] text-white text-xs font-bold shadow-md shadow-[#14433B]/20 inline-flex items-center space-x-2">
      <i class="fa-solid fa-plus text-xs"></i>
      <span>Tambah Dokumentasi Pertama</span>
    </a>
  </div>
@endif

@endsection
