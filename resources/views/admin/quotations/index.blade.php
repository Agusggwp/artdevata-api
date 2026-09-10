@extends('layouts.admin')

@section('title', 'Quotation Penawaran')

@section('content')
<div class="space-y-6">

  <!-- Header Section -->
  <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
    <div>
      <h1 class="text-xl font-bold text-slate-800">Quotation Penawaran Harga</h1>
      <p class="text-xs text-slate-500">Buat penawaran proyek resmi ARTDEVATA dengan perhitungan aman & cetak PDF.</p>
    </div>

    @if(Auth::guard('admin')->user()?->hasPermission('quotations.create'))
      <a href="{{ route('admin.quotations.create') }}" class="inline-flex items-center justify-center space-x-2 px-4 py-2.5 rounded-xl bg-[#14433B] hover:bg-[#0B443C] text-white text-xs font-semibold shadow-sm transition-all">
        <i class="fa-solid fa-plus"></i>
        <span>Buat Quotation Baru</span>
      </a>
    @endif
  </div>

  <!-- Filters & Search -->
  <div class="bg-white rounded-2xl p-4 shadow-card border border-slate-100">
    <form method="GET" action="{{ route('admin.quotations.index') }}" class="grid grid-cols-1 sm:grid-cols-3 gap-3">
      <div>
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nomor QT, nama klien, PT..." class="w-full text-xs rounded-xl border-slate-200 focus:border-[#14433B] focus:ring-[#14433B]">
      </div>
      <div>
        <select name="status" class="w-full text-xs rounded-xl border-slate-200 focus:border-[#14433B] focus:ring-[#14433B]">
          <option value="">-- Semua Status --</option>
          <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
          <option value="sent" {{ request('status') == 'sent' ? 'selected' : '' }}>Sent</option>
          <option value="viewed" {{ request('status') == 'viewed' ? 'selected' : '' }}>Viewed</option>
          <option value="accepted" {{ request('status') == 'accepted' ? 'selected' : '' }}>Accepted</option>
          <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
          <option value="expired" {{ request('status') == 'expired' ? 'selected' : '' }}>Expired</option>
          <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
        </select>
      </div>
      <div class="flex items-center space-x-2">
        <button type="submit" class="w-full px-4 py-2 rounded-xl bg-slate-800 text-white text-xs font-semibold hover:bg-slate-900 transition-colors">Filter</button>
        <a href="{{ route('admin.quotations.index') }}" class="px-3 py-2 rounded-xl bg-slate-100 text-slate-600 text-xs font-semibold hover:bg-slate-200 transition-colors">Reset</a>
      </div>
    </form>
  </div>

  <!-- Quotations Table Card -->
  <div class="bg-white rounded-2xl shadow-card border border-slate-100 overflow-hidden">
    <div class="overflow-x-auto">
      <table class="w-full text-left border-collapse">
        <thead>
          <tr class="bg-slate-50 border-b border-slate-100 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
            <th class="py-3.5 px-4">Nomor Quotation</th>
            <th class="py-3.5 px-4">Klien</th>
            <th class="py-3.5 px-4">Tgl Terbit</th>
            <th class="py-3.5 px-4">Berlaku s/d</th>
            <th class="py-3.5 px-4 text-right">Grand Total</th>
            <th class="py-3.5 px-4 text-center">Status</th>
            <th class="py-3.5 px-4 text-right">Aksi</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-100 text-xs text-slate-700">
          @forelse($quotations as $quotation)
            <tr class="hover:bg-slate-50/80 transition-colors">
              <td class="py-3.5 px-4 font-bold text-slate-900">
                <a href="{{ route('admin.quotations.show', $quotation->id) }}" class="hover:text-[#14433B]">
                  {{ $quotation->quotation_number }}
                </a>
              </td>
              <td class="py-3.5 px-4">
                <a href="{{ route('admin.clients.show', $quotation->client_id) }}" class="font-bold text-slate-900 hover:text-[#14433B] block">
                  {{ $quotation->client?->name ?? 'N/A' }}
                </a>
                <span class="text-[11px] text-slate-500 block">{{ $quotation->client?->company_name ?? $quotation->client?->company ?? 'Perorangan' }}</span>
              </td>
              <td class="py-3.5 px-4 text-slate-600">
                {{ $quotation->issue_date->format('d M Y') }}
              </td>
              <td class="py-3.5 px-4 text-slate-600">
                {{ $quotation->valid_until->format('d M Y') }}
              </td>
              <td class="py-3.5 px-4 text-right font-extrabold text-[#14433B]">
                Rp {{ number_format($quotation->total, 0, ',', '.') }}
              </td>
              <td class="py-3.5 px-4 text-center">
                {!! $quotation->status_badge !!}
              </td>
              <td class="py-3.5 px-4 text-right space-x-1">
                <a href="{{ route('admin.quotations.pdf', $quotation->id) }}" target="_blank" class="p-1.5 text-rose-600 hover:text-rose-800 rounded-lg hover:bg-rose-50 inline-block" title="Cetak / PDF">
                  <i class="fa-solid fa-file-pdf"></i>
                </a>

                @if($quotation->status === 'accepted' && !$quotation->project_id && Auth::guard('admin')->user()?->hasPermission('projects.create'))
                  <a href="{{ route('admin.quotations.create-project', $quotation->id) }}" class="inline-flex items-center px-2 py-1 rounded bg-emerald-600 hover:bg-emerald-700 text-white text-[11px] font-semibold transition-colors" title="Create Project">
                    <i class="fa-solid fa-diagram-project mr-1"></i> Project
                  </a>
                @endif

                @if(Auth::guard('admin')->user()?->hasPermission('quotations.edit'))
                  <a href="{{ route('admin.quotations.edit', $quotation->id) }}" class="p-1.5 text-slate-500 hover:text-slate-900 rounded-lg hover:bg-slate-100 inline-block">
                    <i class="fa-solid fa-pen-to-square"></i>
                  </a>
                @endif

                @if(Auth::guard('admin')->user()?->hasPermission('quotations.delete'))
                  <button type="button" onclick="confirmDelete('{{ route('admin.quotations.destroy', $quotation->id) }}', 'Hapus quotation {{ $quotation->quotation_number }}?')" class="p-1.5 text-rose-500 hover:text-rose-700 rounded-lg hover:bg-rose-50 inline-block">
                    <i class="fa-solid fa-trash-can"></i>
                  </button>
                @endif
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="7" class="py-8 text-center text-slate-400">
                <i class="fa-solid fa-file-signature text-3xl mb-2 text-slate-300"></i>
                <p class="text-xs">Belum ada data Quotation penawaran.</p>
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    @if($quotations->hasPages())
      <div class="p-4 border-t border-slate-100">
        {{ $quotations->links() }}
      </div>
    @endif
  </div>

</div>
@endsection
