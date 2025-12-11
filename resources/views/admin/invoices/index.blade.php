<!DOCTYPE html>
<html lang="id" class="h-full scroll-smooth">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Daftar Invoice - ARTDEVATA Admin</title>

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
    .card-hover:hover { @apply shadow-xl transform -translate-y-1 transition-all duration-300; }
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
            <h2 class="text-xl font-semibold">Daftar Invoice</h2>
          </div>
          <div class="flex items-center space-x-4">
            <!-- Dark Mode Toggle -->
            <button id="dark-toggle" class="p-2 rounded-full hover:bg-gray-100 dark:hover:bg-gray-700">
              <i class="fas fa-moon text-lg"></i>
            </button>
            <!-- Profile -->
            <div class="flex items-center space-x-3">
              <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::guard('admin')->user()->name) }}&background=4361ee&color=fff&bold=true" alt="Admin" class="w-10 h-10 rounded-full ring-2 ring-primary"/>
              <div class="hidden sm:block">
                <p class="text-sm font-medium">{{ Auth::guard('admin')->user()->name }}</p>
                <p class="text-xs text-gray-500 dark:text-gray-400">{{ Auth::guard('admin')->user()->email }}</p>
              </div>
            </div>
            <form method="POST" action="{{ route('admin.logout') }}" class="d-inline">
              @csrf
              <button class="text-sm text-red-600 hover:underline">Logout</button>
            </form>
          </div>
        </div>
      </header>

      <!-- Content -->
      <main class="flex-1 overflow-y-auto p-4 lg:p-8">

        <!-- Flash Message -->
        @if(session('success'))
          <div class="mb-6 p-4 bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-100 rounded-lg">
            {{ session('success') }}
          </div>
        @endif

        <!-- Invoice Table -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg overflow-hidden">
          <div class="p-6 border-b dark:border-gray-700 flex justify-between items-center">
            <h3 class="text-lg font-semibold">Semua Invoice ({{ $invoices->total() }})</h3>
            <a href="{{ route('admin.invoices.create') }}" class="bg-primary hover:bg-secondary text-white px-5 py-2.5 rounded-lg transition">
              <i class="fas fa-plus mr-2"></i> Buat Invoice Baru
            </a>
          </div>
          <div class="overflow-x-auto">
            <table class="w-full">
              <thead class="bg-gray-50 dark:bg-gray-700">
                <tr>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">No. Invoice</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Klien</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Tanggal</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Total</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Status</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Aksi</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                @forelse($invoices as $invoice)
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                  <td class="px-6 py-4 font-medium">{{ $invoice->invoice_number }}</td>
                  <td class="px-6 py-4">{{ $invoice->client_name }}</td>
                  <td class="px-6 py-4 text-sm">{{ $invoice->invoice_date->format('d M Y') }}</td>
                  <td class="px-6 py-4 font-medium">Rp {{ number_format($invoice->total, 0, ',', '.') }}</td>
                  <td class="px-6 py-4">
                    <div class="flex items-center space-x-3">
                      <span class="status-badge" data-id="{{ $invoice->id }}">{!! $invoice->status_badge !!}</span>
                      <select class="status-select bg-white dark:bg-gray-800 border rounded px-2 py-1 text-sm" data-id="{{ $invoice->id }}">
                        @foreach(['draft','sent','paid','overdue'] as $s)
                          <option value="{{ $s }}" {{ $invoice->status === $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                        @endforeach
                      </select>
                    </div>
                  </td>
                  <td class="px-6 py-4 text-sm">
                    <a href="{{ route('admin.invoices.show', $invoice) }}" class="text-primary hover:underline">Lihat</a>
                    <a href="{{ route('admin.invoices.edit', $invoice) }}" class="text-blue-600 hover:underline ml-3">Edit</a>
                    <form method="POST" action="{{ route('admin.invoices.destroy', $invoice) }}" class="inline ml-3">
                      @csrf @method('DELETE')
                      <button type="submit" onclick="return confirm('Yakin hapus invoice ini?')" class="text-danger hover:underline">Hapus</button>
                    </form>
                  </td>
                </tr>
                @empty
                <tr>
                  <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                    Belum ada invoice. <a href="{{ route('admin.invoices.create') }}" class="text-primary underline">Buat yang pertama</a>
                  </td>
                </tr>
                @endforelse
              </tbody>
            </table>
          </div>

          <!-- Pagination -->
          @if($invoices->hasPages())
            <div class="p-4 border-t dark:border-gray-700">
              {{ $invoices->links() }}
            </div>
          @endif
        </div>

      </main>
    </div>
  </div>

  <!-- JavaScript (Sidebar + Dark Mode) -->
  <script>
    // Sidebar Mobile
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

    // Dark Mode
    const darkToggle = document.getElementById('dark-toggle');
    const html = document.documentElement;

    darkToggle.addEventListener('click', () => {
      html.classList.toggle('dark');
      const isDark = html.classList.contains('dark');
      darkToggle.innerHTML = isDark ? '<i class="fas fa-sun text-lg"></i>' : '<i class="fas fa-moon text-lg"></i>';
      localStorage.setItem('darkMode', isDark);
    });

    if (localStorage.getItem('darkMode') === 'true' || 
        (!localStorage.getItem('darkMode') && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
      html.classList.add('dark');
      darkToggle.innerHTML = '<i class="fas fa-sun text-lg"></i>';
    }
  </script>

  <!-- setelah script sidebar/dark mode, tambahkan JS ini -->
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
          alert('Terjadi kesalahan jaringan');
        }
      });
    });
  </script>

</body>
</html>