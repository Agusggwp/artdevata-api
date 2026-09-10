@extends('layouts.admin')

@section('title', 'Buat Invoice Baru')

@section('content')

<div class="max-w-4xl mx-auto">
  
  <div class="mb-6 flex items-center justify-between">
    <div>
      <h1 class="text-xl font-bold text-slate-900">Buat Invoice Baru</h1>
      <p class="text-xs text-slate-500">Keluarkan tagihan resmi pembayaran proyek kepada klien</p>
    </div>
    <a href="{{ route('admin.invoices.index') }}" class="px-3.5 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold transition-colors flex items-center space-x-1.5">
      <i class="fa-solid fa-arrow-left text-xs"></i>
      <span>Kembali</span>
    </a>
  </div>

  <div class="bg-white rounded-2xl p-6 sm:p-8 shadow-card border border-slate-100">
    <form method="POST" action="{{ route('admin.invoices.store') }}" class="space-y-6">
      @csrf

      <!-- Client Info -->
      <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
        <div>
          <label for="client_name" class="block text-xs font-semibold text-slate-700 mb-1.5">Nama Klien / Perusahaan <span class="text-rose-500">*</span></label>
          <input 
            id="client_name"
            type="text" 
            name="client_name" 
            value="{{ old('client_name') }}" 
            placeholder="Contoh: PT Medika Utama Indonesia"
            class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 placeholder-slate-400 focus:bg-white focus:border-[#21C9A4] focus:ring-2 focus:ring-[#21C9A4]/20 outline-hidden transition-all duration-200"
            required
          >
        </div>

        <div>
          <label for="client_email" class="block text-xs font-semibold text-slate-700 mb-1.5">Email Klien</label>
          <input 
            id="client_email"
            type="email" 
            name="client_email" 
            value="{{ old('client_email') }}" 
            placeholder="billing@medikautama.com"
            class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 placeholder-slate-400 focus:bg-white focus:border-[#21C9A4] focus:ring-2 focus:ring-[#21C9A4]/20 outline-hidden transition-all duration-200"
          >
        </div>
      </div>

      <div>
        <label for="client_address" class="block text-xs font-semibold text-slate-700 mb-1.5">Alamat Lengkap Klien</label>
        <textarea 
          id="client_address"
          name="client_address" 
          rows="2" 
          placeholder="Alamat penagihan surat/faktur..."
          class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 placeholder-slate-400 focus:bg-white focus:border-[#21C9A4] focus:ring-2 focus:ring-[#21C9A4]/20 outline-hidden transition-all duration-200"
        >{{ old('client_address') }}</textarea>
      </div>

      <!-- Dates -->
      <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
        <div>
          <label for="invoice_date" class="block text-xs font-semibold text-slate-700 mb-1.5">Tanggal Diterbitkan <span class="text-rose-500">*</span></label>
          <input 
            id="invoice_date"
            type="date" 
            name="invoice_date" 
            value="{{ old('invoice_date', today()->format('Y-m-d')) }}" 
            class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:bg-white focus:border-[#21C9A4] focus:ring-2 focus:ring-[#21C9A4]/20 outline-hidden transition-all duration-200"
            required
          >
        </div>

        <div>
          <label for="due_date" class="block text-xs font-semibold text-slate-700 mb-1.5">Batas Jatuh Tempo <span class="text-rose-500">*</span></label>
          <input 
            id="due_date"
            type="date" 
            name="due_date" 
            value="{{ old('due_date', today()->addDays(14)->format('Y-m-d')) }}" 
            class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:bg-white focus:border-[#21C9A4] focus:ring-2 focus:ring-[#21C9A4]/20 outline-hidden transition-all duration-200"
            required
          >
        </div>
      </div>

      <!-- Items Section -->
      <div class="pt-4 border-t border-slate-100">
        <div class="flex items-center justify-between mb-3">
          <label class="block text-xs font-bold text-slate-800">Rincian Item Pekerjaan / Layanan</label>
          <button type="button" id="add-item" class="px-3 py-1.5 rounded-xl bg-emerald-50 hover:bg-emerald-100 text-[#14433B] text-xs font-semibold transition-colors flex items-center space-x-1.5">
            <i class="fa-solid fa-plus text-xs text-[#21C9A4]"></i>
            <span>Tambah Item</span>
          </button>
        </div>

        <div id="items-container" class="space-y-3">
          <div class="item-row grid grid-cols-12 gap-3 items-center bg-slate-50/70 p-3 rounded-xl border border-slate-100">
            <div class="col-span-12 sm:col-span-5">
              <input type="text" name="items[0][description]" required placeholder="Deskripsi item pekerjaan" class="w-full px-3 py-2 bg-white border border-slate-200 rounded-xl text-xs text-slate-800 focus:border-[#21C9A4] outline-hidden">
            </div>
            <div class="col-span-4 sm:col-span-2">
              <input type="number" name="items[0][quantity]" required min="1" value="1" placeholder="Qty" class="item-qty w-full px-3 py-2 bg-white border border-slate-200 rounded-xl text-xs text-slate-800 focus:border-[#21C9A4] outline-hidden">
            </div>
            <div class="col-span-6 sm:col-span-3">
              <input type="number" name="items[0][price]" required min="0" step="1000" placeholder="Harga (Rp)" class="item-price w-full px-3 py-2 bg-white border border-slate-200 rounded-xl text-xs text-slate-800 focus:border-[#21C9A4] outline-hidden">
            </div>
            <div class="col-span-2 sm:col-span-2 text-right font-bold text-xs text-[#14433B] subtotal">
              Rp 0
            </div>
          </div>
        </div>
      </div>

      <!-- Calculations Summary Card -->
      <div class="p-4 rounded-xl bg-slate-50 border border-slate-200/80 flex flex-col items-end space-y-1.5 text-xs">
        <div class="flex items-center space-x-8 text-slate-600">
          <span>Subtotal Pekerjaan:</span>
          <span id="subtotal-display" class="font-bold text-slate-900">Rp 0</span>
        </div>
        <div class="flex items-center space-x-8 text-slate-600">
          <span>Estimasi PPN 11%:</span>
          <span id="tax-display" class="font-bold text-slate-900">Rp 0</span>
        </div>
        <div class="flex items-center space-x-8 text-sm font-black text-[#14433B] pt-2 border-t border-slate-200 w-fit">
          <span>Total Tagihan:</span>
          <span id="total-display" class="text-base text-[#14433B]">Rp 0</span>
        </div>
      </div>

      <!-- Notes -->
      <div>
        <label for="notes" class="block text-xs font-semibold text-slate-700 mb-1.5">Catatan / Instruksi Pembayaran</label>
        <textarea 
          id="notes"
          name="notes" 
          rows="3" 
          placeholder="Nomor rekening bank penampung, atau instruksi pembayaran lainnya..."
          class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 placeholder-slate-400 focus:bg-white focus:border-[#21C9A4] focus:ring-2 focus:ring-[#21C9A4]/20 outline-hidden transition-all duration-200"
        >{{ old('notes') }}</textarea>
      </div>

      <!-- Submit Actions -->
      <div class="pt-6 border-t border-slate-100 flex items-center justify-end space-x-3">
        <a href="{{ route('admin.invoices.index') }}" class="px-5 py-2.5 rounded-xl border border-slate-200 text-slate-700 text-xs font-semibold hover:bg-slate-50 transition-colors">
          Batal
        </a>
        <button type="submit" class="px-6 py-2.5 rounded-xl bg-[#14433B] hover:bg-[#0B443C] text-white text-xs font-semibold shadow-md shadow-[#14433B]/20 transition-all flex items-center space-x-2">
          <i class="fa-solid fa-floppy-disk text-xs"></i>
          <span>Simpan & Terbitkan Invoice</span>
        </button>
      </div>

    </form>
  </div>

</div>

@endsection

@push('scripts')
<script>
  let itemIndex = 1;
  document.getElementById('add-item').addEventListener('click', () => {
    const container = document.getElementById('items-container');
    const row = document.createElement('div');
    row.className = 'item-row grid grid-cols-12 gap-3 items-center bg-slate-50/70 p-3 rounded-xl border border-slate-100';
    row.innerHTML = `
      <div class="col-span-12 sm:col-span-5">
        <input type="text" name="items[${itemIndex}][description]" required placeholder="Deskripsi item pekerjaan" class="w-full px-3 py-2 bg-white border border-slate-200 rounded-xl text-xs text-slate-800 focus:border-[#21C9A4] outline-hidden">
      </div>
      <div class="col-span-4 sm:col-span-2">
        <input type="number" name="items[${itemIndex}][quantity]" required min="1" value="1" placeholder="Qty" class="item-qty w-full px-3 py-2 bg-white border border-slate-200 rounded-xl text-xs text-slate-800 focus:border-[#21C9A4] outline-hidden">
      </div>
      <div class="col-span-5 sm:col-span-3">
        <input type="number" name="items[${itemIndex}][price]" required min="0" step="1000" placeholder="Harga (Rp)" class="item-price w-full px-3 py-2 bg-white border border-slate-200 rounded-xl text-xs text-slate-800 focus:border-[#21C9A4] outline-hidden">
      </div>
      <div class="col-span-3 sm:col-span-2 flex items-center justify-end space-x-2">
        <span class="subtotal font-bold text-xs text-[#14433B]">Rp 0</span>
        <button type="button" class="remove-item p-1.5 text-slate-400 hover:text-rose-600 rounded-lg"><i class="fa-solid fa-xmark"></i></button>
      </div>
    `;
    container.appendChild(row);
    itemIndex++;
    attachItemEvents();
  });

  function attachItemEvents() {
    document.querySelectorAll('.item-row').forEach(row => {
      const qty = row.querySelector('.item-qty');
      const price = row.querySelector('.item-price');
      const subtotalEl = row.querySelector('.subtotal');

      const calculate = () => {
        const subtotal = (parseFloat(qty.value) || 0) * (parseFloat(price.value) || 0);
        subtotalEl.textContent = 'Rp ' + Math.round(subtotal).toLocaleString('id-ID');
        calculateTotal();
      };

      if (qty && price) {
        qty.oninput = calculate;
        price.oninput = calculate;
      }

      row.querySelector('.remove-item')?.addEventListener('click', () => {
        row.remove();
        calculateTotal();
      });
    });
    calculateTotal();
  }

  function calculateTotal() {
    let subtotal = 0;
    document.querySelectorAll('.item-row').forEach(row => {
      const qty = parseFloat(row.querySelector('.item-qty')?.value || 0);
      const price = parseFloat(row.querySelector('.item-price')?.value || 0);
      subtotal += qty * price;
    });
    const tax = subtotal * 0.11;
    const total = subtotal + tax;

    document.getElementById('subtotal-display').textContent = 'Rp ' + Math.round(subtotal).toLocaleString('id-ID');
    document.getElementById('tax-display').textContent = 'Rp ' + Math.round(tax).toLocaleString('id-ID');
    document.getElementById('total-display').textContent = 'Rp ' + Math.round(total).toLocaleString('id-ID');
  }

  attachItemEvents();
</script>
@endpush