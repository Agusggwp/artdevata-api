@extends('layouts.admin')

@section('title', 'Dashboard SaaS Overview')

@section('content')

<!-- Welcome Banner -->
<div class="relative overflow-hidden rounded-2xl bg-[#14433B] p-6 text-white shadow-lg border border-[#0E5D55]">
  <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-4">
    <div>
      <div class="inline-flex items-center space-x-2 px-3 py-1 rounded-full bg-[#0E5D55] text-[#21C9A4] border border-[#21C9A4]/30 text-xs font-bold mb-3">
        <i class="fa-solid fa-sparkles"></i>
        <span>Selamat datang kembali</span>
      </div>
      <h1 class="text-2xl md:text-3xl font-black tracking-tight text-white">Halo, {{ Auth::guard('admin')->user()->name ?? 'Administrator' }}!</h1>
      <p class="text-xs md:text-sm text-emerald-100 font-medium mt-1 max-w-xl">Ringkasan performa bisnis, proyek aktif, invoice, dan keuangan ARTDEVATA terkini.</p>
    </div>

    <!-- Quick Action Header Buttons -->
    <div class="flex items-center space-x-3 shrink-0">
      <a href="{{ route('admin.projects.create') }}" class="px-4 py-2.5 rounded-xl bg-[#21C9A4] hover:bg-[#1bb895] text-[#14433B] text-xs font-extrabold shadow-md transition-all duration-200 flex items-center space-x-2">
        <i class="fa-solid fa-plus text-xs"></i>
        <span>Proyek Baru</span>
      </a>
      <a href="{{ route('admin.invoices.create') }}" class="px-4 py-2.5 rounded-xl bg-white hover:bg-slate-100 text-[#14433B] text-xs font-extrabold shadow-md transition-all duration-200 flex items-center space-x-2 border border-slate-200">
        <i class="fa-solid fa-file-circle-plus text-xs"></i>
        <span>Buat Invoice</span>
      </a>
    </div>
  </div>
</div>

<!-- Primary Financial Balance Hero Card -->
<div class="bg-white rounded-2xl p-6 shadow-card border border-slate-300 flex flex-col lg:flex-row lg:items-center justify-between gap-6">
  <div class="flex items-start space-x-4">
    <div class="w-14 h-14 rounded-2xl bg-[#14433B] text-[#21C9A4] flex items-center justify-center text-2xl shadow-md shrink-0 border border-[#0E5D55]">
      <i class="fa-solid fa-vault"></i>
    </div>
    <div>
      <span class="text-xs font-bold uppercase tracking-wider text-slate-700">Total Saldo Perusahaan</span>
      <div class="text-3xl md:text-4xl font-black text-slate-900 mt-0.5 tracking-tight">
        Rp {{ number_format($companyBalance ?? 0, 0, ',', '.') }}
      </div>
      <p class="text-xs text-slate-700 font-semibold mt-1 flex items-center space-x-1">
        <i class="fa-solid fa-circle-info text-[#14433B]"></i>
        <span>Total Invoice Dibayar dikurangi Gaji Terbayar + Penyesuaian Kas</span>
      </p>
    </div>
  </div>

  <div class="flex flex-wrap items-center gap-3 border-t lg:border-t-0 lg:border-l border-slate-200 pt-4 lg:pt-0 lg:pl-6">
    <a href="{{ route('admin.salaries.index') }}" class="px-4 py-2.5 rounded-xl bg-amber-600 hover:bg-amber-700 text-white text-xs font-extrabold shadow-xs transition-all duration-200 flex items-center space-x-2">
      <i class="fa-solid fa-wallet"></i>
      <span>Kelola Gaji & Payroll</span>
    </a>
    <a href="{{ route('admin.finance.transactions.index') }}" class="px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-900 text-xs font-extrabold border border-slate-300 transition-all duration-200 flex items-center space-x-2">
      <i class="fa-solid fa-list-check"></i>
      <span>Riwayat Transaksi</span>
    </a>
  </div>
</div>

<!-- Stat Cards Grid -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 md:gap-6">
  
  <!-- Projects Stat -->
  <div class="bg-white rounded-2xl p-5 shadow-card border border-slate-300 hover:border-[#14433B] transition-all duration-200 group">
    <div class="flex items-center justify-between">
      <span class="text-xs font-extrabold text-slate-800">Total Proyek</span>
      <div class="w-10 h-10 rounded-xl bg-[#14433B] text-[#21C9A4] flex items-center justify-center text-base group-hover:scale-110 transition-transform shadow-xs">
        <i class="fa-solid fa-diagram-project"></i>
      </div>
    </div>
    <div class="mt-3 flex items-baseline justify-between">
      <span class="text-3xl font-black text-slate-900 tracking-tight">{{ $totalProjects ?? 0 }}</span>
      <span class="text-xs font-extrabold text-emerald-950 bg-emerald-100 px-2.5 py-1 rounded-lg border border-emerald-400 shadow-xs">{{ $ongoingProjects ?? 0 }} Aktif</span>
    </div>
    <div class="w-full bg-slate-200 h-2.5 rounded-full mt-3 overflow-hidden">
      @php
        $projectPercent = ($totalProjects ?? 0) > 0 ? round((($ongoingProjects ?? 0) / $totalProjects) * 100) : 0;
      @endphp
      <div class="bg-[#14433B] h-full rounded-full" style="width: {{ $projectPercent }}%"></div>
    </div>
  </div>

  <!-- Invoices Stat -->
  <div class="bg-white rounded-2xl p-5 shadow-card border border-slate-300 hover:border-blue-600 transition-all duration-200 group">
    <div class="flex items-center justify-between">
      <span class="text-xs font-extrabold text-slate-800">Invoice Terbayar</span>
      <div class="w-10 h-10 rounded-xl bg-blue-700 text-white flex items-center justify-center text-base group-hover:scale-110 transition-transform shadow-xs">
        <i class="fa-solid fa-file-invoice-dollar"></i>
      </div>
    </div>
    <div class="mt-3 flex items-baseline justify-between">
      <span class="text-3xl font-black text-slate-900 tracking-tight">{{ $paidInvoiceCount ?? 0 }} <span class="text-xs text-slate-700 font-bold">/ {{ $totalInvoiceCount ?? 0 }}</span></span>
      <span class="text-xs font-extrabold text-blue-950 bg-blue-100 px-2.5 py-1 rounded-lg border border-blue-400 shadow-xs">Rp {{ number_format($totalPaidAmount ?? 0, 0, ',', '.') }}</span>
    </div>
    <div class="w-full bg-slate-200 h-2.5 rounded-full mt-3 overflow-hidden">
      @php
        $invPercent = ($totalInvoiceCount ?? 0) > 0 ? round((($paidInvoiceCount ?? 0) / $totalInvoiceCount) * 100) : 0;
      @endphp
      <div class="bg-blue-700 h-full rounded-full" style="width: {{ $invPercent }}%"></div>
    </div>
  </div>

  <!-- Clients Stat -->
  <div class="bg-white rounded-2xl p-5 shadow-card border border-slate-300 hover:border-purple-600 transition-all duration-200 group">
    <div class="flex items-center justify-between">
      <span class="text-xs font-extrabold text-slate-800">Total Klien</span>
      <div class="w-10 h-10 rounded-xl bg-purple-700 text-white flex items-center justify-center text-base group-hover:scale-110 transition-transform shadow-xs">
        <i class="fa-solid fa-users"></i>
      </div>
    </div>
    <div class="mt-3 flex items-baseline justify-between">
      <span class="text-3xl font-black text-slate-900 tracking-tight">{{ $totalClients ?? 0 }}</span>
      <span class="text-xs font-extrabold text-purple-950 bg-purple-100 px-2.5 py-1 rounded-lg border border-purple-400 shadow-xs">Mitra Kerja</span>
    </div>
    <div class="w-full bg-slate-200 h-2.5 rounded-full mt-3 overflow-hidden">
      <div class="bg-purple-700 h-full rounded-full w-full"></div>
    </div>
  </div>

  <!-- Content Services/Portfolio Stat -->
  <div class="bg-white rounded-2xl p-5 shadow-card border border-slate-300 hover:border-amber-600 transition-all duration-200 group">
    <div class="flex items-center justify-between">
      <span class="text-xs font-extrabold text-slate-800">Layanan & Portofolio</span>
      <div class="w-10 h-10 rounded-xl bg-amber-600 text-white flex items-center justify-center text-base group-hover:scale-110 transition-transform shadow-xs">
        <i class="fa-solid fa-cubes"></i>
      </div>
    </div>
    <div class="mt-3 flex items-baseline justify-between">
      <span class="text-3xl font-black text-slate-900 tracking-tight">{{ ($totalServices ?? 0) + ($totalPortfolios ?? 0) }}</span>
      <span class="text-xs font-extrabold text-amber-950 bg-amber-100 px-2.5 py-1 rounded-lg border border-amber-400 shadow-xs">{{ $totalServices ?? 0 }} Layanan</span>
    </div>
    <div class="w-full bg-slate-200 h-2.5 rounded-full mt-3 overflow-hidden">
      <div class="bg-amber-600 h-full rounded-full w-full"></div>
    </div>
  </div>

</div>

<!-- Charts & Main Overview Section -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
  
  <!-- Left Side: Interactive Chart -->
  <div class="lg:col-span-2 bg-white rounded-2xl p-6 shadow-card border border-slate-200">
    <div class="flex items-center justify-between mb-6">
      <div>
        <h2 class="text-base font-bold text-slate-900">Status & Ringkasan Proyek</h2>
        <p class="text-xs text-slate-700 font-medium">Distribusi status pengerjaan proyek ARTDEVATA</p>
      </div>
      <div class="flex items-center space-x-3 text-xs font-medium">
        <span class="flex items-center space-x-1"><span class="w-2.5 h-2.5 rounded-full bg-[#14433B]"></span><span class="text-slate-800 font-semibold">Ongoing ({{ $ongoingProjects ?? 0 }})</span></span>
        <span class="flex items-center space-x-1"><span class="w-2.5 h-2.5 rounded-full bg-[#21C9A4]"></span><span class="text-slate-800 font-semibold">Selesai ({{ $completedProjects ?? 0 }})</span></span>
        <span class="flex items-center space-x-1"><span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span><span class="text-slate-800 font-semibold">Pending ({{ $pendingProjects ?? 0 }})</span></span>
      </div>
    </div>

    <!-- Chart Container -->
    <div class="h-64 relative flex items-center justify-center">
      <canvas id="projectStatusChart"></canvas>
    </div>
  </div>

  <!-- Right Side: Quick Action Shortcuts & Info Card -->
  <div class="bg-white rounded-2xl p-6 shadow-card border border-slate-200 flex flex-col justify-between space-y-6">
    <div>
      <h2 class="text-base font-bold text-slate-900 mb-1">Akses Cepat Admin</h2>
      <p class="text-xs text-slate-700 font-medium mb-4">Shortcut fitur utama management panel</p>
      
      <div class="grid grid-cols-2 gap-3">
        <a href="{{ route('admin.projects.index') }}" class="p-3 rounded-xl bg-slate-50 hover:bg-emerald-50 hover:text-[#14433B] border border-slate-200 transition-all text-left flex flex-col items-start group">
          <i class="fa-solid fa-diagram-project text-lg text-[#14433B] mb-2 group-hover:scale-110 transition-transform"></i>
          <span class="text-xs font-bold text-slate-900">Kelola Proyek</span>
          <span class="text-[10px] text-slate-600 font-medium mt-0.5">{{ $totalProjects ?? 0 }} total proyek</span>
        </a>

        <a href="{{ route('admin.invoices.index') }}" class="p-3 rounded-xl bg-slate-50 hover:bg-blue-50 hover:text-blue-700 border border-slate-200 transition-all text-left flex flex-col items-start group">
          <i class="fa-solid fa-file-invoice-dollar text-lg text-blue-700 mb-2 group-hover:scale-110 transition-transform"></i>
          <span class="text-xs font-bold text-slate-900">Kelola Invoice</span>
          <span class="text-[10px] text-slate-600 font-medium mt-0.5">{{ $totalInvoiceCount ?? 0 }} invoice</span>
        </a>

        <a href="{{ route('admin.services.index') }}" class="p-3 rounded-xl bg-slate-50 hover:bg-purple-50 hover:text-purple-700 border border-slate-200 transition-all text-left flex flex-col items-start group">
          <i class="fa-solid fa-cubes text-lg text-purple-700 mb-2 group-hover:scale-110 transition-transform"></i>
          <span class="text-xs font-bold text-slate-900">Layanan</span>
          <span class="text-[10px] text-slate-600 font-medium mt-0.5">{{ $totalServices ?? 0 }} aktif</span>
        </a>

        <a href="{{ route('admin.clients.index') }}" class="p-3 rounded-xl bg-slate-50 hover:bg-amber-50 hover:text-amber-800 border border-slate-200 transition-all text-left flex flex-col items-start group">
          <i class="fa-solid fa-users text-lg text-amber-800 mb-2 group-hover:scale-110 transition-transform"></i>
          <span class="text-xs font-bold text-slate-900">Klien</span>
          <span class="text-[10px] text-slate-600 font-medium mt-0.5">{{ $totalClients ?? 0 }} mitra</span>
        </a>
      </div>
    </div>

    <!-- System Info Box -->
    <div class="p-4 rounded-xl bg-slate-50 border border-slate-200">
      <div class="flex items-center justify-between mb-2">
        <span class="text-xs font-bold text-slate-800">Ringkasan Konten</span>
        <span class="text-[10px] font-semibold text-slate-600">Live Website</span>
      </div>
      <div class="flex items-center justify-between text-xs text-slate-700 font-medium py-1 border-b border-slate-200">
        <span>Artikel Blog Published</span>
        <span class="font-bold text-slate-900">{{ $totalBlogs ?? 0 }}</span>
      </div>
      <div class="flex items-center justify-between text-xs text-slate-700 font-medium py-1">
        <span>Portfolio Karya</span>
        <span class="font-bold text-slate-900">{{ $totalPortfolios ?? 0 }}</span>
      </div>
    </div>

  </div>

</div>

<!-- Recent Transactions Table Section -->
<div class="bg-white rounded-2xl p-6 shadow-card border border-slate-200">
  <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
    <div>
      <h2 class="text-base font-bold text-slate-900">Transaksi Terbaru</h2>
      <p class="text-xs text-slate-700 font-medium">Mutasi kas & transaksi manual perusahaan</p>
    </div>
    <a href="{{ route('admin.finance.transactions.index') }}" class="text-xs font-bold text-[#14433B] hover:text-[#0E5D55] flex items-center space-x-1">
      <span>Lihat Semua Transaksi</span>
      <i class="fa-solid fa-chevron-right text-[10px]"></i>
    </a>
  </div>

  <div class="overflow-x-auto">
    <table class="w-full text-left border-collapse">
      <thead>
        <tr class="border-b border-slate-200 bg-slate-100/70 text-[11px] uppercase tracking-wider text-slate-700 font-bold">
          <th class="py-3 px-4 rounded-l-xl">Tanggal</th>
          <th class="py-3 px-4">Tipe</th>
          <th class="py-3 px-4">Keterangan</th>
          <th class="py-3 px-4">Admin Handler</th>
          <th class="py-3 px-4 text-right rounded-r-xl">Jumlah (Nominal)</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-slate-100 text-xs">
        @forelse($recentTransactions as $tx)
          <tr class="hover:bg-slate-50 transition-colors">
            <td class="py-3 px-4 text-slate-700 font-semibold whitespace-nowrap">
              {{ $tx->created_at ? $tx->created_at->format('d M Y, H:i') : '-' }}
            </td>
            <td class="py-3 px-4 whitespace-nowrap">
              @if($tx->type === 'credit')
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-900 border border-emerald-300">
                  <i class="fa-solid fa-arrow-down mr-1 text-[9px]"></i> Credit (Masuk)
                </span>
              @else
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-rose-100 text-rose-900 border border-rose-300">
                  <i class="fa-solid fa-arrow-up mr-1 text-[9px]"></i> Debit (Keluar)
                </span>
              @endif
            </td>
            <td class="py-3 px-4 text-slate-900 font-medium">
              {{ $tx->description ?? 'Tanpa Keterangan' }}
            </td>
            <td class="py-3 px-4 text-slate-700 font-medium">
              {{ $tx->admin->name ?? 'System' }}
            </td>
            <td class="py-3 px-4 text-right font-black whitespace-nowrap {{ $tx->type === 'credit' ? 'text-emerald-700' : 'text-rose-700' }}">
              {{ $tx->type === 'credit' ? '+' : '-' }} Rp {{ number_format($tx->amount, 0, ',', '.') }}
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="5" class="py-8 text-center text-slate-500 font-medium">
              <div class="flex flex-col items-center justify-center space-y-2">
                <i class="fa-solid fa-receipt text-3xl text-slate-400"></i>
                <span class="text-xs">Belum ada riwayat transaksi recorded.</span>
              </div>
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>

@endsection

@push('scripts')
<script>
  document.addEventListener('DOMContentLoaded', () => {
    const ctx = document.getElementById('projectStatusChart');
    if (ctx) {
      new Chart(ctx, {
        type: 'doughnut',
        data: {
          labels: ['Ongoing', 'Selesai', 'Pending'],
          datasets: [{
            data: [
              {{ $ongoingProjects ?? 0 }},
              {{ $completedProjects ?? 0 }},
              {{ $pendingProjects ?? 0 }}
            ],
            backgroundColor: ['#14433B', '#21C9A4', '#FBBF24'],
            borderWidth: 0,
            hoverOffset: 6
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: {
            legend: { display: false }
          },
          cutout: '75%'
        }
      });
    }
  });
</script>
@endpush