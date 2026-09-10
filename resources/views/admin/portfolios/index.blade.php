@extends('layouts.admin')

@section('title', 'Kelola Portfolio')

@section('content')

<!-- Header Bar -->
<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
  <div>
    <h1 class="text-xl font-bold text-slate-900">Daftar Portfolio & Project Showcase</h1>
    <p class="text-xs text-slate-700 font-medium">Kelola portofolio karya & hasil proyek ARTDEVATA</p>
  </div>
  
  <a href="{{ route('admin.portfolios.create') }}" class="px-4 py-2.5 rounded-xl bg-[#14433B] hover:bg-[#0B443C] text-white text-xs font-semibold shadow-md shadow-[#14433B]/20 transition-all duration-200 flex items-center space-x-2 w-fit">
    <i class="fa-solid fa-plus text-xs"></i>
    <span>Tambah Portfolio Baru</span>
  </a>
</div>

<!-- Table Card -->
<div class="bg-white rounded-2xl shadow-card border border-slate-200 overflow-hidden">
  
  <div class="overflow-x-auto">
    <table class="w-full text-left border-collapse">
      <thead>
        <tr class="border-b border-slate-200 bg-slate-100/70 text-[11px] uppercase tracking-wider text-slate-700 font-bold">
          <th class="py-3.5 px-4 sm:px-6">Project Portfolio</th>
          <th class="py-3.5 px-4 sm:px-6">Kategori</th>
          <th class="py-3.5 px-4 sm:px-6">Client / Mitra</th>
          <th class="py-3.5 px-4 sm:px-6">Teknologi</th>
          <th class="py-3.5 px-4 sm:px-6">Tautan</th>
          <th class="py-3.5 px-4 sm:px-6 text-right">Aksi</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-slate-100 text-xs">
        @forelse($portfolios as $portfolio)
          <tr class="hover:bg-slate-50 transition-colors">
            
            <!-- Title & Main Image -->
            <td class="py-4 px-4 sm:px-6">
              <div class="flex items-center space-x-3">
                @if($portfolio->image)
                  <img src="{{ Storage::url($portfolio->image) }}" alt="{{ $portfolio->title }}" class="w-14 h-14 object-cover rounded-xl border border-slate-200 shadow-xs shrink-0">
                @else
                  <div class="w-14 h-14 rounded-xl bg-[#14433B]/10 text-[#14433B] flex items-center justify-center font-bold text-base shrink-0">
                    <i class="fa-solid fa-briefcase"></i>
                  </div>
                @endif
                <div>
                  <span class="font-bold text-slate-900 block text-sm">{{ $portfolio->title }}</span>
                  <span class="text-[11px] text-slate-700 font-medium line-clamp-1 max-w-xs">{{ $portfolio->description }}</span>
                </div>
              </div>
            </td>

            <!-- Category -->
            <td class="py-4 px-4 sm:px-6 whitespace-nowrap">
              <span class="px-2.5 py-1 bg-emerald-100 text-emerald-950 border border-emerald-300 text-[11px] font-bold rounded-md">
                {{ $portfolio->category ?? 'Umum' }}
              </span>
            </td>

            <!-- Client -->
            <td class="py-4 px-4 sm:px-6 text-slate-900 font-medium whitespace-nowrap">
              {{ $portfolio->client ?? '-' }}
            </td>

            <!-- Tech Stack Tags -->
            <td class="py-4 px-4 sm:px-6 max-w-xs">
              <div class="flex flex-wrap gap-1">
                @if(is_array($portfolio->technologies) && count($portfolio->technologies) > 0)
                  @foreach($portfolio->technologies as $tech)
                    <span class="px-2 py-0.5 bg-slate-100 text-slate-800 border border-slate-200 text-[10px] font-semibold rounded-md">
                      {{ $tech }}
                    </span>
                  @endforeach
                @else
                  <span class="text-slate-500 text-[11px] font-medium">-</span>
                @endif
              </div>
            </td>

            <!-- Link -->
            <td class="py-4 px-4 sm:px-6 whitespace-nowrap">
              @if($portfolio->link)
                <a href="{{ $portfolio->link }}" target="_blank" class="inline-flex items-center space-x-1 text-[#14433B] hover:text-[#0B443C] text-xs font-bold hover:underline">
                  <span>Buka Link</span>
                  <i class="fa-solid fa-arrow-up-right-from-square text-[10px]"></i>
                </a>
              @else
                <span class="text-slate-500 text-[11px] font-medium">-</span>
              @endif
            </td>

            <!-- Actions -->
            <td class="py-4 px-4 sm:px-6 text-right whitespace-nowrap">
              <div class="flex items-center justify-end space-x-2">
                <a href="{{ route('admin.portfolios.edit', $portfolio) }}" class="p-2 rounded-lg text-slate-700 hover:text-[#14433B] hover:bg-slate-100 transition-colors" title="Edit Portfolio">
                  <i class="fa-solid fa-pen-to-square"></i>
                </a>
                <button type="button" onclick="confirmDelete('{{ route('admin.portfolios.destroy', $portfolio) }}', 'Apakah Anda yakin ingin menghapus portfolio \'{{ addslashes($portfolio->title) }}\'?')" class="p-2 rounded-lg text-slate-600 hover:text-rose-600 hover:bg-rose-50 transition-colors" title="Hapus Portfolio">
                  <i class="fa-solid fa-trash-can"></i>
                </button>
              </div>
            </td>

          </tr>
        @empty
          <tr>
            <td colspan="6" class="py-12 text-center text-slate-500">
              <div class="flex flex-col items-center justify-center space-y-3">
                <div class="w-16 h-16 rounded-full bg-slate-100 flex items-center justify-center text-slate-400 text-2xl">
                  <i class="fa-solid fa-folder-open"></i>
                </div>
                <div>
                  <p class="text-sm font-semibold text-slate-800">Belum Ada Portfolio</p>
                  <p class="text-xs text-slate-600 mt-0.5">Tambahkan hasil pengerjaan proyek untuk dipublikasikan.</p>
                </div>
                <a href="{{ route('admin.portfolios.create') }}" class="px-4 py-2 rounded-xl bg-[#14433B] text-white text-xs font-semibold shadow-sm hover:bg-[#0B443C] transition-colors">
                  + Tambah Portfolio Baru
                </a>
              </div>
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>

</div>

@endsection
