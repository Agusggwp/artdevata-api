@extends('layouts.admin')

@section('title', 'Leads Prospek')

@section('content')
<div class="space-y-6">

  <!-- Header Section -->
  <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
    <div>
      <h1 class="text-xl font-bold text-slate-800">Leads & CRM Prospek</h1>
      <p class="text-xs text-slate-500">Kelola calon klien, sumber lead, dan alur konversi proyek.</p>
    </div>

    @if(Auth::guard('admin')->user()?->hasPermission('leads.create'))
      <a href="{{ route('admin.leads.create') }}" class="inline-flex items-center justify-center space-x-2 px-4 py-2.5 rounded-xl bg-[#14433B] hover:bg-[#0B443C] text-white text-xs font-semibold shadow-sm transition-all">
        <i class="fa-solid fa-plus"></i>
        <span>Tambah Lead Baru</span>
      </a>
    @endif
  </div>

  <!-- Filters & Search -->
  <div class="bg-white rounded-2xl p-4 shadow-card border border-slate-100">
    <form method="GET" action="{{ route('admin.leads.index') }}" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-3">
      <div>
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama, PT, email, HP..." class="w-full text-xs rounded-xl border-slate-200 focus:border-[#14433B] focus:ring-[#14433B]">
      </div>
      <div>
        <select name="status" class="w-full text-xs rounded-xl border-slate-200 focus:border-[#14433B] focus:ring-[#14433B]">
          <option value="">-- Semua Status --</option>
          <option value="new" {{ request('status') == 'new' ? 'selected' : '' }}>New</option>
          <option value="contacted" {{ request('status') == 'contacted' ? 'selected' : '' }}>Contacted</option>
          <option value="qualified" {{ request('status') == 'qualified' ? 'selected' : '' }}>Qualified</option>
          <option value="negotiation" {{ request('status') == 'negotiation' ? 'selected' : '' }}>Negotiation</option>
          <option value="won" {{ request('status') == 'won' ? 'selected' : '' }}>Won</option>
          <option value="lost" {{ request('status') == 'lost' ? 'selected' : '' }}>Lost</option>
        </select>
      </div>
      <div>
        <select name="source" class="w-full text-xs rounded-xl border-slate-200 focus:border-[#14433B] focus:ring-[#14433B]">
          <option value="">-- Semua Sumber --</option>
          <option value="website" {{ request('source') == 'website' ? 'selected' : '' }}>Website</option>
          <option value="whatsapp" {{ request('source') == 'whatsapp' ? 'selected' : '' }}>WhatsApp</option>
          <option value="instagram" {{ request('source') == 'instagram' ? 'selected' : '' }}>Instagram</option>
          <option value="tiktok" {{ request('source') == 'tiktok' ? 'selected' : '' }}>TikTok</option>
          <option value="facebook" {{ request('source') == 'facebook' ? 'selected' : '' }}>Facebook</option>
          <option value="referral" {{ request('source') == 'referral' ? 'selected' : '' }}>Referral</option>
          <option value="walk_in" {{ request('source') == 'walk_in' ? 'selected' : '' }}>Walk-in</option>
          <option value="other" {{ request('source') == 'other' ? 'selected' : '' }}>Other</option>
        </select>
      </div>
      <div class="flex items-center space-x-2">
        <button type="submit" class="w-full px-4 py-2 rounded-xl bg-slate-800 text-white text-xs font-semibold hover:bg-slate-900 transition-colors">Filter</button>
        <a href="{{ route('admin.leads.index') }}" class="px-3 py-2 rounded-xl bg-slate-100 text-slate-600 text-xs font-semibold hover:bg-slate-200 transition-colors">Reset</a>
      </div>
    </form>
  </div>

  <!-- Leads Table Card -->
  <div class="bg-white rounded-2xl shadow-card border border-slate-100 overflow-hidden">
    <div class="overflow-x-auto">
      <table class="w-full text-left border-collapse">
        <thead>
          <tr class="bg-slate-50 border-b border-slate-100 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
            <th class="py-3.5 px-4">Nama Prospek</th>
            <th class="py-3.5 px-4">Kontak</th>
            <th class="py-3.5 px-4">Sumber</th>
            <th class="py-3.5 px-4">Layanan Minat</th>
            <th class="py-3.5 px-4 text-right">Estimasi Budget</th>
            <th class="py-3.5 px-4 text-center">Status</th>
            <th class="py-3.5 px-4">Assigned To</th>
            <th class="py-3.5 px-4 text-right">Aksi</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-100 text-xs text-slate-700">
          @forelse($leads as $lead)
            <tr class="hover:bg-slate-50/80 transition-colors">
              <td class="py-3.5 px-4">
                <a href="{{ route('admin.leads.show', $lead->id) }}" class="font-bold text-slate-900 hover:text-[#14433B] block">
                  {{ $lead->name }}
                </a>
                @if($lead->company_name)
                  <span class="text-[11px] text-slate-500 block"><i class="fa-regular fa-building text-[10px]"></i> {{ $lead->company_name }}</span>
                @endif
              </td>
              <td class="py-3.5 px-4">
                <div>{{ $lead->email ?? '-' }}</div>
                <div class="text-[11px] text-slate-500">{{ $lead->phone ?? '-' }}</div>
              </td>
              <td class="py-3.5 px-4">
                <span class="inline-block px-2 py-0.5 rounded text-[10px] font-semibold bg-slate-100 text-slate-700 uppercase">
                  {{ $lead->source }}
                </span>
              </td>
              <td class="py-3.5 px-4 font-medium text-slate-800">
                {{ $lead->service_interest ?? '-' }}
              </td>
              <td class="py-3.5 px-4 text-right font-bold text-slate-900">
                Rp {{ number_format($lead->estimated_budget, 0, ',', '.') }}
              </td>
              <td class="py-3.5 px-4 text-center">
                {!! $lead->status_badge !!}
              </td>
              <td class="py-3.5 px-4 text-slate-600">
                {{ $lead->assignedStaff?->name ?? 'Belum Ditugaskan' }}
              </td>
              <td class="py-3.5 px-4 text-right space-x-1">
                @if($lead->client_id)
                  <a href="{{ route('admin.clients.show', $lead->client_id) }}" title="Lihat Client 360°" class="inline-flex items-center px-2 py-1 rounded bg-emerald-100 text-emerald-800 hover:bg-emerald-200 text-[11px] font-semibold transition-colors">
                    <i class="fa-solid fa-user-check mr-1"></i> Client
                  </a>
                @elseif(Auth::guard('admin')->user()?->hasPermission('leads.convert'))
                  <form method="POST" action="{{ route('admin.leads.convert', $lead->id) }}" class="inline">
                    @csrf
                    <button type="submit" title="Convert to Client" class="inline-flex items-center px-2 py-1 rounded bg-sky-600 hover:bg-sky-700 text-white text-[11px] font-semibold transition-colors">
                      <i class="fa-solid fa-arrow-right-to-bracket mr-1"></i> Convert
                    </button>
                  </form>
                @endif

                @if(Auth::guard('admin')->user()?->hasPermission('leads.edit'))
                  <a href="{{ route('admin.leads.edit', $lead->id) }}" class="p-1.5 text-slate-500 hover:text-slate-900 rounded-lg hover:bg-slate-100 inline-block">
                    <i class="fa-solid fa-pen-to-square"></i>
                  </a>
                @endif

                @if(Auth::guard('admin')->user()?->hasPermission('leads.delete'))
                  <button type="button" onclick="confirmDelete('{{ route('admin.leads.destroy', $lead->id) }}', 'Hapus lead {{ $lead->name }}?')" class="p-1.5 text-rose-500 hover:text-rose-700 rounded-lg hover:bg-rose-50 inline-block">
                    <i class="fa-solid fa-trash-can"></i>
                  </button>
                @endif
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="8" class="py-8 text-center text-slate-400">
                <i class="fa-solid fa-user-tag text-3xl mb-2 text-slate-300"></i>
                <p class="text-xs">Belum ada data Lead prospek.</p>
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    @if($leads->hasPages())
      <div class="p-4 border-t border-slate-100">
        {{ $leads->links() }}
      </div>
    @endif
  </div>

</div>
@endsection
