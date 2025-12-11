<!DOCTYPE html>
<html lang="id" class="h-full scroll-smooth">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Buat Invoice Baru - ARTDEVATA Admin</title>

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
            <h2 class="text-xl font-semibold">Buat Invoice Baru</h2>
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
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-6 max-w-4xl mx-auto">
          <form method="POST" action="{{ route('admin.invoices.store') }}">
            @csrf

            <!-- Client Info -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
              <div>
                <label class="block text-sm font-medium mb-2">Nama Klien <span class="text-danger">*</span></label>
                <input type="text" name="client_name" required class="w-full px-4 py-2 border rounded-lg dark:bg-gray-700 dark:border-gray-600" placeholder="Nama lengkap klien">
              </div>
              <div>
                <label class="block text-sm font-medium mb-2">Email Klien</label>
                <input type="email" name="client_email" class="w-full px-4 py-2 border rounded-lg dark:bg-gray-700 dark:border-gray-600" placeholder="email@klien.com">
              </div>
            </div>

            <div class="mb-6">
              <label class="block text-sm font-medium mb-2">Alamat Klien</label>
              <textarea name="client_address" rows="3" class="w-full px-4 py-2 border rounded-lg dark:bg-gray-700 dark:border-gray-600" placeholder="Alamat lengkap"></textarea>
            </div>

            <!-- Dates -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
              <div>
                <label class="block text-sm font-medium mb-2">Tanggal Invoice <span class="text-danger">*</span></label>
                <input type="date" name="invoice_date" required value="{{ old('invoice_date', today()->format('Y-m-d')) }}" class="w-full px-4 py-2 border rounded-lg dark:bg-gray-700 dark:border-gray-600">
              </div>
              <div>
                <label class="block text-sm font-medium mb-2">Jatuh Tempo <span class="text-danger">*</span></label>
                <input type="date" name="due_date" required value="{{ old('due_date', today()->addDays(14)->format('Y-m-d')) }}" class="w-full px-4 py-2 border rounded-lg dark:bg-gray-700 dark:border-gray-600">
              </div>
            </div>

            <!-- Items (Dynamic with JavaScript) -->
            <div class="mb-6">
              <h3 class="text-lg font-medium mb-4">Item Invoice</h3>
              <div id="items-container">
                <div class="item-row grid grid-cols-12 gap-4 mb-3 items-end">
                  <div class="col-span-5">
                    <input type="text" name="items[0][description]" required placeholder="Deskripsi item" class="w-full px-4 py-2 border rounded-lg dark:bg-gray-700 dark:border-gray-600">
                  </div>
                  <div class="col-span-2">
                    <input type="number" name="items[0][quantity]" required min="1" value="1" placeholder="Qty" class="w-full px-4 py-2 border rounded-lg dark:bg-gray-700 dark:border-gray-600">
                  </div>
                  <div class="col-span-3">
                    <input type="number" name="items[0][price]" required min="0" step="1000" placeholder="Harga" class="w-full px-4 py-2 border rounded-lg dark:bg-gray-700 dark:border-gray-600">
                  </div>
                  <div class="col-span-2 text-right font-medium subtotal">Rp 0</div>
                </div>
              </div>
              <button type="button" id="add-item" class="mt-3 text-primary hover:underline text-sm">
                <i class="fas fa-plus mr-1"></i> Tambah Item
              </button>
            </div>

            <!-- Totals -->
            <div class="border-t pt-4 text-right space-y-2">
              <p>Subtotal: <span id="subtotal-display" class="font-bold">Rp 0</span></p>
              <p>PPN 11%: <span id="tax-display" class="font-bold">Rp 0</span></p>
              <p class="text-xl">Total: <span id="total-display" class="font-bold text-primary">Rp 0</span></p>
            </div>

            <!-- Notes -->
            <div class="mt-6">
              <label class="block text-sm font-medium mb-2">Catatan (opsional)</label>
              <textarea name="notes" rows="4" class="w-full px-4 py-2 border rounded-lg dark:bg-gray-700 dark:border-gray-600" placeholder="Catatan tambahan untuk klien..."></textarea>
            </div>

            <!-- Submit -->
            <div class="mt-8 flex justify-end space-x-4">
              <a href="{{ route('admin.invoices.index') }}" class="px-6 py-3 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700">Batal</a>
              <button type="submit" class="px-6 py-3 bg-primary hover:bg-secondary text-white rounded-lg">Simpan Invoice</button>
            </div>
          </form>
        </div>
      </main>
    </div>
  </div>

  <!-- JavaScript: Sidebar, Dark Mode, Dynamic Items & Calculation -->
  <script>
    // Sidebar & Dark Mode (sama seperti sebelumnya)
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

    // Dynamic Items & Calculation
    let itemIndex = 1;
    document.getElementById('add-item').addEventListener('click', () => {
      const container = document.getElementById('items-container');
      const row = document.createElement('div');
      row.className = 'item-row grid grid-cols-12 gap-4 mb-3 items-end';
      row.innerHTML = `
        <div class="col-span-5">
          <input type="text" name="items[${itemIndex}][description]" required placeholder="Deskripsi item" class="w-full px-4 py-2 border rounded-lg dark:bg-gray-700 dark:border-gray- FIRST600">
        </div>
        <div class="col-span-2">
          <input type="number" name="items[${itemIndex}][quantity]" required min="1" value="1" placeholder="Qty" class="w-full px-4 py-2 border rounded-lg dark:bg-gray-700 dark:border-gray-600 item-qty">
        </div>
        <div class="col-span-3">
          <input type="number" name="items[${itemIndex}][price]" required min="0" step="1000" placeholder="Harga" class="w-full px-4 py-2 border rounded-lg dark:bg-gray-700 dark:border-gray-600 item-price">
        </div>
        <div class="col-span-1 text-right">
          <button type="button" class="text-danger remove-item">&times;</button>
        </div>
        <div class="col-span-1 text-right font-medium subtotal">Rp 0</div>
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
          const subtotal = (qty.value || 0) * (price.value || 0);
          subtotalEl.textContent = 'Rp ' + subtotal.toLocaleString('id-ID');
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

      document.getElementById('subtotal-display').textContent = 'Rp ' + subtotal.toLocaleString('id-ID');
      document.getElementById('tax-display').textContent = 'Rp ' + tax.toLocaleString('id-ID');
      document.getElementById('total-display').textContent = 'Rp ' + total.toLocaleString('id-ID');
    }

    // Initial calculation
    attachItemEvents();
  </script>
</body>
</html>