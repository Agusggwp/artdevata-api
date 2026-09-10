@extends('layouts.admin')

@section('title', 'Kelola Layanan')

@section('content')

<!-- Header Bar -->
<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
  <div>
    <h1 class="text-xl font-bold text-slate-900">Daftar Layanan ARTDEVATA</h1>
    <p class="text-xs text-slate-700 font-medium">Kelola paket & jenis layanan yang ditawarkan di website</p>
  </div>
  
  <a href="{{ route('admin.services.create') }}" class="px-4 py-2.5 rounded-xl bg-[#14433B] hover:bg-[#0B443C] text-white text-xs font-semibold shadow-md shadow-[#14433B]/20 transition-all duration-200 flex items-center space-x-2 w-fit">
    <i class="fa-solid fa-plus text-xs"></i>
    <span>Tambah Layanan Baru</span>
  </a>
</div>

<!-- Table Card -->
<div class="bg-white rounded-2xl shadow-card border border-slate-200 overflow-hidden">
  
  <div class="overflow-x-auto">
    <table class="w-full text-left border-collapse">
      <thead>
        <tr class="border-b border-slate-200 bg-slate-100/70 text-[11px] uppercase tracking-wider text-slate-700 font-bold">
          <th class="py-3.5 px-4 sm:px-6">Layanan</th>
          <th class="py-3.5 px-4 sm:px-6">Deskripsi Singkat</th>
          <th class="py-3.5 px-4 sm:px-6">Fitur & Fasilitas</th>
          <th class="py-3.5 px-4 sm:px-6 text-right">Aksi</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-slate-100 text-xs">
        @forelse($services as $service)
          <tr class="hover:bg-slate-50 transition-colors">
            
            <!-- Service title & image -->
            <td class="py-4 px-4 sm:px-6">
              <div class="flex items-center space-x-3">
                @if($service->image)
                  <img src="{{ Storage::url($service->image) }}" alt="{{ $service->title }}" class="w-12 h-12 object-cover rounded-xl border border-slate-200 shadow-xs shrink-0">
                @else
                  <div class="w-12 h-12 rounded-xl bg-[#14433B]/10 text-[#14433B] flex items-center justify-center font-bold text-base shrink-0">
                    <i class="fa-solid fa-cubes"></i>
                  </div>
                @endif
                <div>
                  <span class="font-bold text-slate-900 block text-sm">{{ $service->title }}</span>
                  <span class="text-[10px] text-slate-600 font-semibold">ID: #SRV-{{ $service->id }}</span>
                </div>
              </div>
            </td>

            <!-- Description -->
            <td class="py-4 px-4 sm:px-6 text-slate-800 font-medium max-w-xs">
              <p class="line-clamp-2 text-xs leading-relaxed">{{ $service->description }}</p>
            </td>

            <!-- Features -->
            <td class="py-4 px-4 sm:px-6">
              <div class="flex flex-wrap gap-1 max-w-xs">
                @if(is_array($service->features) && count($service->features) > 0)
                  @foreach($service->features as $feature)
                    <span class="px-2 py-0.5 bg-emerald-100 text-emerald-950 border border-emerald-300 text-[10px] font-bold rounded-md">
                      <i class="fa-solid fa-check text-[8px] mr-1 text-[#14433B]"></i>{{ $feature }}
                    </span>
                  @endforeach
                @else
                  <span class="text-slate-500 text-[11px] font-medium italic">Tidak ada fitur spesifik</span>
                @endif
              </div>
            </td>

            <!-- Actions -->
            <td class="py-4 px-4 sm:px-6 text-right whitespace-nowrap">
              <div class="flex items-center justify-end space-x-2">
                <a href="{{ route('admin.services.edit', $service) }}" class="p-2 rounded-lg text-slate-700 hover:text-[#14433B] hover:bg-slate-100 transition-colors" title="Edit Layanan">
                  <i class="fa-solid fa-pen-to-square"></i>
                </a>
                <button type="button" onclick="confirmDelete('{{ route('admin.services.destroy', $service) }}', 'Apakah Anda yakin ingin menghapus layanan \'{{ addslashes($service->title) }}\'?')" class="p-2 rounded-lg text-slate-600 hover:text-rose-600 hover:bg-rose-50 transition-colors" title="Hapus Layanan">
                  <i class="fa-solid fa-trash-can"></i>
                </button>
              </div>
            </td>

          </tr>
        @empty
          <tr>
            <td colspan="4" class="py-12 text-center text-slate-500">
              <div class="flex flex-col items-center justify-center space-y-3">
                <div class="w-16 h-16 rounded-full bg-slate-100 flex items-center justify-center text-slate-400 text-2xl">
                  <i class="fa-solid fa-cubes-stacked"></i>
                </div>
                <div>
                  <p class="text-sm font-semibold text-slate-800">Belum Ada Layanan</p>
                  <p class="text-xs text-slate-600 mt-0.5">Tambahkan layanan pertama Anda untuk ditampilkan di website.</p>
                </div>
                <a href="{{ route('admin.services.create') }}" class="px-4 py-2 rounded-xl bg-[#14433B] text-white text-xs font-semibold shadow-sm hover:bg-[#0B443C] transition-colors">
                  + Tambah Layanan Baru
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
