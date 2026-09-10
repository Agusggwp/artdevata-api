@extends('layouts.admin')

@section('title', 'Audit Log / Activity Logs')

@section('content')

<!-- Header Bar -->
<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
  <div>
    <h1 class="text-xl font-bold text-slate-900">Audit Log / Activity Logs</h1>
    <p class="text-xs text-slate-700 font-medium">Pencatatan terpusat seluruh jejak aktivitas sensitif administrator</p>
  </div>
</div>

<!-- Filters Bar -->
<div class="bg-white rounded-2xl p-4 shadow-card border border-slate-200 mb-6">
  <form method="GET" action="{{ route('admin.activity-logs.index') }}" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-5 gap-3">
    
    <!-- Search Bar -->
    <div class="relative md:col-span-2">
      <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400 text-xs">
        <i class="fa-solid fa-magnifying-glass"></i>
      </div>
      <input 
        type="text" 
        name="search" 
        value="{{ request('search') }}"
        class="w-full pl-9 pr-3.5 py-2 bg-slate-50 border border-slate-300 rounded-xl text-xs text-slate-900 font-medium placeholder-slate-400 focus:bg-white focus:border-[#14433B] outline-hidden transition-all"
        placeholder="Cari deskripsi, admin, IP..."
      >
    </div>

    <!-- Admin Filter -->
    <div>
      <select name="admin_id" onchange="this.form.submit()" class="w-full py-2 px-3 bg-slate-50 border border-slate-300 rounded-xl text-xs text-slate-900 font-semibold focus:bg-white outline-hidden">
        <option value="">Semua Admin</option>
        @foreach($admins as $adm)
          <option value="{{ $adm->id }}" {{ request('admin_id') == $adm->id ? 'selected' : '' }}>{{ $adm->name }}</option>
        @endforeach
      </select>
    </div>

    <!-- Module Filter -->
    <div>
      <select name="module" onchange="this.form.submit()" class="w-full py-2 px-3 bg-slate-50 border border-slate-300 rounded-xl text-xs text-slate-900 font-semibold focus:bg-white outline-hidden">
        <option value="">Semua Modul</option>
        @foreach($modules as $mod)
          <option value="{{ $mod }}" {{ request('module') == $mod ? 'selected' : '' }}>{{ $mod }}</option>
        @endforeach
      </select>
    </div>

    <!-- Action Filter -->
    <div>
      <select name="action" onchange="this.form.submit()" class="w-full py-2 px-3 bg-slate-50 border border-slate-300 rounded-xl text-xs text-slate-900 font-semibold focus:bg-white outline-hidden">
        <option value="">Semua Aksi</option>
        @foreach($actions as $act)
          <option value="{{ $act }}" {{ request('action') == $act ? 'selected' : '' }}>{{ strtoupper($act) }}</option>
        @endforeach
      </select>
    </div>

  </form>
</div>

<!-- Audit Logs Timeline / Table -->
<div class="bg-white rounded-2xl shadow-card border border-slate-200 overflow-hidden">
  <div class="overflow-x-auto">
    <table class="w-full text-left border-collapse">
      <thead>
        <tr class="bg-slate-50 border-b border-slate-200 text-[11px] font-bold text-slate-600 uppercase tracking-wider">
          <th class="py-3.5 px-6">Waktu</th>
          <th class="py-3.5 px-4">Administrator</th>
          <th class="py-3.5 px-4">Modul / Aksi</th>
          <th class="py-3.5 px-6">Deskripsi Aktivitas</th>
          <th class="py-3.5 px-4">IP Address</th>
          <th class="py-3.5 px-6 text-right">Detail Data</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-slate-100 text-xs font-medium text-slate-800">
        @forelse($logs as $log)
          <tr class="hover:bg-slate-50/80 transition-colors">
            <!-- Timestamp -->
            <td class="py-4 px-6 text-[11px] text-slate-500 whitespace-nowrap">
              <div>{{ $log->created_at->format('d M Y') }}</div>
              <div class="text-[10px] font-mono text-slate-400">{{ $log->created_at->format('H:i:s') }}</div>
            </td>

            <!-- Admin User -->
            <td class="py-4 px-4 whitespace-nowrap">
              <div class="font-bold text-slate-900">{{ $log->admin_name }}</div>
              <div class="text-[10px] text-slate-400">ID: {{ $log->admin_id ?? '-' }}</div>
            </td>

            <!-- Module & Action Badges -->
            <td class="py-4 px-4 whitespace-nowrap">
              <span class="px-2 py-0.5 rounded-full bg-slate-100 text-slate-700 font-extrabold text-[10px] border border-slate-200 mr-1">
                {{ $log->module }}
              </span>
              <span class="px-2 py-0.5 rounded-full text-[10px] font-black uppercase
                @if($log->action === 'create') bg-emerald-100 text-emerald-900 border border-emerald-300
                @elseif($log->action === 'update') bg-blue-100 text-blue-900 border border-blue-300
                @elseif($log->action === 'delete') bg-rose-100 text-rose-900 border border-rose-300
                @elseif($log->action === 'login') bg-indigo-100 text-indigo-900 border border-indigo-300
                @elseif($log->action === 'logout') bg-slate-200 text-slate-800 border border-slate-300
                @else bg-amber-100 text-amber-900 border border-amber-300
                @endif
              ">
                {{ $log->action }}
              </span>
            </td>

            <!-- Description -->
            <td class="py-4 px-6 text-slate-800 font-medium">
              {{ $log->description ?? '-' }}
            </td>

            <!-- IP Address -->
            <td class="py-4 px-4 font-mono text-[11px] text-slate-500 whitespace-nowrap">
              {{ $log->ip_address ?? '-' }}
            </td>

            <!-- Detail Modal Trigger -->
            <td class="py-4 px-6 text-right whitespace-nowrap">
              @if($log->old_data || $log->new_data)
                <button 
                  type="button" 
                  onclick="showLogDetail('{{ $log->id }}', '{{ e($log->module) }}', '{{ e($log->action) }}', '{{ e(json_encode($log->old_data)) }}', '{{ e(json_encode($log->new_data)) }}')"
                  class="px-3 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-800 text-xs font-bold border border-slate-300 transition-colors"
                >
                  Lihat Perubahan
                </button>
              @else
                <span class="text-slate-400 text-[11px] italic">Tidak ada perubahan</span>
              @endif
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="6" class="py-8 text-center text-slate-400 italic">Belum ada catatan log aktivitas yang ditemukan.</td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  <div class="p-4 border-t border-slate-200">
    {{ $logs->withQueryString()->links() }}
  </div>
</div>

<!-- Modal Detail Perubahan Data -->
<div id="log-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-900/50 backdrop-blur-xs p-4">
  <div class="bg-white rounded-2xl max-w-2xl w-full p-6 shadow-2xl border border-slate-100 transform transition-all">
    <div class="flex items-center justify-between border-b border-slate-200 pb-3 mb-4">
      <h3 id="modal-title" class="text-base font-bold text-slate-900">Detail Perubahan Data Audit Log</h3>
      <button onclick="closeLogModal()" class="text-slate-400 hover:text-slate-700 p-1"><i class="fa-solid fa-xmark text-base"></i></button>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs font-mono">
      <div>
        <span class="font-bold text-rose-700 block mb-1.5 font-sans">Data Lama (Before):</span>
        <pre id="modal-old-data" class="bg-slate-900 text-slate-200 p-3 rounded-xl overflow-x-auto h-48 text-[11px]"></pre>
      </div>

      <div>
        <span class="font-bold text-emerald-700 block mb-1.5 font-sans">Data Baru (After):</span>
        <pre id="modal-new-data" class="bg-slate-900 text-slate-200 p-3 rounded-xl overflow-x-auto h-48 text-[11px]"></pre>
      </div>
    </div>

    <div class="pt-4 mt-4 border-t border-slate-200 text-right">
      <button type="button" onclick="closeLogModal()" class="px-5 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-800 text-xs font-bold border border-slate-300 transition-colors">
        Tutup
      </button>
    </div>
  </div>
</div>

@push('scripts')
<script>
  function showLogDetail(id, module, action, oldDataRaw, newDataRaw) {
    const modal = document.getElementById('log-modal');
    const title = document.getElementById('modal-title');
    const oldPre = document.getElementById('modal-old-data');
    const newPre = document.getElementById('modal-new-data');

    title.textContent = `Detail Audit Log #${id} - ${module} (${action.toUpperCase()})`;

    try {
      const oldObj = JSON.parse(oldDataRaw || 'null');
      oldPre.textContent = oldObj ? JSON.stringify(oldObj, null, 2) : 'Null';
    } catch(e) { oldPre.textContent = oldDataRaw || 'Null'; }

    try {
      const newObj = JSON.parse(newDataRaw || 'null');
      newPre.textContent = newObj ? JSON.stringify(newObj, null, 2) : 'Null';
    } catch(e) { newPre.textContent = newDataRaw || 'Null'; }

    modal.classList.remove('hidden');
    modal.classList.add('flex');
  }

  function closeLogModal() {
    const modal = document.getElementById('log-modal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
  }
</script>
@endpush

@endsection
