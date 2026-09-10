@extends('layouts.admin')

@section('title', 'Kelola User Administrator')

@section('content')

<!-- Header Bar -->
<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
  <div>
    <h1 class="text-xl font-bold text-slate-900">Daftar Administrator & Tim</h1>
    <p class="text-xs text-slate-700 font-medium">Kelola tim pengembang, akun administrator, dan akses panel ARTDEVATA</p>
  </div>
  
  <a href="{{ route('admin.users.create') }}" class="px-4 py-2.5 rounded-xl bg-[#14433B] hover:bg-[#0B443C] text-white text-xs font-semibold shadow-md shadow-[#14433B]/20 transition-all duration-200 flex items-center space-x-2 w-fit">
    <i class="fa-solid fa-user-plus text-xs"></i>
    <span>Tambah Admin Baru</span>
  </a>
</div>

<!-- Table Card -->
<div class="bg-white rounded-2xl shadow-card border border-slate-200 overflow-hidden">
  
  <div class="overflow-x-auto">
    <table class="w-full text-left border-collapse">
      <thead>
        <tr class="border-b border-slate-200 bg-slate-100/70 text-[11px] uppercase tracking-wider text-slate-700 font-bold">
          <th class="py-3.5 px-4 sm:px-6">Nama Administrator</th>
          <th class="py-3.5 px-4 sm:px-6">Email / Kontak</th>
          <th class="py-3.5 px-4 sm:px-6">Tanggal Terdaftar</th>
          <th class="py-3.5 px-4 sm:px-6">Status Akses</th>
          <th class="py-3.5 px-4 sm:px-6 text-right">Aksi</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-slate-100 text-xs">
        @forelse($users as $user)
          <tr class="hover:bg-slate-50 transition-colors">
            
            <!-- Name & Avatar -->
            <td class="py-4 px-4 sm:px-6">
              <div class="flex items-center space-x-3">
                <div class="w-10 h-10 rounded-full bg-[#14433B] text-[#21C9A4] font-black text-sm flex items-center justify-center shrink-0 border border-[#0E5D55] shadow-xs">
                  {{ strtoupper(substr($user->name, 0, 1)) }}
                </div>
                <div>
                  <span class="font-bold text-slate-900 block text-sm flex items-center gap-2">
                    {{ $user->name }}
                    @if(Auth::guard('admin')->id() === $user->id)
                      <span class="px-2 py-0.5 rounded-full text-[9px] font-extrabold bg-emerald-100 text-emerald-950 border border-emerald-300">
                        Anda (Session Aktif)
                      </span>
                    @endif
                  </span>
                  <span class="text-[10px] text-slate-600 font-semibold">ID: #ADM-{{ $user->id }}</span>
                </div>
              </div>
            </td>

            <!-- Email -->
            <td class="py-4 px-4 sm:px-6 text-slate-900 font-semibold whitespace-nowrap">
              <div class="flex items-center space-x-1.5">
                <i class="fa-solid fa-envelope text-slate-600 text-xs"></i>
                <span>{{ $user->email }}</span>
              </div>
            </td>

            <!-- Joined Date -->
            <td class="py-4 px-4 sm:px-6 text-slate-700 font-medium whitespace-nowrap">
              {{ $user->created_at ? $user->created_at->format('d M Y, H:i') : '-' }}
            </td>

            <!-- Role / Status Badge -->
            <td class="py-4 px-4 sm:px-6 whitespace-nowrap">
              <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-blue-100 text-blue-950 border border-blue-300 shadow-xs">
                <i class="fa-solid fa-user-shield text-[9px] mr-1.5 text-blue-700"></i> Administrator
              </span>
            </td>

            <!-- Actions -->
            <td class="py-4 px-4 sm:px-6 text-right whitespace-nowrap">
              <div class="flex items-center justify-end space-x-2">
                <a href="{{ route('admin.users.edit', $user) }}" class="p-2 rounded-lg text-slate-700 hover:text-[#14433B] hover:bg-slate-100 transition-colors" title="Edit Admin">
                  <i class="fa-solid fa-pen-to-square"></i>
                </a>
                
                @if(Auth::guard('admin')->id() !== $user->id)
                  <button type="button" onclick="confirmDelete('{{ route('admin.users.destroy', $user) }}', 'Apakah Anda yakin ingin menghapus akun administrator \'{{ addslashes($user->name) }}\'?')" class="p-2 rounded-lg text-slate-600 hover:text-rose-600 hover:bg-rose-50 transition-colors" title="Hapus Admin">
                    <i class="fa-solid fa-trash-can"></i>
                  </button>
                @else
                  <button type="button" disabled class="p-2 rounded-lg text-slate-300 cursor-not-allowed" title="Tidak dapat menghapus akun sendiri">
                    <i class="fa-solid fa-ban"></i>
                  </button>
                @endif
              </div>
            </td>

          </tr>
        @empty
          <tr>
            <td colspan="5" class="py-12 text-center text-slate-500">
              <div class="flex flex-col items-center justify-center space-y-3">
                <div class="w-16 h-16 rounded-full bg-slate-100 flex items-center justify-center text-slate-400 text-2xl">
                  <i class="fa-solid fa-users-gear"></i>
                </div>
                <div>
                  <p class="text-sm font-semibold text-slate-800">Belum Ada Administrator Lain</p>
                  <p class="text-xs text-slate-600 mt-0.5">Tambahkan anggota tim administrator baru.</p>
                </div>
                <a href="{{ route('admin.users.create') }}" class="px-4 py-2 rounded-xl bg-[#14433B] text-white text-xs font-semibold shadow-sm hover:bg-[#0B443C] transition-colors">
                  + Tambah Admin Baru
                </a>
              </div>
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  @if($users->hasPages())
    <div class="px-6 py-4 border-t border-slate-200">
      {{ $users->links() }}
    </div>
  @endif

</div>

@endsection
