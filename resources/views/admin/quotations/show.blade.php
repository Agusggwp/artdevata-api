@extends('layouts.admin')

@section('title', 'Detail Quotation ' . $quotation->quotation_number)

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

  <!-- Header Section -->
  <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
    <div class="flex items-center space-x-3">
      <div class="w-12 h-12 rounded-2xl bg-[#14433B] text-white flex items-center justify-center font-bold text-lg">
        <i class="fa-solid fa-file-signature text-xl"></i>
      </div>
      <div>
        <div class="flex items-center space-x-2">
          <h1 class="text-xl font-bold text-slate-800">{{ $quotation->quotation_number }}</h1>
          {!! $quotation->status_badge !!}
        </div>
        <p class="text-xs text-slate-500">Klien: <span class="font-bold text-slate-800">{{ $quotation->client?->name ?? 'N/A' }}</span> ({{ $quotation->client?->company_name ?? 'Perorangan' }})</p>
      </div>
    </div>

    <div class="flex items-center space-x-2">
      <a href="{{ route('admin.quotations.pdf', $quotation->id) }}" target="_blank" class="px-4 py-2.5 rounded-xl bg-rose-600 hover:bg-rose-700 text-white text-xs font-semibold shadow-sm transition-all flex items-center space-x-1.5">
        <i class="fa-solid fa-file-pdf"></i>
        <span>Download / Cetak PDF</span>
      </a>

      @if($quotation->status === 'accepted' && !$quotation->project_id && Auth::guard('admin')->user()?->hasPermission('projects.create'))
        <a href="{{ route('admin.quotations.create-project', $quotation->id) }}" class="px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold shadow-sm transition-all flex items-center space-x-1.5">
          <i class="fa-solid fa-diagram-project"></i>
          <span>Create Project</span>
        </a>
      @elseif($quotation->project_id)
        <a href="{{ route('admin.projects.show', $quotation->project_id) }}" class="px-4 py-2.5 rounded-xl bg-emerald-100 text-emerald-800 hover:bg-emerald-200 text-xs font-semibold transition-all flex items-center space-x-1.5">
          <i class="fa-solid fa-diagram-project"></i>
          <span>Lihat Proyek Terhubung</span>
        </a>
      @endif

      @if(Auth::guard('admin')->user()?->hasPermission('quotations.edit'))
        <a href="{{ route('admin.quotations.edit', $quotation->id) }}" class="px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold transition-colors">
          <i class="fa-solid fa-pen-to-square mr-1"></i> Edit
        </a>
      @endif

      <a href="{{ route('admin.quotations.index') }}" class="px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold transition-colors">
        Kembali
      </a>
    </div>
  </div>

  <!-- Quotation Invoice-style Card Preview -->
  <div class="bg-white rounded-2xl p-8 shadow-card border border-slate-100 space-y-6">
    
    <!-- Branding Header -->
    <div class="flex justify-between items-start border-b border-slate-100 pb-6">
      <div>
        <h2 class="text-2xl font-black text-[#14433B] tracking-tight">ARTDEVATA</h2>
        <p class="text-xs font-semibold text-slate-500 uppercase tracking-widest">IT Solusi & Software Development</p>
        <p class="text-xs text-slate-400 mt-1">artdevata.net • admin@artdevata.net</p>
      </div>
      <div class="text-right space-y-1 text-xs">
        <span class="px-3 py-1 rounded-full bg-slate-100 font-mono font-bold text-slate-800 text-sm inline-block">{{ $quotation->quotation_number }}</span>
        <div class="text-slate-500">Tanggal: <strong class="text-slate-800">{{ $quotation->issue_date->format('d M Y') }}</strong></div>
        <div class="text-slate-500">Berlaku s/d: <strong class="text-slate-800">{{ $quotation->valid_until->format('d M Y') }}</strong></div>
      </div>
    </div>

    <!-- Client Info -->
    <div class="p-4 rounded-xl bg-slate-50 text-xs space-y-1">
      <span class="text-[10px] font-bold text-[#14433B] uppercase tracking-wider block">Kepada Yth. Klien:</span>
      <div class="font-bold text-sm text-slate-900">{{ $quotation->client?->name }}</div>
      @if($quotation->client?->company_name)
        <div class="text-slate-700 font-semibold">{{ $quotation->client->company_name }}</div>
      @endif
      <div class="text-slate-500">{{ $quotation->client?->email ?? '-' }} • {{ $quotation->client?->phone ?? '-' }}</div>
      <div class="text-slate-500">{{ $quotation->client?->address ?? '-' }}</div>
    </div>

    <!-- Items Table -->
    <div class="overflow-x-auto">
      <table class="w-full text-left text-xs border-collapse">
        <thead>
          <tr class="bg-slate-100 border-b border-slate-200 text-slate-600 font-bold uppercase text-[10px]">
            <th class="py-2.5 px-3 w-12 text-center">No</th>
            <th class="py-2.5 px-3">Deskripsi Pekerjaan</th>
            <th class="py-2.5 px-3 text-center w-16">Qty</th>
            <th class="py-2.5 px-3 text-right w-32">Harga Satuan</th>
            <th class="py-2.5 px-3 text-right w-24">Diskon</th>
            <th class="py-2.5 px-3 text-right w-36">Subtotal</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-100 text-slate-800">
          @foreach($quotation->items as $index => $item)
            <tr class="hover:bg-slate-50">
              <td class="py-3 px-3 text-center text-slate-400 font-semibold">{{ $index + 1 }}</td>
              <td class="py-3 px-3">
                <div class="font-semibold text-slate-900">{{ $item->description }}</div>
                @if($item->service)
                  <span class="text-[10px] text-slate-400">Master Service: {{ $item->service->title }}</span>
                @endif
              </td>
              <td class="py-3 px-3 text-center">{{ $item->quantity }} {{ $item->unit }}</td>
              <td class="py-3 px-3 text-right">Rp {{ number_format($item->unit_price, 0, ',', '.') }}</td>
              <td class="py-3 px-3 text-right text-rose-600">Rp {{ number_format($item->discount, 0, ',', '.') }}</td>
              <td class="py-3 px-3 text-right font-bold">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>

    <!-- Calculation Footer -->
    <div class="flex flex-col sm:flex-row justify-between items-start gap-6 border-t border-slate-100 pt-4 text-xs">
      <div class="space-y-4 flex-1">
        @if($quotation->terms)
          <div>
            <span class="font-bold text-slate-800 block mb-1">Syarat & Ketentuan:</span>
            <div class="text-slate-600 whitespace-pre-line p-3 rounded-xl bg-slate-50">{{ $quotation->terms }}</div>
          </div>
        @endif
        @if($quotation->notes)
          <div>
            <span class="font-bold text-slate-800 block mb-1">Catatan Tambahan:</span>
            <div class="text-slate-600 whitespace-pre-line p-3 rounded-xl bg-slate-50">{{ $quotation->notes }}</div>
          </div>
        @endif
      </div>

      <div class="w-full sm:w-72 space-y-2 p-4 bg-slate-50 rounded-xl">
        <div class="flex justify-between text-slate-600">
          <span>Subtotal</span>
          <span class="font-semibold text-slate-800">Rp {{ number_format($quotation->subtotal, 0, ',', '.') }}</span>
        </div>
        <div class="flex justify-between text-slate-600">
          <span>Diskon</span>
          <span class="font-semibold text-rose-600">- Rp {{ number_format($quotation->discount, 0, ',', '.') }}</span>
        </div>
        <div class="flex justify-between text-slate-600">
          <span>Pajak (PPN)</span>
          <span class="font-semibold text-slate-800">+ Rp {{ number_format($quotation->tax, 0, ',', '.') }}</span>
        </div>
        <div class="flex justify-between text-base font-extrabold text-[#14433B] pt-2 border-t border-slate-200">
          <span>Grand Total</span>
          <span>Rp {{ number_format($quotation->total, 0, ',', '.') }}</span>
        </div>
      </div>
    </div>

  </div>

</div>
@endsection
