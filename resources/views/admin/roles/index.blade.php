@extends('layouts.admin')

@section('title', 'Manajemen Peran / Roles')

@section('content')

<!-- Header Bar -->
<div class="flex items-center justify-between mb-6">
  <div>
    <h1 class="text-xl font-bold text-slate-900">Manajemen Peran & Hak Akses (Roles)</h1>
    <p class="text-xs text-slate-700 font-medium">Kelola tingkat kewenangan dan matriks izin akses administrator</p>
  </div>

  <a href="{{ route('admin.roles.create') }}" class="px-4 py-2.5 rounded-xl bg-[#14433B] hover:bg-[#0B443C] text-white text-xs font-extrabold shadow-md shadow-[#14433B]/20 transition-all flex items-center space-x-2">
    <i class="fa-solid fa-plus text-xs"></i>
    <span>Tambah Peran Baru</span>
  </a>
</div>

<!-- Roles Table -->
<div class="bg-white rounded-2xl shadow-card border border-slate-200 overflow-hidden">
  <div class="overflow-x-auto">
    <table class="w-full text-left border-collapse">
      <thead>
        <tr class="bg-slate-50 border-b border-slate-200 text-[11px] font-bold text-slate-600 uppercase tracking-wider">
          <th class="py-3.5 px-6">Nama Peran</th>
          <th class="py-3.5 px-4">Slug Identifier</th>
          <th class="py-3.5 px-4">Deskripsi</th>
          <th class="py-3.5 px-4 text-center">Jumlah Izin</th>
          <th class="py-3.5 px-4 text-center">Pengguna</th>
          <th class="py-3.5 px-6 text-right">Aksi</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-slate-100 text-xs font-medium text-slate-800">
        @foreach($roles as $role)
          <tr class="hover:bg-slate-50/80 transition-colors">
            <!-- Role Name & Badge -->
            <td class="py-4 px-6 font-bold text-slate-900 flex items-center space-x-3">
              <div class="w-8 h-8 rounded-lg bg-[#14433B]/10 text-[#14433B] flex items-center justify-center font-bold text-sm">
                <i class="fa-solid fa-user-shield"></i>
              </div>
              <div>
                <span>{{ $role->name }}</span>
                @if($role->slug === 'super-admin')
                  <span class="ml-2 px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800 text-[10px] font-extrabold border border-emerald-300">Default Super</span>
                @endif
              </div>
            </td>

            <!-- Slug -->
            <td class="py-4 px-4 font-mono text-[11px] text-slate-600">
              {{ $role->slug }}
            </td>

            <!-- Description -->
            <td class="py-4 px-4 text-slate-600 max-w-xs truncate">
              {{ $role->description ?? '-' }}
            </td>

            <!-- Permissions Count -->
            <td class="py-4 px-4 text-center">
              <span class="px-2.5 py-1 rounded-full bg-blue-50 text-blue-800 font-extrabold text-[11px] border border-blue-200">
                {{ $role->permissions_count }} Izin
              </span>
            </td>

            <!-- Admins Count -->
            <td class="py-4 px-4 text-center">
              <span class="px-2.5 py-1 rounded-full bg-slate-100 text-slate-800 font-extrabold text-[11px] border border-slate-200">
                {{ $role->admins_count }} User
              </span>
            </td>

            <!-- Action Buttons -->
            <td class="py-4 px-6 text-right">
              <div class="flex items-center justify-end space-x-2">
                <a href="{{ route('admin.roles.edit', $role->id) }}" class="w-8 h-8 rounded-lg bg-amber-50 text-amber-700 hover:bg-amber-100 flex items-center justify-center text-xs font-bold transition-colors border border-amber-200" title="Edit Peran">
                  <i class="fa-solid fa-pen-to-square"></i>
                </a>

                @if($role->slug !== 'super-admin')
                  <button 
                    type="button"
                    onclick="confirmDelete('{{ route('admin.roles.destroy', $role->id) }}', 'Apakah Anda yakin ingin menghapus peran {{ e($role->name) }}?')"
                    class="w-8 h-8 rounded-lg bg-rose-50 text-rose-700 hover:bg-rose-100 flex items-center justify-center text-xs font-bold transition-colors border border-rose-200"
                    title="Hapus Peran"
                  >
                    <i class="fa-solid fa-trash-can"></i>
                  </button>
                @endif
              </div>
            </td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</div>

@endsection
