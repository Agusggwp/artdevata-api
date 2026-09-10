@extends('layouts.admin')

@section('title', 'Transaksi & Kas Perusahaan')

@section('content')

<!-- Header Bar -->
<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
  <div>
    <h1 class="text-xl font-bold text-slate-900">Riwayat Mutasi & Transaksi Kas</h1>
    <p class="text-xs text-slate-700 font-medium">Pencatatan kredit (pemasukan) dan debit (pengeluaran) kas ARTDEVATA</p>
  </div>
  
  <div class="flex items-center space-x-3">
    <div class="px-4 py-2.5 rounded-xl bg-emerald-100/80 border border-emerald-300 text-emerald-950 text-xs font-bold">
      <span>Saldo Sekarang: </span>
      <span class="font-black text-[#14433B]">Rp {{ number_format($companyBalance ?? 0, 0, ',', '.') }}</span>
    </div>

    <!-- Modal Trigger Button -->
    <button onclick="toggleTransactionModal()" class="px-4 py-2.5 rounded-xl bg-[#14433B] hover:bg-[#0B443C] text-white text-xs font-semibold shadow-md shadow-[#14433B]/20 transition-all duration-200 flex items-center space-x-2">
      <i class="fa-solid fa-plus text-xs"></i>
      <span>Catat Transaksi Kas</span>
    </button>
  </div>
</div>

<!-- Filter Bar Card -->
<div class="bg-white rounded-2xl p-4 shadow-card border border-slate-200 mb-6">
  <form method="GET" action="{{ route('admin.finance.transactions.index') }}" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-5 gap-3">
    
    <div>
      <select name="type" class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl text-xs text-slate-900 font-medium focus:bg-white focus:border-[#14433B] outline-hidden">
        <option value="">Semua Tipe Transaksi</option>
        <option value="credit" {{ $qType === 'credit' ? 'selected' : '' }}>Credit (Pemasukan)</option>
        <option value="debit" {{ $qType === 'debit' ? 'selected' : '' }}>Debit (Pengeluaran)</option>
      </select>
    </div>

    <div>
      <input type="date" name="from" value="{{ $qFrom ?? '' }}" class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl text-xs text-slate-900 font-medium focus:bg-white focus:border-[#14433B] outline-hidden">
    </div>

    <div>
      <input type="date" name="to" value="{{ $qTo ?? '' }}" class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl text-xs text-slate-900 font-medium focus:bg-white focus:border-[#14433B] outline-hidden">
    </div>

    <div>
      <input type="text" name="q" value="{{ $qSearch ?? '' }}" placeholder="Cari keterangan..." class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl text-xs text-slate-900 font-medium placeholder-slate-500 focus:bg-white focus:border-[#14433B] outline-hidden">
    </div>

    <div class="flex items-center space-x-2">
      <button type="submit" class="px-4 py-2 rounded-xl bg-[#14433B] hover:bg-[#0B443C] text-white text-xs font-semibold transition-colors flex-1">
        <i class="fa-solid fa-filter text-xs mr-1"></i> Filter
      </button>
      @if($qType || $qFrom || $qTo || $qSearch)
        <a href="{{ route('admin.finance.transactions.index') }}" class="p-2 rounded-xl border border-slate-300 text-slate-700 hover:text-slate-900 hover:bg-slate-100 transition-colors" title="Reset Filter">
          <i class="fa-solid fa-rotate-left"></i>
        </a>
      @endif
    </div>

  </form>
</div>

<!-- Transactions Table Card -->
<div class="bg-white rounded-2xl shadow-card border border-slate-200 overflow-hidden">
  
  <div class="overflow-x-auto">
    <table class="w-full text-left border-collapse text-xs">
      <thead>
        <tr class="border-b border-slate-200 bg-slate-100/70 text-[11px] uppercase tracking-wider text-slate-700 font-bold">
          <th class="py-3.5 px-4 sm:px-6">Waktu & Tanggal</th>
          <th class="py-3.5 px-4 sm:px-6">Oleh Admin</th>
          <th class="py-3.5 px-4 sm:px-6">Tipe Mutasi</th>
          <th class="py-3.5 px-4 sm:px-6">Keterangan</th>
          <th class="py-3.5 px-4 sm:px-6 text-right">Jumlah</th>
          <th class="py-3.5 px-4 sm:px-6 text-right">Saldo Setelah Transaksi</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-slate-100">
        @forelse($transactions as $t)
          <tr class="hover:bg-slate-50 transition-colors">
            
            <td class="py-4 px-4 sm:px-6 text-slate-700 font-semibold whitespace-nowrap">
              {{ $t->created_at ? $t->created_at->format('d M Y, H:i') : '-' }}
            </td>

            <td class="py-4 px-4 sm:px-6 text-slate-900 font-bold whitespace-nowrap">
              {{ $t->admin->name ?? 'System' }}
            </td>

            <td class="py-4 px-4 sm:px-6 whitespace-nowrap">
              @if($t->type === 'credit')
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-900 border border-emerald-300">
                  <i class="fa-solid fa-arrow-down mr-1 text-[9px]"></i> Credit (Masuk)
                </span>
              @else
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-rose-100 text-rose-900 border border-rose-300">
                  <i class="fa-solid fa-arrow-up mr-1 text-[9px]"></i> Debit (Keluar)
                </span>
              @endif
            </td>

            <td class="py-4 px-4 sm:px-6 text-slate-900 font-medium max-w-xs">
              {{ $t->description ?? 'Tanpa Keterangan' }}
            </td>

            <td class="py-4 px-4 sm:px-6 text-right font-black whitespace-nowrap {{ $t->type === 'credit' ? 'text-emerald-700' : 'text-rose-700' }}">
              {{ $t->type === 'credit' ? '+' : '-' }} Rp {{ number_format($t->amount, 0, ',', '.') }}
            </td>

            <td class="py-4 px-4 sm:px-6 text-right font-black text-slate-900 whitespace-nowrap">
              @php
                $bal = $balanceMap[$t->id] ?? ($t->balance_after ?? null);
              @endphp
              {{ $bal !== null ? 'Rp '.number_format($bal, 0, ',', '.') : '-' }}
            </td>

          </tr>
        @empty
          <tr>
            <td colspan="6" class="py-12 text-center text-slate-500">
              <div class="flex flex-col items-center justify-center space-y-3">
                <div class="w-16 h-16 rounded-full bg-slate-100 flex items-center justify-center text-slate-400 text-2xl">
                  <i class="fa-solid fa-receipt"></i>
                </div>
                <div>
                  <p class="text-sm font-semibold text-slate-800">Belum Ada Transaksi Kas</p>
                  <p class="text-xs text-slate-600 mt-0.5">Catat penyesuaian kas kredit/debit pertama Anda.</p>
                </div>
                <button onclick="toggleTransactionModal()" class="px-4 py-2 rounded-xl bg-[#14433B] text-white text-xs font-semibold shadow-sm hover:bg-[#0B443C] transition-colors">
                  + Catat Transaksi Kas
                </button>
              </div>
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  @if($transactions->hasPages())
    <div class="px-6 py-4 border-t border-slate-100">
      {{ $transactions->links() }}
    </div>
  @endif

</div>

<!-- Modal Manual Transaction Entry -->
<div id="transaction-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-900/50 backdrop-blur-xs p-4">
  <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl border border-slate-100 transform transition-all scale-95 duration-200">
    <div class="flex items-center justify-between mb-4 pb-3 border-b border-slate-100">
      <h3 class="text-base font-bold text-slate-900">Catat Transaksi Kas Manual</h3>
      <button onclick="toggleTransactionModal()" class="text-slate-400 hover:text-slate-600 p-1"><i class="fa-solid fa-xmark text-lg"></i></button>
    </div>

    <form method="POST" action="{{ route('admin.finance.transaction.store') }}" class="space-y-4">
      @csrf

      <div>
        <label for="type" class="block text-xs font-semibold text-slate-700 mb-1">Tipe Mutasi <span class="text-rose-500">*</span></label>
        <select id="type" name="type" required class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:bg-white focus:border-[#21C9A4] outline-hidden">
          <option value="credit">Credit (Tambah Saldo / Pemasukan)</option>
          <option value="debit">Debit (Kurangi Saldo / Pengeluaran)</option>
        </select>
      </div>

      <div>
        <label for="amount" class="block text-xs font-semibold text-slate-700 mb-1">Jumlah Nominal (Rp) <span class="text-rose-500">*</span></label>
        <input id="amount" type="number" step="0.01" min="0.01" name="amount" required placeholder="Contoh: 5000000" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:bg-white focus:border-[#21C9A4] outline-hidden">
      </div>

      <div>
        <label for="description" class="block text-xs font-semibold text-slate-700 mb-1">Keterangan Transaksi</label>
        <textarea id="description" name="description" rows="3" placeholder="Contoh: Pembayaran Server Hostinger & Domain" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:bg-white focus:border-[#21C9A4] outline-hidden"></textarea>
      </div>

      <div class="pt-3 border-t border-slate-100 flex items-center justify-end space-x-3">
        <button type="button" onclick="toggleTransactionModal()" class="px-4 py-2.5 rounded-xl border border-slate-200 text-slate-700 text-xs font-semibold hover:bg-slate-50 transition-colors">Batal</button>
        <button type="submit" class="px-5 py-2.5 rounded-xl bg-[#14433B] hover:bg-[#0B443C] text-white text-xs font-semibold shadow-md shadow-[#14433B]/20 transition-all">Simpan Transaksi</button>
      </div>

    </form>
  </div>
</div>

@endsection

@push('scripts')
<script>
  function toggleTransactionModal() {
    const modal = document.getElementById('transaction-modal');
    if (modal.classList.contains('hidden')) {
      modal.classList.remove('hidden');
      modal.classList.add('flex');
    } else {
      modal.classList.add('hidden');
      modal.classList.remove('flex');
    }
  }
</script>
@endpush