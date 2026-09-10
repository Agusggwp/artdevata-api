@extends('layouts.admin')

@section('title', 'Kelola Client & Klien')

@section('content')

<!-- Header Bar -->
<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
  <div>
    <h1 class="text-xl font-bold text-slate-900">Daftar Klien & Mitra Kerja</h1>
    <p class="text-xs text-slate-700 font-medium">Kelola kontak, informasi perusahaan, dan logo mitra ARTDEVATA</p>
  </div>
  
  <a href="{{ route('admin.clients.create') }}" class="px-4 py-2.5 rounded-xl bg-[#14433B] hover:bg-[#0B443C] text-white text-xs font-semibold shadow-md shadow-[#14433B]/20 transition-all duration-200 flex items-center space-x-2 w-fit">
    <i class="fa-solid fa-plus text-xs"></i>
    <span>Tambah Klien Baru</span>
  </a>
</div>

<!-- Table Card -->
<div class="bg-white rounded-2xl shadow-card border border-slate-200 overflow-hidden">
  
  <div class="overflow-x-auto">
    <table class="w-full text-left border-collapse">
      <thead>
        <tr class="border-b border-slate-200 bg-slate-100/70 text-[11px] uppercase tracking-wider text-slate-700 font-bold">
          <th class="py-3.5 px-4 sm:px-6">Nama Klien</th>
          <th class="py-3.5 px-4 sm:px-6">Perusahaan</th>
          <th class="py-3.5 px-4 sm:px-6">Kontak Email / Telp</th>
          <th class="py-3.5 px-4 sm:px-6">Status</th>
          <th class="py-3.5 px-4 sm:px-6 text-right">Aksi</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-slate-100 text-xs">
        @forelse($clients as $client)
          <tr class="hover:bg-slate-50 transition-colors">
            
            <!-- Logo & Client Name -->
            <td class="py-4 px-4 sm:px-6">
              <div class="flex items-center space-x-3">
                @if($client->logo)
                  <img src="{{ Storage::url($client->logo) }}" alt="{{ $client->name }}" class="w-10 h-10 object-cover rounded-full border border-slate-200 shadow-xs shrink-0">
                @else
                  <div class="w-10 h-10 rounded-full bg-[#14433B] text-[#21C9A4] flex items-center justify-center font-bold text-sm shrink-0">
                    {{ strtoupper(substr($client->name, 0, 1)) }}
                  </div>
                @endif
                <div>
                  <span class="font-bold text-slate-900 block text-sm">{{ $client->name }}</span>
                  <span class="text-[10px] text-slate-600 font-semibold">ID: #CLI-{{ $client->id }}</span>
                </div>
              </div>
            </td>

            <!-- Company -->
            <td class="py-4 px-4 sm:px-6 text-slate-900 font-medium whitespace-nowrap">
              {{ $client->company ?? '-' }}
            </td>

            <!-- Contact info -->
            <td class="py-4 px-4 sm:px-6 text-slate-700 whitespace-nowrap">
              <div class="flex flex-col space-y-0.5">
                <span class="text-xs font-bold text-slate-900"><i class="fa-solid fa-envelope text-[10px] text-slate-600 mr-1.5"></i>{{ $client->email ?? '-' }}</span>
                <span class="text-[11px] font-semibold text-slate-700"><i class="fa-solid fa-phone text-[10px] text-slate-600 mr-1.5"></i>{{ $client->phone ?? '-' }}</span>
              </div>
            </td>

            <!-- Status -->
            <td class="py-4 px-4 sm:px-6 whitespace-nowrap">
              @if($client->status === 'active')
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-900 border border-emerald-300">
                  <span class="w-1.5 h-1.5 rounded-full bg-emerald-600 mr-1.5"></span>Aktif
                </span>
              @else
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-slate-200 text-slate-900 border border-slate-300">
                  <span class="w-1.5 h-1.5 rounded-full bg-slate-600 mr-1.5"></span>Tidak Aktif
                </span>
              @endif
            </td>

            <!-- Actions -->
            <td class="py-4 px-4 sm:px-6 text-right whitespace-nowrap">
              <div class="flex items-center justify-end space-x-2">
                <a href="{{ route('admin.clients.edit', $client) }}" class="p-2 rounded-lg text-slate-700 hover:text-[#14433B] hover:bg-slate-100 transition-colors" title="Edit Klien">
                  <i class="fa-solid fa-pen-to-square"></i>
                </a>
                <button type="button" onclick="confirmDelete('{{ route('admin.clients.destroy', $client) }}', 'Apakah Anda yakin ingin menghapus data klien \'{{ addslashes($client->name) }}\'?')" class="p-2 rounded-lg text-slate-600 hover:text-rose-600 hover:bg-rose-50 transition-colors" title="Hapus Klien">
                  <i class="fa-solid fa-trash-can"></i>
                </button>
              </div>
            </td>

          </tr>
        @empty
          <tr>
            <td colspan="5" class="py-12 text-center text-slate-500">
              <div class="flex flex-col items-center justify-center space-y-3">
                <div class="w-16 h-16 rounded-full bg-slate-100 flex items-center justify-center text-slate-400 text-2xl">
                  <i class="fa-solid fa-users-rectangle"></i>
                </div>
                <div>
                  <p class="text-sm font-semibold text-slate-800">Belum Ada Data Klien</p>
                  <p class="text-xs text-slate-600 mt-0.5">Tambahkan klien pertama untuk mengelola kerjasama proyek.</p>
                </div>
                <a href="{{ route('admin.clients.create') }}" class="px-4 py-2 rounded-xl bg-[#14433B] text-white text-xs font-semibold shadow-sm hover:bg-[#0B443C] transition-colors">
                  + Tambah Klien Baru
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
