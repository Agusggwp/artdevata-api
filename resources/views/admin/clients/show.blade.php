@extends('layouts.admin')

@section('title', 'Client 360° - ' . $client->name)

@section('content')
<div class="space-y-6">

  <!-- Client 360 Header -->
  <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
    <div class="flex items-center space-x-4">
      <div class="w-14 h-14 rounded-2xl bg-gradient-to-tr from-[#14433B] to-[#0E5D55] text-white flex items-center justify-center font-bold text-xl shadow-md">
        @if($client->logo)
          <img src="{{ asset('storage/' . $client->logo) }}" alt="{{ $client->name }}" class="w-full h-full object-cover rounded-2xl">
        @else
          {{ strtoupper(substr($client->name, 0, 1)) }}
        @endif
      </div>
      <div>
        <div class="flex items-center space-x-3">
          <h1 class="text-2xl font-bold text-slate-800">{{ $client->name }}</h1>
          {!! $client->status_badge !!}
        </div>
        <p class="text-xs text-slate-500 font-medium">
          <i class="fa-regular fa-building text-slate-400 mr-1"></i> {{ $client->company_name ?? $client->company ?? 'Perorangan' }}
          @if($client->website)
            • <a href="{{ Str::startsWith($client->website, 'http') ? $client->website : 'https://' . $client->website }}" target="_blank" class="text-sky-600 hover:underline"><i class="fa-solid fa-globe mr-1"></i> {{ $client->website }}</a>
          @endif
        </p>
      </div>
    </div>

    <div class="flex items-center space-x-2">
      @if(Auth::guard('admin')->user()?->hasPermission('quotations.create'))
        <a href="{{ route('admin.quotations.create', ['client_id' => $client->id]) }}" class="px-4 py-2.5 rounded-xl bg-[#14433B] hover:bg-[#0B443C] text-white text-xs font-semibold shadow-sm transition-all flex items-center space-x-1.5">
          <i class="fa-solid fa-plus"></i>
          <span>Buat Quotation</span>
        </a>
      @endif

      @if(Auth::guard('admin')->user()?->hasPermission('clients.edit'))
        <a href="{{ route('admin.clients.edit', $client->id) }}" class="px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold transition-colors">
          <i class="fa-solid fa-pen-to-square mr-1"></i> Edit Klien
        </a>
      @endif

      <a href="{{ route('admin.clients.index') }}" class="px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold transition-colors">
        Kembali
      </a>
    </div>
  </div>

  <!-- Financial 360 Summary Cards -->
  <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-5 gap-4">
    <div class="bg-white rounded-2xl p-4 shadow-card border border-slate-100">
      <span class="text-[11px] font-bold uppercase text-slate-400 block mb-1">Total Invoice</span>
      <span class="text-base font-extrabold text-slate-800">Rp {{ number_format($totalInvoice, 0, ',', '.') }}</span>
    </div>
    <div class="bg-white rounded-2xl p-4 shadow-card border border-slate-100">
      <span class="text-[11px] font-bold uppercase text-emerald-600 block mb-1">Lunas (Paid)</span>
      <span class="text-base font-extrabold text-emerald-600">Rp {{ number_format($totalPaid, 0, ',', '.') }}</span>
    </div>
    <div class="bg-white rounded-2xl p-4 shadow-card border border-slate-100">
      <span class="text-[11px] font-bold uppercase text-sky-600 block mb-1">Pending</span>
      <span class="text-base font-extrabold text-sky-600">Rp {{ number_format($totalPending, 0, ',', '.') }}</span>
    </div>
    <div class="bg-white rounded-2xl p-4 shadow-card border border-slate-100">
      <span class="text-[11px] font-bold uppercase text-rose-600 block mb-1">Overdue</span>
      <span class="text-base font-extrabold text-rose-600">Rp {{ number_format($totalOverdue, 0, ',', '.') }}</span>
    </div>
    <div class="bg-white rounded-2xl p-4 shadow-card border border-slate-100 col-span-2 sm:col-span-1">
      <span class="text-[11px] font-bold uppercase text-[#14433B] block mb-1">Total Revenue</span>
      <span class="text-base font-extrabold text-[#14433B]">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</span>
    </div>
  </div>

  <!-- Main Grid: Info + Tabs Content -->
  <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    <!-- Contact & Details Column -->
    <div class="space-y-6">
      <div class="bg-white rounded-2xl p-6 shadow-card border border-slate-100 space-y-4">
        <h3 class="text-sm font-bold text-slate-800 border-b border-slate-100 pb-3 flex items-center space-x-2">
          <i class="fa-solid fa-address-book text-[#14433B]"></i>
          <span>Detail Kontak Klien</span>
        </h3>

        <div class="space-y-3 text-xs">
          <div>
            <span class="text-slate-400 block text-[11px]">Email</span>
            <span class="font-semibold text-slate-800">{{ $client->email ?? '-' }}</span>
          </div>
          <div>
            <span class="text-slate-400 block text-[11px]">Nomor Telepon</span>
            <span class="font-semibold text-slate-800">{{ $client->phone ?? '-' }}</span>
          </div>
          <div>
            <span class="text-slate-400 block text-[11px]">Tax ID / NPWP</span>
            <span class="font-semibold text-slate-800">{{ $client->tax_id ?? '-' }}</span>
          </div>
          <div>
            <span class="text-slate-400 block text-[11px]">Alamat</span>
            <span class="text-slate-700 block whitespace-pre-line">{{ $client->address ?? '-' }}</span>
          </div>
          <div>
            <span class="text-slate-400 block text-[11px]">Catatan Klien</span>
            <div class="p-3 rounded-xl bg-slate-50 text-slate-700 mt-1 whitespace-pre-line">{{ $client->notes ?? 'Tidak ada catatan.' }}</div>
          </div>
        </div>
      </div>

      <!-- Activity Log Timeline -->
      <div class="bg-white rounded-2xl p-6 shadow-card border border-slate-100 space-y-4">
        <h3 class="text-sm font-bold text-slate-800 border-b border-slate-100 pb-3 flex items-center space-x-2">
          <i class="fa-solid fa-clock-rotate-left text-[#14433B]"></i>
          <span>Timeline Aktivitas</span>
        </h3>

        <div class="space-y-4 relative before:absolute before:inset-0 before:left-2.5 before:w-0.5 before:bg-slate-100 pl-6">
          @forelse($activityLogs as $log)
            <div class="relative text-xs">
              <div class="absolute -left-6 top-1 w-2.5 h-2.5 rounded-full bg-[#14433B]"></div>
              <div class="font-semibold text-slate-800">{{ $log->description }}</div>
              <div class="text-[10px] text-slate-400">{{ $log->created_at->format('d M Y, H:i') }} • {{ $log->user_name ?? 'System' }}</div>
            </div>
          @empty
            <p class="text-xs text-slate-400 py-2">Belum ada catatan aktivitas.</p>
          @endforelse
        </div>
      </div>
    </div>

    <!-- Right Column: Projects, Quotations & Invoices -->
    <div class="lg:col-span-2 space-y-6">

      <!-- Projects Section -->
      <div class="bg-white rounded-2xl p-6 shadow-card border border-slate-100 space-y-4">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
          <h3 class="text-sm font-bold text-slate-800 flex items-center space-x-2">
            <i class="fa-solid fa-diagram-project text-[#14433B]"></i>
            <span>Daftar Proyek Klien ({{ $client->projects->count() }})</span>
          </h3>
          @if(Auth::guard('admin')->user()?->hasPermission('projects.create'))
            <a href="{{ route('admin.projects.create') }}" class="text-xs text-[#14433B] font-bold hover:underline">+ Proyek Baru</a>
          @endif
        </div>

        <div class="space-y-3">
          @forelse($client->projects as $project)
            <div class="p-4 rounded-xl border border-slate-100 hover:border-slate-200 bg-slate-50/50 transition-all flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-xs">
              <div class="space-y-1">
                <a href="{{ route('admin.projects.show', $project->id) }}" class="font-bold text-slate-900 hover:text-[#14433B] text-sm block">
                  {{ $project->name }}
                </a>
                <div class="flex items-center space-x-2 text-[11px] text-slate-500">
                  <span>Budget: <strong class="text-slate-800">Rp {{ number_format($project->budget, 0, ',', '.') }}</strong></span>
                  <span>•</span>
                  <span>{!! $project->status_badge !!}</span>
                </div>
              </div>

              <div class="w-full sm:w-36 space-y-1">
                <div class="flex items-center justify-between text-[11px] text-slate-600 font-semibold">
                  <span>Progress</span>
                  <span>{{ $project->progress }}%</span>
                </div>
                <div class="w-full h-2 rounded-full bg-slate-200 overflow-hidden">
                  <div class="h-full bg-[#21C9A4] rounded-full" style="width: {{ $project->progress }}%"></div>
                </div>
              </div>
            </div>
          @empty
            <p class="text-xs text-slate-400 py-4 text-center">Belum ada proyek untuk klien ini.</p>
          @endforelse
        </div>
      </div>

      <!-- Quotations Section -->
      <div class="bg-white rounded-2xl p-6 shadow-card border border-slate-100 space-y-4">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
          <h3 class="text-sm font-bold text-slate-800 flex items-center space-x-2">
            <i class="fa-solid fa-file-signature text-[#14433B]"></i>
            <span>Penawaran / Quotations ({{ $client->quotations->count() }})</span>
          </h3>
        </div>

        <div class="overflow-x-auto">
          <table class="w-full text-left text-xs">
            <thead>
              <tr class="text-[11px] font-bold text-slate-400 uppercase border-b border-slate-100">
                <th class="pb-2">Nomor</th>
                <th class="pb-2">Tanggal</th>
                <th class="pb-2 text-right">Total</th>
                <th class="pb-2 text-center">Status</th>
                <th class="pb-2 text-right">Aksi</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 text-slate-700">
              @forelse($client->quotations as $quotation)
                <tr class="hover:bg-slate-50">
                  <td class="py-2.5 font-bold text-slate-900">
                    <a href="{{ route('admin.quotations.show', $quotation->id) }}" class="hover:text-[#14433B]">{{ $quotation->quotation_number }}</a>
                  </td>
                  <td class="py-2.5 text-slate-500">{{ $quotation->issue_date->format('d M Y') }}</td>
                  <td class="py-2.5 text-right font-bold text-slate-800">Rp {{ number_format($quotation->total, 0, ',', '.') }}</td>
                  <td class="py-2.5 text-center">{!! $quotation->status_badge !!}</td>
                  <td class="py-2.5 text-right space-x-1">
                    <a href="{{ route('admin.quotations.pdf', $quotation->id) }}" target="_blank" class="p-1 text-rose-600 hover:text-rose-800" title="Cetak PDF"><i class="fa-solid fa-file-pdf"></i></a>
                    <a href="{{ route('admin.quotations.show', $quotation->id) }}" class="p-1 text-slate-600 hover:text-slate-900"><i class="fa-solid fa-eye"></i></a>
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="5" class="py-4 text-center text-slate-400">Belum ada quotation penawaran.</td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>

      <!-- Invoices Section -->
      <div class="bg-white rounded-2xl p-6 shadow-card border border-slate-100 space-y-4">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
          <h3 class="text-sm font-bold text-slate-800 flex items-center space-x-2">
            <i class="fa-solid fa-file-invoice-dollar text-[#14433B]"></i>
            <span>Tagihan / Invoices ({{ $client->invoices->count() }})</span>
          </h3>
        </div>

        <div class="overflow-x-auto">
          <table class="w-full text-left text-xs">
            <thead>
              <tr class="text-[11px] font-bold text-slate-400 uppercase border-b border-slate-100">
                <th class="pb-2">Nomor Invoice</th>
                <th class="pb-2">Jatuh Tempo</th>
                <th class="pb-2 text-right">Total</th>
                <th class="pb-2 text-center">Status</th>
                <th class="pb-2 text-right">Aksi</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 text-slate-700">
              @forelse($client->invoices as $inv)
                <tr class="hover:bg-slate-50">
                  <td class="py-2.5 font-bold text-slate-900">
                    <a href="{{ route('admin.invoices.show', $inv->id) }}" class="hover:text-[#14433B]">{{ $inv->invoice_number }}</a>
                  </td>
                  <td class="py-2.5 text-slate-500">{{ $inv->due_date ? $inv->due_date->format('d M Y') : '-' }}</td>
                  <td class="py-2.5 text-right font-bold text-slate-800">Rp {{ number_format($inv->total, 0, ',', '.') }}</td>
                  <td class="py-2.5 text-center">{!! $inv->status_badge !!}</td>
                  <td class="py-2.5 text-right">
                    <a href="{{ route('admin.invoices.show', $inv->id) }}" class="p-1 text-slate-600 hover:text-slate-900"><i class="fa-solid fa-eye"></i></a>
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="5" class="py-4 text-center text-slate-400">Belum ada invoice tagihan.</td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>

    </div>

  </div>

</div>
@endsection
