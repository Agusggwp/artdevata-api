@extends('layouts.admin')

@section('title', 'Riwayat Login Administrator')

@section('content')

<!-- Header Bar -->
<div class="flex items-center justify-between mb-6">
  <div>
    <h1 class="text-xl font-bold text-slate-900">Riwayat Login (Login History)</h1>
    <p class="text-xs text-slate-700 font-medium">Pemantauan aktivitas login, percobaan gagal, dan sesi keluar administrator</p>
  </div>
</div>

<!-- Filters Bar -->
<div class="bg-white rounded-2xl p-4 shadow-card border border-slate-200 mb-6">
  <form method="GET" action="{{ route('admin.login-histories.index') }}" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-3">
    
    <!-- Search Bar -->
    <div class="relative">
      <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400 text-xs">
        <i class="fa-solid fa-magnifying-glass"></i>
      </div>
      <input 
        type="text" 
        name="search" 
        value="{{ request('search') }}"
        class="w-full pl-9 pr-3.5 py-2 bg-slate-50 border border-slate-300 rounded-xl text-xs text-slate-900 font-medium placeholder-slate-400 focus:bg-white focus:border-[#14433B] outline-hidden transition-all"
        placeholder="Cari email atau IP..."
      >
    </div>

    <!-- Admin Filter -->
    @if(Auth::guard('admin')->user()->isSuperAdmin())
      <div>
        <select name="admin_id" onchange="this.form.submit()" class="w-full py-2 px-3 bg-slate-50 border border-slate-300 rounded-xl text-xs text-slate-900 font-semibold focus:bg-white outline-hidden">
          <option value="">Semua Administrator</option>
          @foreach($admins as $adm)
            <option value="{{ $adm->id }}" {{ request('admin_id') == $adm->id ? 'selected' : '' }}>{{ $adm->name }} ({{ $adm->email }})</option>
          @endforeach
        </select>
      </div>
    @endif

    <!-- Status Filter -->
    <div>
      <select name="status" onchange="this.form.submit()" class="w-full py-2 px-3 bg-slate-50 border border-slate-300 rounded-xl text-xs text-slate-900 font-semibold focus:bg-white outline-hidden">
        <option value="">Semua Status</option>
        <option value="success" {{ request('status') == 'success' ? 'selected' : '' }}>Berhasil (Success)</option>
        <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>Gagal (Failed)</option>
        <option value="logout" {{ request('status') == 'logout' ? 'selected' : '' }}>Keluar (Logout)</option>
      </select>
    </div>

    <div>
      <button type="submit" class="w-full px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-800 text-xs font-bold rounded-xl border border-slate-300 transition-colors">
        Filter Data
      </button>
    </div>

  </form>
</div>

<!-- Login Histories Table -->
<div class="bg-white rounded-2xl shadow-card border border-slate-200 overflow-hidden">
  <div class="overflow-x-auto">
    <table class="w-full text-left border-collapse">
      <thead>
        <tr class="bg-slate-50 border-b border-slate-200 text-[11px] font-bold text-slate-600 uppercase tracking-wider">
          <th class="py-3.5 px-6">Waktu Kejadian</th>
          <th class="py-3.5 px-4">Email Administrator</th>
          <th class="py-3.5 px-4">Status</th>
          <th class="py-3.5 px-4">IP Address</th>
          <th class="py-3.5 px-6">User Agent / Perangkat</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-slate-100 text-xs font-medium text-slate-800">
        @forelse($histories as $history)
          <tr class="hover:bg-slate-50/80 transition-colors">
            <!-- Time -->
            <td class="py-4 px-6 text-[11px] text-slate-500 whitespace-nowrap">
              <div>{{ $history->created_at->format('d M Y') }}</div>
              <div class="text-[10px] font-mono text-slate-400">{{ $history->created_at->format('H:i:s') }}</div>
            </td>

            <!-- Email / Admin -->
            <td class="py-4 px-4 whitespace-nowrap">
              <div class="font-bold text-slate-900">{{ $history->admin ? $history->admin->name : $history->email }}</div>
              <div class="text-[10px] text-slate-500">{{ $history->email }}</div>
            </td>

            <!-- Status Badge -->
            <td class="py-4 px-4 whitespace-nowrap">
              @if($history->status === 'success')
                <span class="px-2.5 py-0.5 rounded-full bg-emerald-100 text-emerald-900 text-[10px] font-extrabold border border-emerald-300">
                  Berhasil (Success)
                </span>
              @elseif($history->status === 'failed')
                <span class="px-2.5 py-0.5 rounded-full bg-rose-100 text-rose-900 text-[10px] font-extrabold border border-rose-300">
                  Gagal (Failed)
                </span>
              @else
                <span class="px-2.5 py-0.5 rounded-full bg-slate-200 text-slate-800 text-[10px] font-extrabold border border-slate-300">
                  Logout
                </span>
              @endif
            </td>

            <!-- IP Address -->
            <td class="py-4 px-4 font-mono text-[11px] text-slate-600 whitespace-nowrap">
              {{ $history->ip_address ?? '-' }}
            </td>

            <!-- User Agent -->
            <td class="py-4 px-6 text-slate-500 text-[11px] max-w-xs truncate" title="{{ $history->user_agent }}">
              {{ $history->user_agent ?? '-' }}
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="5" class="py-8 text-center text-slate-400 italic">Belum ada riwayat login yang dicatat.</td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  <div class="p-4 border-t border-slate-200">
    {{ $histories->withQueryString()->links() }}
  </div>
</div>

@endsection
