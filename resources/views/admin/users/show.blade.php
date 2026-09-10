@extends('layouts.admin')

@section('title', 'Detail Administrator & Audit Log')

@section('content')

<!-- Header Bar -->
<div class="flex items-center justify-between mb-6">
  <div>
    <h1 class="text-xl font-bold text-slate-900">Detail Profil Administrator</h1>
    <p class="text-xs text-slate-700 font-medium">Informasi akun, peran hak akses, riwayat aktivitas, dan log login</p>
  </div>

  <a href="{{ route('admin.users.index') }}" class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-800 text-xs font-bold border border-slate-300 transition-colors flex items-center space-x-2">
    <i class="fa-solid fa-arrow-left"></i>
    <span>Kembali</span>
  </a>
</div>

<!-- Profile Info Card -->
<div class="bg-white rounded-2xl p-6 shadow-card border border-slate-200 mb-6">
  <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
    <div class="flex items-center space-x-4">
      <div class="w-14 h-14 rounded-2xl bg-[#14433B] text-white font-black text-xl flex items-center justify-center shadow-md">
        {{ strtoupper(substr($user->name, 0, 1)) }}
      </div>
      <div>
        <h2 class="text-lg font-bold text-slate-900 flex items-center space-x-2">
          <span>{{ $user->name }}</span>
          @if($user->status === 'active')
            <span class="px-2.5 py-0.5 rounded-full bg-emerald-100 text-emerald-900 text-[10px] font-extrabold border border-emerald-300">Aktif</span>
          @elseif($user->status === 'inactive')
            <span class="px-2.5 py-0.5 rounded-full bg-amber-100 text-amber-900 text-[10px] font-extrabold border border-amber-300">Nonaktif</span>
          @else
            <span class="px-2.5 py-0.5 rounded-full bg-rose-100 text-rose-900 text-[10px] font-extrabold border border-rose-300">Ditangguhkan</span>
          @endif
        </h2>
        <p class="text-xs text-slate-500 mt-0.5">{{ $user->email }}</p>
      </div>
    </div>

    <div class="flex items-center space-x-2">
      <a href="{{ route('admin.users.edit', $user->id) }}" class="px-4 py-2 rounded-xl bg-amber-50 hover:bg-amber-100 text-amber-800 text-xs font-bold border border-amber-200 transition-colors flex items-center space-x-1.5">
        <i class="fa-solid fa-pen-to-square"></i>
        <span>Edit Profile</span>
      </a>

      @if(Auth::guard('admin')->id() !== $user->id)
        <form method="POST" action="{{ route('admin.users.force-logout', $user->id) }}" onsubmit="return confirm('Apakah Anda yakin ingin melakukan force logout pada pengguna ini?')">
          @csrf
          <button type="submit" class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-800 text-xs font-bold border border-slate-300 transition-colors flex items-center space-x-1.5">
            <i class="fa-solid fa-right-from-bracket"></i>
            <span>Force Logout</span>
          </button>
        </form>
      @endif
    </div>
  </div>

  <div class="grid grid-cols-1 sm:grid-cols-4 gap-4 pt-6 mt-6 border-t border-slate-100 text-xs">
    <div>
      <span class="text-slate-400 block mb-0.5">Peran / Role:</span>
      <span class="font-bold text-[#14433B]">{{ $user->role ? $user->role->name : 'Belum Ada Role' }}</span>
    </div>

    <div>
      <span class="text-slate-400 block mb-0.5">Login Terakhir:</span>
      <span class="font-bold text-slate-800">{{ $user->last_login_at ? $user->last_login_at->format('d M Y, H:i') : '-' }}</span>
    </div>

    <div>
      <span class="text-slate-400 block mb-0.5">IP Login Terakhir:</span>
      <span class="font-mono font-bold text-slate-800">{{ $user->last_login_ip ?? '-' }}</span>
    </div>

    <div>
      <span class="text-slate-400 block mb-0.5">Aktivitas Terakhir:</span>
      <span class="font-bold text-slate-800">{{ $user->last_activity_at ? $user->last_activity_at->diffForHumans() : '-' }}</span>
    </div>
  </div>
</div>

<!-- Tabs: Audit Logs & Login History -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
  
  <!-- Recent Activity Audit Logs -->
  <div class="bg-white rounded-2xl p-6 shadow-card border border-slate-200">
    <h3 class="text-sm font-bold text-slate-900 mb-4 flex items-center space-x-2">
      <i class="fa-solid fa-[#14433B] fa-clock-rotate-left"></i>
      <span>10 Aktivitas Terakhir (Audit Log)</span>
    </h3>

    @if($recentLogs->count() > 0)
      <div class="space-y-3">
        @foreach($recentLogs as $log)
          <div class="p-3 rounded-xl bg-slate-50 border border-slate-200 text-xs flex flex-col space-y-1">
            <div class="flex items-center justify-between">
              <span class="font-bold text-[#14433B] uppercase tracking-wide text-[10px]">{{ $log->module }} / {{ $log->action }}</span>
              <span class="text-[10px] text-slate-400">{{ $log->created_at->format('d M H:i') }}</span>
            </div>
            <p class="text-slate-800 font-medium">{{ $log->description }}</p>
            <div class="text-[10px] text-slate-400 font-mono">IP: {{ $log->ip_address }}</div>
          </div>
        @endforeach
      </div>
    @else
      <p class="text-xs text-slate-400 italic">Belum ada riwayat aktivitas yang tercatat.</p>
    @endif
  </div>

  <!-- Recent Login History -->
  <div class="bg-white rounded-2xl p-6 shadow-card border border-slate-200">
    <h3 class="text-sm font-bold text-slate-900 mb-4 flex items-center space-x-2">
      <i class="fa-solid fa-[#14433B] fa-[#14433B] fa-shield-halved"></i>
      <span>10 Riwayat Login Terakhir</span>
    </h3>

    @if($recentLogins->count() > 0)
      <div class="space-y-3">
        @foreach($recentLogins as $history)
          <div class="p-3 rounded-xl bg-slate-50 border border-slate-200 text-xs flex items-center justify-between">
            <div class="flex items-center space-x-3">
              @if($history->status === 'success')
                <div class="w-7 h-7 rounded-lg bg-emerald-100 text-emerald-700 flex items-center justify-center font-bold text-xs">
                  <i class="fa-solid fa-check"></i>
                </div>
              @elseif($history->status === 'failed')
                <div class="w-7 h-7 rounded-lg bg-rose-100 text-rose-700 flex items-center justify-center font-bold text-xs">
                  <i class="fa-solid fa-xmark"></i>
                </div>
              @else
                <div class="w-7 h-7 rounded-lg bg-slate-200 text-slate-700 flex items-center justify-center font-bold text-xs">
                  <i class="fa-solid fa-arrow-right-from-bracket"></i>
                </div>
              @endif

              <div class="flex flex-col">
                <span class="font-bold text-slate-900 uppercase text-[10px] tracking-wide">{{ $history->status }}</span>
                <span class="text-[10px] text-slate-500 font-mono">IP: {{ $history->ip_address }}</span>
              </div>
            </div>

            <span class="text-[10px] text-slate-400 font-medium">{{ $history->created_at->format('d M Y, H:i') }}</span>
          </div>
        @endforeach
      </div>
    @else
      <p class="text-xs text-slate-400 italic">Belum ada riwayat login yang tercatat.</p>
    @endif
  </div>

</div>

@endsection
