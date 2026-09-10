@extends('layouts.admin')

@section('title', 'Invoice ' . $invoice->invoice_number)

@section('content')

<div class="max-w-4xl mx-auto space-y-6">
  
  <!-- Header Bar -->
  <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 print:hidden">
    <div class="flex items-center space-x-3">
      <div class="w-12 h-12 rounded-2xl bg-gradient-to-tr from-[#14433B] to-[#0E5D55] text-[#21C9A4] flex items-center justify-center font-bold text-xl shadow-xs">
        <i class="fa-solid fa-file-invoice-dollar"></i>
      </div>
      <div>
        <h1 class="text-2xl font-black text-slate-900 tracking-tight">Invoice {{ $invoice->invoice_number }}</h1>
        <p class="text-xs text-slate-500">Diterbitkan: {{ $invoice->invoice_date ? $invoice->invoice_date->format('d M Y') : '-' }}</p>
      </div>
    </div>

    <div class="flex items-center space-x-2">
      <button onclick="window.print()" class="px-4 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-900 text-white text-xs font-semibold shadow-xs transition-colors flex items-center space-x-2">
        <i class="fa-solid fa-print text-xs"></i>
        <span>Cetak / Save PDF</span>
      </button>
      <a href="{{ route('admin.invoices.index') }}" class="px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold transition-colors flex items-center space-x-2">
        <i class="fa-solid fa-arrow-left text-xs"></i>
        <span>Kembali</span>
      </a>
    </div>
  </div>

  <!-- Formal Invoice Card -->
  <div class="bg-white rounded-2xl p-8 sm:p-10 shadow-card border border-slate-200 space-y-8">
    
    <!-- Top Header Brand & Invoice Number -->
    <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-6 border-b border-slate-100 pb-8">
      <div>
        <div class="flex items-center space-x-3 mb-2">
          <div class="w-10 h-10 rounded-xl bg-[#14433B] text-[#21C9A4] font-black text-xl flex items-center justify-center">
            A
          </div>
          <span class="text-xl font-black tracking-tight text-[#14433B]">ARTDEVATA</span>
        </div>
        <p class="text-xs text-slate-500 max-w-xs leading-relaxed">
          Penyedia Layanan Software, Web Application Development & Digital Solution.
        </p>
      </div>

      <div class="text-left sm:text-right space-y-1">
        <span class="text-xs font-semibold uppercase tracking-wider text-slate-400 block">FAKTUR INVOICE</span>
        <h2 class="text-xl font-black text-slate-900 tracking-tight">{{ $invoice->invoice_number }}</h2>
        <div>
          {!! $invoice->status_badge !!}
        </div>
      </div>
    </div>

    <!-- Client & Date Details -->
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 text-xs">
      
      <!-- Billed To -->
      <div class="p-4 rounded-xl bg-slate-50 border border-slate-100 space-y-1">
        <span class="font-bold text-slate-400 uppercase tracking-wider text-[10px] block mb-1">Ditujukan Kepada (Klien):</span>
        <h3 class="font-bold text-slate-900 text-sm">{{ $invoice->client_name }}</h3>
        @if($invoice->client_email)
          <p class="text-slate-600"><i class="fa-solid fa-envelope text-[10px] mr-1.5 text-slate-400"></i>{{ $invoice->client_email }}</p>
        @endif
        @if($invoice->client_address)
          <p class="text-slate-600 mt-1 leading-relaxed"><i class="fa-solid fa-location-dot text-[10px] mr-1.5 text-slate-400"></i>{{ $invoice->client_address }}</p>
        @endif
      </div>

      <!-- Dates -->
      <div class="p-4 rounded-xl bg-slate-50 border border-slate-100 flex flex-col justify-center space-y-2">
        <div class="flex justify-between items-center border-b border-slate-200/60 pb-1.5">
          <span class="text-slate-500 font-medium">Tanggal Invoice:</span>
          <span class="font-bold text-slate-800">{{ $invoice->invoice_date ? $invoice->invoice_date->format('d M Y') : '-' }}</span>
        </div>
        <div class="flex justify-between items-center">
          <span class="text-slate-500 font-medium">Jatuh Tempo:</span>
          <span class="font-bold text-rose-600">{{ $invoice->due_date ? $invoice->due_date->format('d M Y') : '-' }}</span>
        </div>
      </div>

    </div>

    <!-- Items Table -->
    <div class="overflow-x-auto">
      <table class="w-full text-left border-collapse text-xs">
        <thead>
          <tr class="border-b border-slate-200 bg-slate-50 text-slate-500 uppercase tracking-wider text-[10px] font-bold">
            <th class="py-3 px-4">Deskripsi Item Pekerjaan</th>
            <th class="py-3 px-4 text-center">Qty</th>
            <th class="py-3 px-4 text-right">Harga Satuan</th>
            <th class="py-3 px-4 text-right">Subtotal</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
          @if(is_array($invoice->items))
            @foreach($invoice->items as $item)
              <tr>
                <td class="py-3.5 px-4 font-semibold text-slate-800">{{ $item['description'] ?? '-' }}</td>
                <td class="py-3.5 px-4 text-center text-slate-600">{{ $item['quantity'] ?? 1 }}</td>
                <td class="py-3.5 px-4 text-right text-slate-600">Rp {{ number_format($item['price'] ?? 0, 0, ',', '.') }}</td>
                <td class="py-3.5 px-4 text-right font-bold text-slate-900">
                  Rp {{ number_format(($item['quantity'] ?? 1) * ($item['price'] ?? 0), 0, ',', '.') }}
                </td>
              </tr>
            @endforeach
          @endif
        </tbody>
      </table>
    </div>

    <!-- Summary Totals -->
    <div class="flex justify-end pt-4 border-t border-slate-100">
      <div class="w-full sm:w-72 space-y-2 text-xs">
        <div class="flex justify-between text-slate-600">
          <span>Subtotal:</span>
          <span class="font-bold text-slate-900">Rp {{ number_format($invoice->subtotal, 0, ',', '.') }}</span>
        </div>
        <div class="flex justify-between text-slate-600">
          <span>PPN 11%:</span>
          <span class="font-bold text-slate-900">Rp {{ number_format($invoice->tax, 0, ',', '.') }}</span>
        </div>
        <div class="flex justify-between items-center text-base font-black text-[#14433B] pt-2 border-t border-slate-200">
          <span>Total Tagihan:</span>
          <span class="text-lg">Rp {{ number_format($invoice->total, 0, ',', '.') }}</span>
        </div>
      </div>
    </div>

    <!-- Notes & Footer Info -->
    @if($invoice->notes)
      <div class="p-4 rounded-xl bg-slate-50 border border-slate-100 text-xs">
        <span class="font-bold text-slate-700 block mb-1">Catatan Tambahan / Pembayaran:</span>
        <p class="text-slate-600 whitespace-pre-line leading-relaxed">{{ $invoice->notes }}</p>
      </div>
    @endif

  </div>

</div>

@endsection