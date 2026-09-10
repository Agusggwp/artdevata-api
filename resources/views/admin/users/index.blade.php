@extends('layouts.admin')

@section('title', 'Kelola Administrator')

@section('content')

<!-- Header Bar -->
<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
  <div>
    <h1 class="text-xl font-bold text-slate-900">Kelola Administrator & Tim</h1>
    <p class="text-xs text-slate-700 font-medium">Manajemen akun pengguna, alokasi peran, status keamanan, dan riwayat aktivitas</p>
  </div>

  <a href="{{ route('admin.users.create') }}" class="px-4 py-2.5 rounded-xl bg-[#14433B] hover:bg-[#0B443C] text-white text-xs font-extrabold shadow-md shadow-[#14433B]/20 transition-all flex items-center justify-center space-x-2 shrink-0">
    <i class="fa-solid fa-user-plus text-xs"></i>
    <span>Tambah Admin Baru</span>
  </a>
</div>

<!-- Filter & Search Bar -->
<div class="bg-white rounded-2xl p-4 shadow-card border border-slate-200 mb-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
  <form method="GET" action="{{ route('admin.users.index') }}" class="flex-1 flex flex-col sm:flex-row items-center gap-3">
    <!-- Search Input -->
    <div class="relative w-full sm:w-64">
      <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400 text-xs">
        <i class="fa-solid fa-magnifying-glass"></i>
      </div>
      <input 
        type="text" 
        name="search" 
        value="{{ request('search') }}"
        class="w-full pl-9 pr-3.5 py-2 bg-slate-50 border border-slate-300 rounded-xl text-xs text-slate-900 font-medium placeholder-slate-400 focus:bg-white focus:border-[#14433B] focus:ring-2 focus:ring-[#14433B]/20 outline-hidden transition-all"
        placeholder="Cari nama atau email..."
      >
    </div>

    <!-- Role Filter -->
    <div class="w-full sm:w-44">
      <select name="role_id" onchange="this.form.submit()" class="w-full py-2 px-3 bg-slate-50 border border-slate-300 rounded-xl text-xs text-slate-900 font-semibold focus:bg-white focus:border-[#14433B] outline-hidden">
        <option value="">Semua Peran</option>
        @foreach($roles as $role)
          <option value="{{ $role->id }}" {{ request('role_id') == $role->id ? 'selected' : '' }}>{{ $role->name }}</option>
        @endforeach
      </select>
    </div>

    <!-- Status Filter -->
    <div class="w-full sm:w-36">
      <select name="status" onchange="this.form.submit()" class="w-full py-2 px-3 bg-slate-50 border border-slate-300 rounded-xl text-xs text-slate-900 font-semibold focus:bg-white focus:border-[#14433B] outline-hidden">
        <option value="">Semua Status</option>
        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Aktif</option>
        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Nonaktif</option>
        <option value="suspended" {{ request('status') == 'suspended' ? 'selected' : '' }}>Ditangguhkan</option>
      </select>
    </div>

    <button type="submit" class="w-full sm:w-auto px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-800 text-xs font-bold rounded-xl border border-slate-300 transition-colors">
      Filter
    </button>

    @if(request()->hasAny(['search', 'role_id', 'status']))
      <a href="{{ route('admin.users.index') }}" class="text-xs text-rose-600 hover:text-rose-800 font-semibold">
        Reset
      </a>
    @endif
  </form>
</div>

<!-- Admin Users Table -->
<div class="bg-white rounded-2xl shadow-card border border-slate-200 overflow-hidden">
  <div class="overflow-x-auto">
    <table class="w-full text-left border-collapse">
      <thead>
        <tr class="bg-slate-50 border-b border-slate-200 text-[11px] font-bold text-slate-600 uppercase tracking-wider">
          <th class="py-3.5 px-6">Administrator</th>
          <th class="py-3.5 px-4">Peran (Role)</th>
          <th class="py-3.5 px-4">Status</th>
          <th class="py-3.5 px-4">Login Terakhir</th>
          <th class="py-3.5 px-4">Aktivitas Terakhir</th>
          <th class="py-3.5 px-6 text-right">Aksi</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-slate-100 text-xs font-medium text-slate-800">
        @foreach($users as $user)
          <tr class="hover:bg-slate-50/80 transition-colors">
            
            <!-- User Profile -->
            <td class="py-4 px-6">
              <div class="flex items-center space-x-3">
                <div class="w-9 h-9 rounded-full bg-[#14433B] text-white font-extrabold text-xs flex items-center justify-center shrink-0 shadow-xs">
                  {{ strtoupper(substr($user->name, 0, 1)) }}
                </div>
                <div class="flex flex-col">
                  <div class="flex items-center space-x-2">
                    <span class="font-bold text-slate-900">{{ $user->name }}</span>
                    @if(Auth::guard('admin')->id() === $user->id)
                      <span class="px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800 text-[10px] font-extrabold border border-emerald-300">Saya</span>
                    @endif
                  </div>
                  <span class="text-[11px] text-slate-500">{{ $user->email }}</span>
                </div>
              </div>
            </td>

            <!-- Role Badge -->
            <td class="py-4 px-4">
              @if($user->role)
                <span class="px-2.5 py-1 rounded-full text-[11px] font-extrabold border bg-slate-100 text-[#14433B] border-slate-200">
                  {{ $user->role->name }}
                </span>
              @else
                <span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-slate-100 text-slate-500">
                  Belum Ada Role
                </span>
              @endif
            </td>

            <!-- Status Badge -->
            <td class="py-4 px-4">
              @if($user->status === 'active')
                <span class="px-2.5 py-0.5 rounded-full bg-emerald-100 text-emerald-900 text-[10px] font-extrabold border border-emerald-300">
                  Aktif
                </span>
              @elseif($user->status === 'inactive')
                <span class="px-2.5 py-0.5 rounded-full bg-amber-100 text-amber-900 text-[10px] font-extrabold border border-amber-300">
                  Nonaktif
                </span>
              @else
                <span class="px-2.5 py-0.5 rounded-full bg-rose-100 text-rose-900 text-[10px] font-extrabold border border-rose-300">
                  Ditangguhkan
                </span>
              @endif
            </td>

            <!-- Last Login -->
            <td class="py-4 px-4 text-slate-600 text-[11px]">
              @if($user->last_login_at)
                <div>{{ $user->last_login_at->format('d M Y, H:i') }}</div>
                <div class="text-[10px] text-slate-400 font-mono">{{ $user->last_login_ip ?? '-' }}</div>
              @else
                <span class="text-slate-400 italic">Belum pernah login</span>
              @endif
            </td>

            <!-- Last Activity -->
            <td class="py-4 px-4 text-slate-600 text-[11px]">
              @if($user->last_activity_at)
                <div>{{ $user->last_activity_at->diffForHumans() }}</div>
              @else
                <span class="text-slate-400">-</span>
              @endif
            </td>

            <!-- Action Controls -->
            <td class="py-4 px-6 text-right">
              <div class="flex items-center justify-end space-x-1.5">
                <!-- Detail Profile & Logs -->
                <a href="{{ route('admin.users.show', $user->id) }}" class="w-8 h-8 rounded-lg bg-blue-50 text-blue-700 hover:bg-blue-100 flex items-center justify-center text-xs font-bold transition-colors border border-blue-200" title="Detail & Log Aktivitas">
                  <i class="fa-solid fa-eye"></i>
                </a>

                <!-- Edit User -->
                <a href="{{ route('admin.users.edit', $user->id) }}" class="w-8 h-8 rounded-lg bg-amber-50 text-amber-700 hover:bg-amber-100 flex items-center justify-center text-xs font-bold transition-colors border border-amber-200" title="Edit Admin">
                  <i class="fa-solid fa-pen-to-square"></i>
                </a>

                <!-- Force Logout -->
                @if(Auth::guard('admin')->id() !== $user->id)
                  <form method="POST" action="{{ route('admin.users.force-logout', $user->id) }}" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin melakukan force logout pada pengguna ini?')">
                    @csrf
                    <button type="submit" class="w-8 h-8 rounded-lg bg-slate-100 text-slate-700 hover:bg-slate-200 flex items-center justify-center text-xs font-bold transition-colors border border-slate-300" title="Force Logout Sesi">
                      <i class="fa-solid fa-right-from-bracket"></i>
                    </button>
                  </form>

                  <!-- Delete User -->
                  <button 
                    type="button"
                    onclick="confirmDelete('{{ route('admin.users.destroy', $user->id) }}', 'Apakah Anda yakin ingin menghapus administrator {{ e($user->name) }}?')"
                    class="w-8 h-8 rounded-lg bg-rose-50 text-rose-700 hover:bg-rose-100 flex items-center justify-center text-xs font-bold transition-colors border border-rose-200"
                    title="Hapus Admin"
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

  <div class="p-4 border-t border-slate-200">
    {{ $users->withQueryString()->links() }}
  </div>
</div>

@endsection
