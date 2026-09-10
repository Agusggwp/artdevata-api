@extends('layouts.admin')

@section('title', 'Security Dashboard & Monitoring')

@section('content')

<!-- Header Bar -->
<div class="flex items-center justify-between mb-6">
  <div>
    <h1 class="text-xl font-bold text-slate-900">Security Dashboard & Monitoring</h1>
    <p class="text-xs text-slate-700 font-medium">Status keamanan sistem, indikator perlindungan, dan pemantauan ancaman real-time</p>
  </div>
</div>

<!-- Metrics Overview Cards -->
<div class="grid grid-cols-1 sm:grid-cols-3 gap-6 mb-6">
  <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-card flex items-center justify-between">
    <div>
      <span class="text-xs font-semibold text-slate-500">Administrator Aktif</span>
      <h3 class="text-2xl font-black text-slate-900 mt-1">{{ $activeAdminsCount }}</h3>
    </div>
    <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl border border-emerald-100">
      <i class="fa-solid fa-[#14433B] fa-user-shield"></i>
    </div>
  </div>

  <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-card flex items-center justify-between">
    <div>
      <span class="text-xs font-semibold text-slate-500">Percobaan Login Gagal Hari Ini</span>
      <h3 class="text-2xl font-black text-rose-600 mt-1">{{ $failedLoginsToday }}</h3>
    </div>
    <div class="w-12 h-12 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center text-xl border border-rose-100">
      <i class="fa-solid fa-triangle-exclamation"></i>
    </div>
  </div>

  <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-card flex items-center justify-between">
    <div>
      <span class="text-xs font-semibold text-slate-500">Akun Ditangguhkan</span>
      <h3 class="text-2xl font-black text-amber-600 mt-1">{{ $suspendedAdminsCount }}</h3>
    </div>
    <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-xl border border-amber-100">
      <i class="fa-solid fa-user-lock"></i>
    </div>
  </div>
</div>

<!-- Security Status Checklist Grid -->
<div class="bg-white rounded-2xl p-6 shadow-card border border-slate-200 mb-6">
  <h2 class="text-sm font-bold text-slate-900 mb-4 flex items-center space-x-2">
    <i class="fa-solid fa-[#14433B] fa-shield-halved text-[#14433B]"></i>
    <span>Status Proteksi & Keamanan Sistem (Security Hardening)</span>
  </h2>

  <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
    @foreach($securityChecklist as $item)
      <div class="p-4 rounded-xl border border-slate-200 bg-slate-50/60 flex flex-col justify-between space-y-3">
        <div class="flex items-start justify-between">
          <span class="text-xs font-bold text-slate-900">{{ $item['title'] }}</span>
          @if($item['status'])
            <span class="w-5 h-5 rounded-full bg-emerald-500 text-white flex items-center justify-center text-[10px] shrink-0">
              <i class="fa-solid fa-check"></i>
            </span>
          @else
            <span class="w-5 h-5 rounded-full bg-amber-500 text-white flex items-center justify-center text-[10px] shrink-0">
              <i class="fa-solid fa-exclamation"></i>
            </span>
          @endif
        </div>

        <p class="text-[11px] text-slate-600 leading-relaxed">{{ $item['description'] }}</p>
        
        <div class="pt-2 border-t border-slate-200 flex items-center justify-between">
          <span class="text-[10px] font-extrabold text-emerald-800 bg-emerald-100 px-2 py-0.5 rounded-full border border-emerald-300">
            {{ $item['label'] }}
          </span>
        </div>
      </div>
    @endforeach
  </div>
</div>

<!-- Logs & Failed Attempts Split View -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

  <!-- Recent Failed Login Attempts -->
  <div class="bg-white rounded-2xl p-6 shadow-card border border-slate-200">
    <h3 class="text-sm font-bold text-slate-900 mb-4 flex items-center space-x-2 text-rose-700">
      <i class="fa-solid fa-user-xmark"></i>
      <span>Percobaan Login Gagal Terakhir</span>
    </h3>

    @if($recentFailedLogins->count() > 0)
      <div class="space-y-3">
        @foreach($recentFailedLogins as $failed)
          <div class="p-3 rounded-xl bg-rose-50 border border-rose-200 text-xs flex items-center justify-between">
            <div>
              <div class="font-bold text-rose-900">{{ $failed->email }}</div>
              <div class="text-[10px] text-rose-700 font-mono">IP: {{ $failed->ip_address }}</div>
            </div>
            <span class="text-[10px] text-rose-700 font-semibold">{{ $failed->created_at->format('d M, H:i') }}</span>
          </div>
        @endforeach
      </div>
    @else
      <p class="text-xs text-slate-400 italic">Tidak ada percobaan login gagal baru-baru ini.</p>
    @endif
  </div>

  <!-- Recent Security Audit Log Events -->
  <div class="bg-white rounded-2xl p-6 shadow-card border border-slate-200">
    <h3 class="text-sm font-bold text-slate-900 mb-4 flex items-center space-x-2">
      <i class="fa-solid fa-file-shield text-[#14433B]"></i>
      <span>Peristiwa Keamanan Terbaru</span>
    </h3>

    @if($recentSecurityEvents->count() > 0)
      <div class="space-y-3">
        @foreach($recentSecurityEvents as $event)
          <div class="p-3 rounded-xl bg-slate-50 border border-slate-200 text-xs flex items-center justify-between">
            <div>
              <div class="flex items-center space-x-2">
                <span class="font-bold text-slate-900">{{ $event->admin_name }}</span>
                <span class="px-2 py-0.2 rounded-full bg-slate-200 text-slate-800 text-[9px] font-extrabold">{{ $event->action }}</span>
              </div>
              <p class="text-[11px] text-slate-600 mt-0.5 line-clamp-1">{{ $event->description }}</p>
            </div>
            <span class="text-[10px] text-slate-400 font-mono shrink-0 ml-2">{{ $event->created_at->format('H:i') }}</span>
          </div>
        @endforeach
      </div>
    @else
      <p class="text-xs text-slate-400 italic">Belum ada peristiwa keamanan yang dicatat.</p>
    @endif
  </div>

</div>

@endsection
