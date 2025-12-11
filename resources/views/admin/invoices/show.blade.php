<!DOCTYPE html>
<html lang="id" class="h-full scroll-smooth">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Detail Invoice {{ $invoice->invoice_number }} - ARTDEVATA Admin</title>

  <!-- Tailwind CSS CDN -->
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      darkMode: 'class',
      theme: {
        extend: {
          colors: {
            primary: '#4361ee',
            secondary: '#3f37c9',
            accent: '#4895ef',
            danger: '#f72585',
            success: '#4cc9f0',
          },
          fontFamily: {
            sans: ['Inter', 'sans-serif'],
          }
        }
      }
    }
  </script>

  <!-- Google Fonts: Inter -->
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  
  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
  
  <style>
    .sidebar-hover:hover { @apply bg-gray-100 dark:bg-gray-700; }
  </style>
</head>
<body class="h-full bg-gray-50 dark:bg-gray-900 text-gray-800 dark:text-gray-100 font-sans">

  <div class="flex h-screen overflow-hidden">

    <!-- Sidebar -->
    <div id="sidebar" class="fixed inset-y-0 left-0 z-50 w-64 bg-white dark:bg-gray-800 shadow-2xl transform -translate-x-full lg:translate-x-0 lg:static lg:inset-0 transition-transform duration-300 ease-in-out">
      <div class="flex items-center justify-between p-6 border-b dark:border-gray-700">
        <h1 class="text-2xl font-bold bg-gradient-to-r from-primary to-secondary bg-clip-text text-transparent">ARTDEVATA</h1>
        <button id="close-sidebar" class="lg:hidden text-gray-500 hover:text-primary">
          <i class="fas fa-times text-xl"></i>
        </button>
      </div>
      <nav class="mt-6">
        <a href="{{ route('admin.panel') }}" class="flex items-center px-6 py-3 text-gray-700 dark:text-gray-300 sidebar-hover rounded-l-full mr-3">
          <i class="fas fa-tachometer-alt mr-3"></i> Dashboard
        </a>
        <a href="{{ route('admin.services.index') }}" class="flex items-center px-6 py-3 text-gray-700 dark:text-gray-300 sidebar-hover rounded-l-full mr-3">
          <i class="fas fa-box mr-3"></i> Layanan
        </a>
        <a href="{{ route('admin.portfolios.index') }}" class="flex items-center px-6 py-3 text-gray-700 dark:text-gray-300 sidebar-hover rounded-l-full mr-3">
          <i class="fas fa-chart-line mr-3"></i> Portfolio
        </a>
        <a href="{{ route('admin.blogs.index') }}" class="flex items-center px-6 py-3 text-gray-700 dark:text-gray-300 sidebar-hover rounded-l-full mr-3">
          <i class="fas fa-blog mr-3"></i> Blog
        </a>
        <a href="{{ route('admin.invoices.index') }}" class="flex items-center px-6 py-3 bg-gradient-to-r from-primary to-secondary text-white rounded-l-full mr-3">
          <i class="fas fa-file-invoice-dollar mr-3"></i> Invoice
        </a>
      </nav>
    </div>

    <!-- Overlay (mobile) -->
    <div id="overlay" class="fixed inset-0 bg-black bg-opacity-50 z-40 hidden lg:hidden"></div>

    <!-- Main Content -->
    <div class="flex-1 flex flex-col overflow-hidden">

      <!-- Header -->
      <header class="bg-white dark:bg-gray-800 shadow-md">
        <div class="flex items-center justify-between px-4 py-3 lg:px-8">
          <div class="flex items-center space-x-4">
            <button id="open-sidebar" class="lg:hidden text-primary">
              <i class="fas fa-bars text-xl"></i>
            </button>
            <h2 class="text-xl font-semibold">Detail Invoice {{ $invoice->invoice_number }}</h2>
          </div>
          <div class="flex items-center space-x-4">
            <button id="dark-toggle" class="p-2 rounded-full hover:bg-gray-100 dark:hover:bg-gray-700">
              <i class="fas fa-moon text-lg"></i>
            </button>
            <div class="flex items-center space-x-3">
              <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::guard('admin')->user()->name) }}&background=4361ee&color=fff&bold=true" alt="Admin" class="w-10 h-10 rounded-full ring-2 ring-primary"/>
              <div class="hidden sm:block">
                <p class="text-sm font-medium">{{ Auth::guard('admin')->user()->name }}</p>
                <p class="text-xs text-gray-500 dark:text-gray-400">{{ Auth::guard('admin')->user()->email }}</p>
              </div>
            </div>
            <form method="POST" action="{{ route('admin.logout') }}">
              @csrf
              <button class="text-sm text-red-600 hover:underline">Logout</button>
            </form>
          </div>
        </div>
      </header>

      <!-- Content -->
      <main class="flex-1 overflow-y-auto p-4 lg:p-8">
        <div id="invoice-content" class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-6 max-w-5xl mx-auto">

          <!-- Invoice Header -->
          <div class="flex justify-between items-start mb-8">
            <div>
              <h1 class="text-3xl font-bold">{{ $invoice->invoice_number }}</h1>
              <p class="text-sm text-gray-500 mt-1">Dibuat: {{ $invoice->created_at->format('d M Y H:i') }}</p>
            </div>
            <div class="text-right">
              {!! $invoice->status_badge !!}
              <p class="text-sm mt-2">Jatuh tempo: {{ $invoice->due_date->format('d M Y') }}</p>
            </div>
          </div>

          <!-- Client Info -->
          <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
            <div>
              <h3 class="font-semibold mb-3">Ditagihkan Kepada:</h3>
              <p class="font-medium">{{ $invoice->client_name }}</p>
              @if($invoice->client_email)
                <p class="text-sm text-gray-600 dark:text-gray-400">{{ $invoice->client_email }}</p>
              @endif
              @if($invoice->client_address)
                <p class="text-sm text-gray-600 dark:text-gray-400 mt-2">{{ nl2br(e($invoice->client_address)) }}</p>
              @endif
            </div>
            <div class="text-right">
              <h3 class="font-semibold mb-3">Tanggal Invoice:</h3>
              <p class="text-lg">{{ $invoice->invoice_date->format('d F Y') }}</p>
            </div>
          </div>

          <!-- Items Table -->
          <div class="mb-8">
            <table class="w-full border-collapse">
              <thead>
                <tr class="border-b dark:border-gray-700">
                  <th class="text-left py-3">Deskripsi</th>
                  <th class="text-center py-3 w-24">Qty</th>
                  <th class="text-right py-3 w-32">Harga Satuan</th>
                  <th class="text-right py-3 w-32">Subtotal</th>
                </tr>
              </thead>
              <tbody>
                @foreach($invoice->items as $item)
                <tr class="border-b dark:border-gray-700">
                  <td class="py-4">{{ $item['description'] ?? '-' }}</td>
                  <td class="text-center py-4">{{ $item['quantity'] ?? 0 }}</td>
                  <td class="text-right py-4">Rp {{ number_format($item['price'] ?? 0, 0, ',', '.') }}</td>
                  <td class="text-right py-4 font-medium">Rp {{ number_format(($item['quantity'] ?? 0) * ($item['price'] ?? 0), 0, ',', '.') }}</td>
                </tr>
                @endforeach
              </tbody>
            </table>
          </div>

          <!-- Totals -->
          <div class="ml-auto max-w-md">
            <div class="flex justify-between py-2">
              <span>Subtotal</span>
              <span class="font-medium">Rp {{ number_format($invoice->subtotal, 0, ',', '.') }}</span>
            </div>
            <div class="flex justify-between py-2">
              <span>PPN (11%)</span>
              <span class="font-medium">Rp {{ number_format($invoice->tax, 0, ',', '.') }}</span>
            </div>
            <div class="flex justify-between py-3 text-xl font-bold border-t dark:border-gray-700">
              <span>Total</span>
              <span class="text-primary">Rp {{ number_format($invoice->total, 0, ',', '.') }}</span>
            </div>
          </div>

          <!-- Notes -->
          @if($invoice->notes)
          <div class="mt-10">
            <h3 class="font-semibold mb-3">Catatan:</h3>
            <p class="text-gray-600 dark:text-gray-400 whitespace-pre-wrap">{{ $invoice->notes }}</p>
          </div>
          @endif

          <!-- Actions -->
          <div class="mt-10 flex justify-end space-x-4">
            <a href="{{ route('admin.invoices.edit', $invoice) }}" class="px-6 py-3 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700">
              <i class="fas fa-edit mr-2"></i> Edit
            </a>
            <button id="print-pdf" class="px-6 py-3 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700">
              <i class="fas fa-file-pdf mr-2"></i> Cetak PDF
            </button>
            <a href="{{ route('admin.invoices.index') }}" class="px-6 py-3 bg-primary hover:bg-secondary text-white rounded-lg">
              <i class="fas fa-arrow-left mr-2"></i> Kembali ke Daftar
            </a>
          </div>

        </div>
      </main>
    </div>
  </div>

  <!-- JavaScript: Sidebar & Dark Mode -->
  <script>
    const sidebar = document.getElementById('sidebar');
    const openBtn = document.getElementById('open-sidebar');
    const closeBtn = document.getElementById('close-sidebar');
    const overlay = document.getElementById('overlay');

    openBtn.addEventListener('click', () => {
      sidebar.classList.remove('-translate-x-full');
      overlay.classList.remove('hidden');
    });
    closeBtn.addEventListener('click', () => {
      sidebar.classList.add('-translate-x-full');
      overlay.classList.add('hidden');
    });
    overlay.addEventListener('click', () => {
      sidebar.classList.add('-translate-x-full');
      overlay.classList.add('hidden');
    });

    const darkToggle = document.getElementById('dark-toggle');
    const html = document.documentElement;
    darkToggle.addEventListener('click', () => {
      html.classList.toggle('dark');
      const isDark = html.classList.contains('dark');
      darkToggle.innerHTML = isDark ? '<i class="fas fa-sun text-lg"></i>' : '<i class="fas fa-moon text-lg"></i>';
      localStorage.setItem('darkMode', isDark);
    });
    if (localStorage.getItem('darkMode') === 'true' || (!localStorage.getItem('darkMode') && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
      html.classList.add('dark');
      darkToggle.innerHTML = '<i class="fas fa-sun text-lg"></i>';
    }
  </script>

  <!-- html2pdf (CDN) and PDF generation script -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.9.3/html2pdf.bundle.min.js"></script>
  <script>
    // gunakan data URI jika file ada, supaya tidak terganggu CORS
    @php
      $logo_path = public_path('images/logo.jpg');
      if (file_exists($logo_path)) {
          $logo_data = 'data:image/jpeg;base64,' . base64_encode(file_get_contents($logo_path));
      } else {
          $logo_data = asset('images/logo.jpg'); // fallback ke URL biasa
      }
    @endphp
    const logoUrl = "{!! $logo_data !!}";

    document.getElementById('print-pdf').addEventListener('click', function () {
      const invoiceEl = document.getElementById('invoice-content');
      if (!invoiceEl) return;

      const clone = invoiceEl.cloneNode(true);
      clone.querySelectorAll('*').forEach(el => el.classList.remove('dark'));

      // buat header HTML tapi jangan langsung generate — tunggu gambar ter-load jika bukan dataURI
      const headerHtml = `
        <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:6px;">
          <div style="display:flex;align-items:center;gap:12px;">
            <img id="pdf-logo" src="${logoUrl}" style="width:60px;height:auto;object-fit:contain"/>
            <div>
              <div style="font-family:Inter, sans-serif;font-weight:800;color:#006400;font-size:14px">ART DEVATA IT SOLUTION</div>
              <div style="font-family:Inter, sans-serif;font-size:11px;color:#6b7280;margin-top:4px"> ARTDEVATA</div>
            </div>
          </div>
          <div style="text-align:right;font-family:Inter, sans-serif;font-size:11px;color:#374151;">
            <div>📞 +62 897-4590-050</div>
            <div>🌐 https://artdevata.net</div>
            <div>✉ artdevata@gmail.com</div>
          </div>
        </div>
        <div style="height:8px;border-top:3px solid #006400;margin-bottom:12px"></div>

        <div style="display:flex;justify-content:space-between;align-items:center;margin:6px 0 18px;">
          <h1 style="margin:0;font-family:Inter, sans-serif;color:#006400;font-size:32px;font-weight:700;">INVOICE</h1>
          <div style="text-align:right;font-family:Inter, sans-serif;font-size:12px;color:#374151;">
            <div style="font-weight:700">No. Invoice</div>
            <div style="font-size:14px;margin-top:6px">#{{ $invoice->invoice_number }}</div>
          </div>
        </div>
      `;

      // Build wrapper and inject header + styles
      const wrapper = document.createElement('div');
      wrapper.style.background = '#ffffff';
      wrapper.style.padding = '18px';
      wrapper.style.boxSizing = 'border-box';

      // Insert header, then cloned content
      wrapper.insertAdjacentHTML('beforeend', headerHtml);
      wrapper.appendChild(clone);

      // tunggu logo load (jika remote) sebelum generate
      const img = wrapper.querySelector('#pdf-logo');
      const proceed = () => {
        const styleEl = document.createElement('style');
        styleEl.innerHTML = `
          @page { size: A4; margin: 10mm; }
          body { font-family: Inter, system-ui, -apple-system, "Segoe UI", Roboto, Arial; color:#111827; }
          table { width:100%; border-collapse: collapse; font-size:13px; }
          table thead th, table th { background:#006400 !important; color:#ffffff !important; padding:10px 8px; text-align:left; font-weight:700; }
          table, table td, table th { border:1px solid #e6e6e6; }
          tbody td { padding:10px 8px; vertical-align:top; color:#111827; }
          .payment-info { background-color:#f8f9fa; padding:12px; border-left:4px solid #006400; font-size:11px; margin-top:16px; }
          .no-print, button, .sidebar-hover, a[href^="javascript:"], .fa { display:none !important; }
          img { max-width:100%; }
          .text-right { text-align:right; }
        `;
        wrapper.insertBefore(styleEl, wrapper.firstChild);
        wrapper.querySelectorAll('button, a').forEach(el => el.style.display = 'none');

        const filename = 'Invoice_{{ $invoice->invoice_number }}_' + new Date().toISOString().slice(0,10) + '.pdf';
        const opt = {
          margin:       [10,10,10,10],
          filename:     filename,
          image:        { type: 'jpeg', quality: 0.98 },
          html2canvas:  { scale: 2, useCORS: true, logging: false },
          jsPDF:        { unit: 'mm', format: 'a4', orientation: 'portrait' }
        };
        html2pdf().set(opt).from(wrapper).save();
      };

      if (!img) {
        proceed();
      } else if (img.complete && img.naturalWidth !== 0) {
        proceed();
      } else {
        img.addEventListener('load', proceed);
        img.addEventListener('error', function () {
          // kalau gagal load, lanjut tanpa logo
          img.remove();
          proceed();
        });
      }
    });
  </script>
</body>
</html>