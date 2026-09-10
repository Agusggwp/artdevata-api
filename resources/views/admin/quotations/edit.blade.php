@extends('layouts.admin')

@section('title', 'Edit Quotation ' . $quotation->quotation_number)

@section('content')
<div class="max-w-5xl mx-auto space-y-6">
  
  <div class="flex items-center justify-between">
    <div>
      <h1 class="text-xl font-bold text-slate-800">Edit Quotation {{ $quotation->quotation_number }}</h1>
      <p class="text-xs text-slate-500">Perbarui rincian item, harga, dan status penawaran.</p>
    </div>
    <a href="{{ route('admin.quotations.index') }}" class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold transition-colors">
      <i class="fa-solid fa-arrow-left mr-1"></i> Kembali
    </a>
  </div>

  <form method="POST" action="{{ route('admin.quotations.update', $quotation->id) }}" id="quotation-form" class="space-y-6">
    @csrf
    @method('PUT')

    <div class="bg-white rounded-2xl p-6 shadow-card border border-slate-100 space-y-4">
      <h3 class="text-sm font-bold text-slate-800 border-b border-slate-100 pb-3 flex items-center space-x-2">
        <i class="fa-solid fa-file-invoice text-[#14433B]"></i>
        <span>Informasi Penawaran & Klien</span>
      </h3>

      <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div>
          <label class="block text-xs font-bold text-slate-700 mb-1">Nomor Quotation *</label>
          <input type="text" name="quotation_number" value="{{ old('quotation_number', $quotation->quotation_number) }}" required class="w-full text-xs font-bold text-[#14433B] rounded-xl border-slate-200 focus:border-[#14433B] focus:ring-[#14433B]">
        </div>

        <div>
          <label class="block text-xs font-bold text-slate-700 mb-1">Pilih Klien *</label>
          <select name="client_id" required class="w-full text-xs rounded-xl border-slate-200 focus:border-[#14433B] focus:ring-[#14433B]">
            <option value="">-- Pilih Klien --</option>
            @foreach($clients as $c)
              <option value="{{ $c->id }}" {{ (old('client_id', $quotation->client_id) == $c->id) ? 'selected' : '' }}>
                {{ $c->name }} ({{ $c->company_name ?? $c->company ?? 'Perorangan' }})
              </option>
            @endforeach
          </select>
        </div>

        <div>
          <label class="block text-xs font-bold text-slate-700 mb-1">Status Penawaran *</label>
          <select name="status" required class="w-full text-xs rounded-xl border-slate-200 focus:border-[#14433B] focus:ring-[#14433B]">
            <option value="draft" {{ old('status', $quotation->status) == 'draft' ? 'selected' : '' }}>Draft</option>
            <option value="sent" {{ old('status', $quotation->status) == 'sent' ? 'selected' : '' }}>Sent</option>
            <option value="viewed" {{ old('status', $quotation->status) == 'viewed' ? 'selected' : '' }}>Viewed</option>
            <option value="accepted" {{ old('status', $quotation->status) == 'accepted' ? 'selected' : '' }}>Accepted</option>
            <option value="rejected" {{ old('status', $quotation->status) == 'rejected' ? 'selected' : '' }}>Rejected</option>
            <option value="expired" {{ old('status', $quotation->status) == 'expired' ? 'selected' : '' }}>Expired</option>
            <option value="cancelled" {{ old('status', $quotation->status) == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
          </select>
        </div>

        <div>
          <label class="block text-xs font-bold text-slate-700 mb-1">Tanggal Terbit *</label>
          <input type="date" name="issue_date" value="{{ old('issue_date', $quotation->issue_date->format('Y-m-d')) }}" required class="w-full text-xs rounded-xl border-slate-200 focus:border-[#14433B] focus:ring-[#14433B]">
        </div>

        <div>
          <label class="block text-xs font-bold text-slate-700 mb-1">Berlaku Sampai *</label>
          <input type="date" name="valid_until" value="{{ old('valid_until', $quotation->valid_until->format('Y-m-d')) }}" required class="w-full text-xs rounded-xl border-slate-200 focus:border-[#14433B] focus:ring-[#14433B]">
        </div>

        <div>
          <label class="block text-xs font-bold text-slate-700 mb-1">Hubungkan Proyek (Opsional)</label>
          <select name="project_id" class="w-full text-xs rounded-xl border-slate-200 focus:border-[#14433B] focus:ring-[#14433B]">
            <option value="">-- Tanpa Proyek --</option>
            @foreach($projects as $p)
              <option value="{{ $p->id }}" {{ old('project_id', $quotation->project_id) == $p->id ? 'selected' : '' }}>{{ $p->name }}</option>
            @endforeach
          </select>
        </div>
      </div>
    </div>

    <!-- Items Table Section -->
    <div class="bg-white rounded-2xl p-6 shadow-card border border-slate-100 space-y-4">
      <div class="flex items-center justify-between border-b border-slate-100 pb-3">
        <h3 class="text-sm font-bold text-slate-800 flex items-center space-x-2">
          <i class="fa-solid fa-list-check text-[#14433B]"></i>
          <span>Rincian Item Pekerjaan & Layanan</span>
        </h3>
        <button type="button" onclick="addItemRow()" class="px-3 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold transition-colors">
          <i class="fa-solid fa-plus mr-1"></i> Tambah Item
        </button>
      </div>

      <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse" id="items-table">
          <thead>
            <tr class="bg-slate-50 border-b border-slate-100 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
              <th class="py-2.5 px-3">Layanan</th>
              <th class="py-2.5 px-3">Deskripsi Pekerjaan *</th>
              <th class="py-2.5 px-3 w-20">Qty</th>
              <th class="py-2.5 px-3 w-24">Satuan</th>
              <th class="py-2.5 px-3 w-36">Harga Satuan (Rp)</th>
              <th class="py-2.5 px-3 w-28">Diskon (Rp)</th>
              <th class="py-2.5 px-3 w-36 text-right">Subtotal</th>
              <th class="py-2.5 px-3 w-12 text-center">Hapus</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100 text-xs" id="items-body">
          </tbody>
        </table>
      </div>

      <!-- Financial Totals Calculation -->
      <div class="flex flex-col md:flex-row md:items-start justify-between gap-6 pt-4 border-t border-slate-100">
        <div class="space-y-4 flex-1">
          <div>
            <label class="block text-xs font-bold text-slate-700 mb-1">Syarat & Ketentuan Pembayaran</label>
            <textarea name="terms" rows="3" class="w-full text-xs rounded-xl border-slate-200 focus:border-[#14433B] focus:ring-[#14433B]">{{ old('terms', $quotation->terms) }}</textarea>
          </div>
          <div>
            <label class="block text-xs font-bold text-slate-700 mb-1">Catatan Tambahan untuk Klien</label>
            <textarea name="notes" rows="2" class="w-full text-xs rounded-xl border-slate-200 focus:border-[#14433B] focus:ring-[#14433B]">{{ old('notes', $quotation->notes) }}</textarea>
          </div>
        </div>

        <div class="w-full md:w-80 bg-slate-50 p-4 rounded-xl border border-slate-200 space-y-3 text-xs">
          <div class="flex justify-between items-center text-slate-600">
            <span>Subtotal Item:</span>
            <span class="font-bold text-slate-800" id="calc-subtotal">Rp 0</span>
          </div>
          <div class="flex justify-between items-center text-slate-600">
            <span>Diskon Tambahan (Rp):</span>
            <input type="number" step="0.01" name="discount" id="global-discount" value="{{ old('discount', $quotation->discount) }}" oninput="calculateTotals()" class="w-32 text-xs text-right rounded-lg border-slate-200 py-1">
          </div>
          <div class="flex justify-between items-center text-slate-600">
            <span>Pajak PPN (Rp):</span>
            <input type="number" step="0.01" name="tax" id="global-tax" value="{{ old('tax', $quotation->tax) }}" oninput="calculateTotals()" class="w-32 text-xs text-right rounded-lg border-slate-200 py-1">
          </div>
          <div class="flex justify-between items-center text-sm font-extrabold text-[#14433B] pt-2 border-t border-slate-200">
            <span>Grand Total:</span>
            <span id="calc-grandtotal">Rp 0</span>
          </div>
        </div>
      </div>

    </div>

    <!-- Actions -->
    <div class="flex items-center justify-end space-x-3">
      <a href="{{ route('admin.quotations.index') }}" class="px-5 py-2.5 rounded-xl border border-slate-200 text-slate-700 text-xs font-semibold hover:bg-slate-50 transition-colors">Batal</a>
      <button type="submit" class="px-5 py-2.5 rounded-xl bg-[#14433B] hover:bg-[#0B443C] text-white text-xs font-semibold shadow-sm transition-all">Perbarui Quotation</button>
    </div>

  </form>

</div>

<script>
  let itemIndex = 0;
  const servicesList = @json($services);
  const existingItems = @json($quotation->items);

  function addItemRow(serviceId = '', description = '', qty = 1, unit = 'unit', price = 0, discount = 0) {
    const tbody = document.getElementById('items-body');
    const tr = document.createElement('tr');
    tr.id = `item-row-${itemIndex}`;
    tr.className = 'hover:bg-slate-50';

    let serviceOptions = `<option value="">-- Manual / Custom --</option>`;
    servicesList.forEach(s => {
      serviceOptions += `<option value="${s.id}" ${s.id == serviceId ? 'selected' : ''}>${s.title}</option>`;
    });

    tr.innerHTML = `
      <td class="py-2 px-2">
        <select name="items[${itemIndex}][service_id]" onchange="onServiceChange(this, ${itemIndex})" class="w-full text-xs rounded-lg border-slate-200 py-1">
          ${serviceOptions}
        </select>
      </td>
      <td class="py-2 px-2">
        <input type="text" name="items[${itemIndex}][description]" value="${description}" required class="w-full text-xs rounded-lg border-slate-200 py-1">
      </td>
      <td class="py-2 px-2">
        <input type="number" name="items[${itemIndex}][quantity]" value="${qty}" min="1" required oninput="calculateTotals()" class="w-full text-xs rounded-lg border-slate-200 py-1 item-qty">
      </td>
      <td class="py-2 px-2">
        <input type="text" name="items[${itemIndex}][unit]" value="${unit}" required class="w-full text-xs rounded-lg border-slate-200 py-1">
      </td>
      <td class="py-2 px-2">
        <input type="number" step="0.01" name="items[${itemIndex}][unit_price]" value="${price}" required oninput="calculateTotals()" class="w-full text-xs rounded-lg border-slate-200 py-1 item-price">
      </td>
      <td class="py-2 px-2">
        <input type="number" step="0.01" name="items[${itemIndex}][discount]" value="${discount}" oninput="calculateTotals()" class="w-full text-xs rounded-lg border-slate-200 py-1 item-discount">
      </td>
      <td class="py-2 px-2 text-right font-bold text-slate-800 item-subtotal">
        Rp 0
      </td>
      <td class="py-2 px-2 text-center">
        <button type="button" onclick="removeItemRow(${itemIndex})" class="text-rose-500 hover:text-rose-700 p-1"><i class="fa-solid fa-trash-can"></i></button>
      </td>
    `;

    tbody.appendChild(tr);
    itemIndex++;
    calculateTotals();
  }

  function onServiceChange(selectEl, idx) {
    const serviceId = selectEl.value;
    const descInput = selectEl.closest('tr').querySelector(`input[name="items[${idx}][description]"]`);
    if (serviceId) {
      const s = servicesList.find(item => item.id == serviceId);
      if (s && !descInput.value) {
        descInput.value = s.title;
      }
    }
  }

  function removeItemRow(idx) {
    const row = document.getElementById(`item-row-${idx}`);
    if (row) {
      row.remove();
      calculateTotals();
    }
  }

  function calculateTotals() {
    const tbody = document.getElementById('items-body');
    const rows = tbody.querySelectorAll('tr');
    let totalSubtotal = 0;

    rows.forEach(tr => {
      const qty = parseFloat(tr.querySelector('.item-qty')?.value || 0);
      const price = parseFloat(tr.querySelector('.item-price')?.value || 0);
      const discount = parseFloat(tr.querySelector('.item-discount')?.value || 0);
      let subtotal = (qty * price) - discount;
      if (subtotal < 0) subtotal = 0;

      totalSubtotal += subtotal;

      const subtotalEl = tr.querySelector('.item-subtotal');
      if (subtotalEl) {
        subtotalEl.textContent = 'Rp ' + Math.round(subtotal).toLocaleString('id-ID');
      }
    });

    const globalDiscount = parseFloat(document.getElementById('global-discount')?.value || 0);
    const globalTax = parseFloat(document.getElementById('global-tax')?.value || 0);

    let grandTotal = (totalSubtotal - globalDiscount) + globalTax;
    if (grandTotal < 0) grandTotal = 0;

    document.getElementById('calc-subtotal').textContent = 'Rp ' + Math.round(totalSubtotal).toLocaleString('id-ID');
    document.getElementById('calc-grandtotal').textContent = 'Rp ' + Math.round(grandTotal).toLocaleString('id-ID');
  }

  document.addEventListener('DOMContentLoaded', () => {
    if (existingItems && existingItems.length > 0) {
      existingItems.forEach(it => {
        addItemRow(it.service_id || '', it.description, it.quantity, it.unit, it.unit_price, it.discount);
      });
    } else {
      addItemRow('', '', 1, 'unit', 0, 0);
    }
  });
</script>
@endsection
