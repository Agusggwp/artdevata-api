@extends('layouts.admin')

@section('title', 'Daftar Invoices')

@section('content')

<!-- Header Bar -->
<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
  <div>
    <h1 class="text-xl font-bold text-slate-900">Daftar Invoice & Penagihan</h1>
    <p class="text-xs text-slate-700 font-medium">Kelola tagihan pembayaran proyek dan status invoice ARTDEVATA</p>
  </div>
  
  <a href="{{ route('admin.invoices.create') }}" class="px-4 py-2.5 rounded-xl bg-[#14433B] hover:bg-[#0B443C] text-white text-xs font-semibold shadow-md shadow-[#14433B]/20 transition-all duration-200 flex items-center space-x-2 w-fit">
    <i class="fa-solid fa-plus text-xs"></i>
    <span>Buat Invoice Baru</span>
  </a>
</div>

<!-- Table Card -->
<div class="bg-white rounded-2xl shadow-card border border-slate-200 overflow-hidden">
  
  <div class="overflow-x-auto">
    <table class="w-full text-left border-collapse">
      <thead>
        <tr class="border-b border-slate-200 bg-slate-100/70 text-[11px] uppercase tracking-wider text-slate-700 font-bold">
          <th class="py-3.5 px-4 sm:px-6">No. Invoice</th>
          <th class="py-3.5 px-4 sm:px-6">Klien</th>
          <th class="py-3.5 px-4 sm:px-6">Tanggal Invoice</th>
          <th class="py-3.5 px-4 sm:px-6">Total Tagihan</th>
          <th class="py-3.5 px-4 sm:px-6">Status Payout</th>
          <th class="py-3.5 px-4 sm:px-6 text-right">Aksi</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-slate-100 text-xs">
        @forelse($invoices as $invoice)
          <tr class="hover:bg-slate-50 transition-colors">
            
            <!-- Invoice Number -->
            <td class="py-4 px-4 sm:px-6 whitespace-nowrap">
              <div class="flex items-center space-x-3">
                <div class="w-9 h-9 rounded-xl bg-blue-100 text-blue-800 flex items-center justify-center font-bold text-xs shrink-0">
                  <i class="fa-solid fa-file-invoice"></i>
                </div>
                <div>
                  <a href="{{ route('admin.invoices.show', $invoice) }}" class="font-bold text-slate-900 hover:text-[#14433B] text-xs">
                    {{ $invoice->invoice_number }}
                  </a>
                  <span class="text-[10px] text-slate-600 font-semibold block">Jatuh Tempo: {{ $invoice->due_date ? $invoice->due_date->format('d M Y') : '-' }}</span>
                </div>
              </div>
            </td>

            <!-- Client -->
            <td class="py-4 px-4 sm:px-6 text-slate-900 font-bold whitespace-nowrap">
              {{ $invoice->client_name }}
            </td>

            <!-- Invoice Date -->
            <td class="py-4 px-4 sm:px-6 text-slate-700 font-medium whitespace-nowrap">
              {{ $invoice->invoice_date ? $invoice->invoice_date->format('d M Y') : '-' }}
            </td>

            <!-- Total Amount -->
            <td class="py-4 px-4 sm:px-6 font-extrabold text-slate-900 whitespace-nowrap">
              Rp {{ number_format($invoice->total, 0, ',', '.') }}
            </td>

            <!-- Status with Instant Select -->
            <td class="py-4 px-4 sm:px-6 whitespace-nowrap">
              <div class="flex items-center space-x-2">
                <span class="status-badge" data-id="{{ $invoice->id }}">
                  {!! $invoice->status_badge !!}
                </span>
                <select class="status-select bg-slate-50 border border-slate-300 text-slate-800 font-medium text-[11px] rounded-lg px-2 py-1 focus:outline-hidden focus:border-[#14433B]" data-id="{{ $invoice->id }}">
                  @foreach(['draft' => 'Draft', 'sent' => 'Sent', 'paid' => 'Paid', 'overdue' => 'Overdue'] as $key => $label)
                    <option value="{{ $key }}" {{ $invoice->status === $key ? 'selected' : '' }}>{{ $label }}</option>
                  @endforeach
                </select>
              </div>
            </td>

            <!-- Actions -->
            <td class="py-4 px-4 sm:px-6 text-right whitespace-nowrap">
              <div class="flex items-center justify-end space-x-2">
                <a href="{{ route('admin.invoices.show', $invoice) }}" class="p-2 rounded-lg text-slate-700 hover:text-[#14433B] hover:bg-slate-100 transition-colors" title="Lihat Invoice">
                  <i class="fa-solid fa-eye"></i>
                </a>
                <button type="button" onclick="confirmDelete('{{ route('admin.invoices.destroy', $invoice) }}', 'Apakah Anda yakin ingin menghapus invoice {{ $invoice->invoice_number }}?')" class="p-2 rounded-lg text-slate-600 hover:text-rose-600 hover:bg-rose-50 transition-colors" title="Hapus Invoice">
                  <i class="fa-solid fa-trash-can"></i>
                </button>
              </div>
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
                  <p class="text-sm font-semibold text-slate-800">Belum Ada Invoice</p>
                  <p class="text-xs text-slate-600 mt-0.5">Buat invoice tagihan pertama untuk pembayaran klien.</p>
                </div>
                <a href="{{ route('admin.invoices.create') }}" class="px-4 py-2 rounded-xl bg-[#14433B] text-white text-xs font-semibold shadow-sm hover:bg-[#0B443C] transition-colors">
                  + Buat Invoice Baru
                </a>
              </div>
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  @if($invoices->hasPages())
    <div class="px-6 py-4 border-t border-slate-100">
      {{ $invoices->links() }}
    </div>
  @endif

</div>

@endsection

@push('scripts')
<script>
  const statusUrlTemplate = "{{ url('admin/invoices') }}/:id/status";
  const csrfToken = "{{ csrf_token() }}";

  document.querySelectorAll('.status-select').forEach(select => {
    select.addEventListener('change', async function () {
      const id = this.dataset.id;
      const status = this.value;
      const url = statusUrlTemplate.replace(':id', id);

      try {
        const res = await fetch(url, {
          method: 'PATCH',
          headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': csrfToken
          },
          body: JSON.stringify({ status })
        });

        const data = await res.json();
        if (res.ok && data.status === 'ok') {
          const badgeEl = document.querySelector('.status-badge[data-id="' + id + '"]');
          if (badgeEl) badgeEl.innerHTML = data.badge;
        } else {
          alert(data.message || 'Gagal memperbarui status');
        }
      } catch (err) {
        console.error(err);
        alert('Terjadi kesalahan koneksi');
      }
    });
  });
</script>
@endpush