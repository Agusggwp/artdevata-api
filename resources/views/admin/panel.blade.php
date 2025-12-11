<!DOCTYPE html>
<html lang="id" class="h-full scroll-smooth">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>AdminPanel Pro - Responsif & Modern</title>

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
  
  <!-- Chart.js CDN -->
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

  <style>
    .sidebar-active { @apply bg-gradient-to-r from-primary to-secondary text-white; }
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
          <i class="fas fa-cog mr-3"></i> Blog
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
            <h2 class="text-xl font-semibold">Dashboard</h2>
          </div>
          <div class="flex items-center space-x-4">
            <a href="{{ route('admin.salaries.index') }}" class="px-3 py-2 rounded-md bg-yellow-500 hover:bg-yellow-600 text-white text-sm flex items-center">
              <i class="fas fa-wallet mr-2"></i> Gaji
            </a>
            <!-- Dark Mode Toggle -->
            <button id="dark-toggle" class="p-2 rounded-full hover:bg-gray-100 dark:hover:bg-gray-700">
              <i class="fas fa-moon text-lg"></i>
            </button>
            <!-- Notification -->
            <button class="relative p-2 rounded-full hover:bg-gray-100 dark:hover:bg-gray-700">
              <i class="fas fa-bell text-lg"></i>
              <span class="absolute top-0 right-0 w-2 h-2 bg-danger rounded-full"></span>
            </button>
            <!-- Profile -->
            <div class="flex items-center space-x-3">
              <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::guard('admin')->user()->name) }}&background=4361ee&color=fff&bold=true" alt="Admin" class="w-10 h-10 rounded-full ring-2 ring-primary"/>
              <div class="hidden sm:block">
                <p class="text-sm font-medium">{{ Auth::guard('admin')->user()->name }}</p>
                <p class="text-xs text-gray-500 dark:text-gray-400">{{ Auth::guard('admin')->user()->email }}</p>
              </div>
            </div>
            <!-- Logout -->
            <form method="POST" action="{{ route('admin.logout') }}" class="d-inline">
              @csrf
              <button class="text-sm text-red-600 hover:underline">Logout</button>
            </form>
          </div>
        </div>
      </header>

      <!-- Content -->
      <main class="flex-1 overflow-y-auto p-4 lg:p-8">

        <!-- SALDO PERUSAHAAN (PUSAT PERHATIAN) -->
        <div class="mb-6">
          <div class="bg-gradient-to-r from-yellow-400 to-yellow-500 text-gray-900 p-6 rounded-2xl shadow-xl border border-yellow-300">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-sm font-medium opacity-90">Saldo Perusahaan</p>
                <p class="text-4xl font-bold mt-2">Rp {{ number_format($companyBalance ?? 0, 0, ',', '.') }}</p>
                <p class="text-xs mt-1 opacity-80">Saldo = Invoice diterima - Gaji terbayar + Penyesuaian manual</p>
              </div>
              <div class="flex items-center gap-3">
                <a href="{{ route('admin.salaries.index') }}" class="px-4 py-2 bg-white/90 rounded-md text-sm font-semibold shadow hover:opacity-95">
                  <i class="fas fa-wallet mr-2"></i> Kelola Gaji
                </a>
                <a href="{{ route('admin.finance.transactions.index') }}" class="px-4 py-2 bg-white/20 border border-white/30 rounded-md text-sm">
                  Riwayat
                </a>
              </div>
            </div>
          </div>
        </div>

        <!-- GROUP 1: INVOICE & PENDAPATAN -->
        <div class="mb-8">
          <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4 flex items-center">
            <i class="fas fa-file-invoice text-blue-600 mr-2"></i> Invoice & Pendapatan
          </h3>
          <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <!-- Card 1: Total Invoice -->
            <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-lg card-hover border border-gray-200 dark:border-gray-700">
              <div class="flex items-center justify-between">
                <div>
                  <p class="text-sm text-gray-500 dark:text-gray-400 mb-2">Total Invoice</p>
                  <p class="text-3xl font-bold text-gray-800 dark:text-white">{{ $totalInvoiceCount ?? 0 }}</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900 rounded-full flex items-center justify-center">
                  <i class="fas fa-file-invoice text-blue-600 dark:text-blue-400 text-xl"></i>
                </div>
              </div>
            </div>

            <!-- Card 2: Total Pendapatan -->
            <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-lg card-hover border border-gray-200 dark:border-gray-700">
              <div class="flex items-center justify-between">
                <div>
                  <p class="text-sm text-gray-500 dark:text-gray-400 mb-2">Total Pendapatan</p>
                  <p class="text-3xl font-bold text-gray-800 dark:text-white">Rp {{ number_format($totalInvoiceAmount ?? 0, 0, ',', '.') }}</p>
                </div>
                <div class="w-12 h-12 bg-green-100 dark:bg-green-900 rounded-full flex items-center justify-center">
                  <i class="fas fa-dollar-sign text-green-600 dark:text-green-400 text-xl"></i>
                </div>
              </div>
            </div>

            <!-- Card 3: Invoice Terbayar -->
            <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-lg card-hover border border-gray-200 dark:border-gray-700">
              <div class="flex items-center justify-between">
                <div>
                  <p class="text-sm text-gray-500 dark:text-gray-400 mb-2">Invoice Terbayar</p>
                  <p class="text-3xl font-bold text-gray-800 dark:text-white">{{ $paidInvoiceCount ?? 0 }}</p>
                </div>
                <div class="w-12 h-12 bg-yellow-100 dark:bg-yellow-900 rounded-full flex items-center justify-center">
                  <i class="fas fa-check-circle text-yellow-600 dark:text-yellow-400 text-xl"></i>
                </div>
              </div>
            </div>

            <!-- Card 4: Uang Diterima -->
            <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-lg card-hover border border-gray-200 dark:border-gray-700">
              <div class="flex items-center justify-between">
                <div>
                  <p class="text-sm text-gray-500 dark:text-gray-400 mb-2">Uang Diterima</p>
                  <p class="text-3xl font-bold text-green-600 dark:text-green-400">Rp {{ number_format($totalPaidAmount ?? 0, 0, ',', '.') }}</p>
                </div>
                <div class="w-12 h-12 bg-emerald-100 dark:bg-emerald-900 rounded-full flex items-center justify-center">
                  <i class="fas fa-money-bill-wave text-emerald-600 dark:text-emerald-400 text-xl"></i>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- GROUP 2: KONTEN (Blog, Portfolio, Layanan) -->
        <div class="mb-8">
          <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4 flex items-center">
            <i class="fas fa-layer-group text-purple-600 mr-2"></i> Konten & Layanan
          </h3>
          <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            
            <!-- Card Blog -->
            <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-lg card-hover border border-gray-200 dark:border-gray-700">
              <div class="flex items-center justify-between mb-4">
                <div>
                  <p class="text-sm text-gray-500 dark:text-gray-400 mb-2">Total Blog</p>
                  <p class="text-3xl font-bold text-gray-800 dark:text-white">{{ $totalBlogs ?? 0 }}</p>
                </div>
                <div class="w-12 h-12 bg-purple-100 dark:bg-purple-900 rounded-full flex items-center justify-center">
                  <i class="fas fa-blog text-purple-600 dark:text-purple-400 text-xl"></i>
                </div>
              </div>
              <a href="{{ route('admin.blogs.index') }}" class="text-sm text-purple-600 dark:text-purple-400 hover:underline flex items-center">
                Lihat Blog <i class="fas fa-arrow-right ml-2 text-xs"></i>
              </a>
            </div>

            <!-- Card Portfolio -->
            <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-lg card-hover border border-gray-200 dark:border-gray-700">
              <div class="flex items-center justify-between mb-4">
                <div>
                  <p class="text-sm text-gray-500 dark:text-gray-400 mb-2">Total Portfolio</p>
                  <p class="text-3xl font-bold text-gray-800 dark:text-white">{{ $totalPortfolios ?? 0 }}</p>
                </div>
                <div class="w-12 h-12 bg-indigo-100 dark:bg-indigo-900 rounded-full flex items-center justify-center">
                  <i class="fas fa-briefcase text-indigo-600 dark:text-indigo-400 text-xl"></i>
                </div>
              </div>
              <a href="{{ route('admin.portfolios.index') }}" class="text-sm text-indigo-600 dark:text-indigo-400 hover:underline flex items-center">
                Lihat Portfolio <i class="fas fa-arrow-right ml-2 text-xs"></i>
              </a>
            </div>

            <!-- Card Layanan -->
            <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-lg card-hover border border-gray-200 dark:border-gray-700">
              <div class="flex items-center justify-between mb-4">
                <div>
                  <p class="text-sm text-gray-500 dark:text-gray-400 mb-2">Total Layanan</p>
                  <p class="text-3xl font-bold text-gray-800 dark:text-white">{{ $totalServices ?? 0 }}</p>
                </div>
                <div class="w-12 h-12 bg-pink-100 dark:bg-pink-900 rounded-full flex items-center justify-center">
                  <i class="fas fa-cogs text-pink-600 dark:text-pink-400 text-xl"></i>
                </div>
              </div>
              <a href="{{ route('admin.services.index') }}" class="text-sm text-pink-600 dark:text-pink-400 hover:underline flex items-center">
                Lihat Layanan <i class="fas fa-arrow-right ml-2 text-xs"></i>
              </a>
            </div>

          </div>
        </div>

        <!-- GROUP 3: PROYEK & STATUS -->
        <div class="mb-8">
          <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4 flex items-center">
            <i class="fas fa-tasks text-cyan-600 mr-2"></i> Proyek & Status
          </h3>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

            <!-- Card Pengelolaan Proyek -->
            <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-lg card-hover border border-gray-200 dark:border-gray-700">
              <div class="flex items-center justify-between mb-6">
                <div>
                  <p class="text-sm text-gray-500 dark:text-gray-400 mb-2">Pengelolaan Proyek</p>
                  <p class="text-3xl font-bold text-gray-800 dark:text-white">{{ $totalProjects ?? 0 }}</p>
                </div>
                <div class="w-12 h-12 bg-cyan-100 dark:bg-cyan-900 rounded-full flex items-center justify-center">
                  <i class="fas fa-tasks text-cyan-600 dark:text-cyan-400 text-xl"></i>
                </div>
              </div>
              <div class="space-y-3 mb-4">
                <div class="flex justify-between items-center">
                  <span class="text-sm text-gray-600 dark:text-gray-400">Sedang Berjalan</span>
                  <span class="text-lg font-bold text-cyan-600">{{ $ongoingProjects ?? 0 }}</span>
                </div>
                <div class="flex justify-between items-center">
                  <span class="text-sm text-gray-600 dark:text-gray-400">Selesai</span>
                  <span class="text-lg font-bold text-green-600">{{ $completedProjects ?? 0 }}</span>
                </div>
                <div class="flex justify-between items-center">
                  <span class="text-sm text-gray-600 dark:text-gray-400">Tertunda</span>
                  <span class="text-lg font-bold text-red-600">{{ $pendingProjects ?? 0 }}</span>
                </div>
              </div>
              <a href="{{ route('admin.projects.index') ?? '#' }}" class="text-sm text-cyan-600 dark:text-cyan-400 hover:underline flex items-center">
                Kelola Proyek <i class="fas fa-arrow-right ml-2 text-xs"></i>
              </a>
            </div>

            <!-- Card Ringkasan Invoice Status -->
            <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-lg card-hover border border-gray-200 dark:border-gray-700">
              <div class="flex items-center justify-between mb-6">
                <div>
                  <p class="text-sm text-gray-500 dark:text-gray-400 mb-2">Status Invoice</p>
                  <p class="text-3xl font-bold text-gray-800 dark:text-white">{{ $totalInvoiceCount ?? 0 }}</p>
                </div>
                <div class="w-12 h-12 bg-lime-100 dark:bg-lime-900 rounded-full flex items-center justify-center">
                  <i class="fas fa-file-alt text-lime-600 dark:text-lime-400 text-xl"></i>
                </div>
              </div>
              <div class="space-y-3 mb-4">
                <div class="flex justify-between items-center">
                  <span class="text-sm text-gray-600 dark:text-gray-400">Dibayar</span>
                  <span class="text-lg font-bold text-green-600">{{ $paidInvoiceCount ?? 0 }}</span>
                </div>
                <div class="flex justify-between items-center">
                  <span class="text-sm text-gray-600 dark:text-gray-400">Tertunda</span>
                  <span class="text-lg font-bold text-orange-600">{{ ($totalInvoiceCount ?? 0) - ($paidInvoiceCount ?? 0) }}</span>
                </div>
                <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                  <div class="bg-green-600 h-2 rounded-full" style="width: {{ $totalInvoiceCount > 0 ? (($paidInvoiceCount ?? 0) / ($totalInvoiceCount ?? 1) * 100) : 0 }}%"></div>
                </div>
              </div>
              <a href="{{ route('admin.invoices.index') }}" class="text-sm text-lime-600 dark:text-lime-400 hover:underline flex items-center">
                Lihat Invoice <i class="fas fa-arrow-right ml-2 text-xs"></i>
              </a>
            </div>

          </div>
        </div>

        <!-- GROUP 4: PAYROLL (Gaji QA & Developer) -->
        <div class="mb-8">
          <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4 flex items-center">
            <i class="fas fa-money-check text-green-600 mr-2"></i> Payroll Proyek Selesai
          </h3>
          <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-lg card-hover border border-gray-200 dark:border-gray-700">
              <p class="text-sm text-gray-500 dark:text-gray-400 mb-2">Total untuk QA (5% dari proyek selesai)</p>
              <p class="text-2xl font-bold text-gray-800 dark:text-white">Rp {{ number_format($totalQAPayout ?? 0, 0, ',', '.') }}</p>
              <p class="text-xs text-gray-500 mt-2">Belum terdistribusi: Rp {{ number_format($unassignedQAPool ?? 0, 0, ',', '.') }}</p>
            </div>

            <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-lg card-hover border border-gray-200 dark:border-gray-700">
              <p class="text-sm text-gray-500 dark:text-gray-400 mb-2">Total untuk Developer (25% dari proyek selesai)</p>
              <p class="text-2xl font-bold text-gray-800 dark:text-white">Rp {{ number_format($totalDevPayout ?? 0, 0, ',', '.') }}</p>
              <p class="text-xs text-gray-500 mt-2">Belum terdistribusi: Rp {{ number_format($unassignedDevPool ?? 0, 0, ',', '.') }}</p>
            </div>
          </div>
        </div>

        <!-- GROUP 5: SALDO & TRANSAKSI MANUAL -->
        <div class="mb-8">
          <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4 flex items-center">
            <i class="fas fa-credit-card text-yellow-600 mr-2"></i> Manajemen Saldo
          </h3>
          <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-lg card-hover border border-gray-200 dark:border-gray-700">
            <!-- Form singkat tambah/kurangi saldo -->
            <form action="{{ route('admin.finance.transaction.store') }}" method="POST" class="space-y-4">
              @csrf
              <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                <select name="type" required class="px-3 py-2 border rounded-lg dark:bg-gray-700 dark:border-gray-600">
                  <option value="credit">Tambah (Credit)</option>
                  <option value="debit">Kurangi (Debit)</option>
                </select>
                <input type="number" name="amount" step="0.01" min="0.01" placeholder="Jumlah (Rp)" required class="px-3 py-2 border rounded-lg dark:bg-gray-700 dark:border-gray-600"/>
                <input type="text" name="description" placeholder="Keterangan (opsional)" class="px-3 py-2 border rounded-lg dark:bg-gray-700 dark:border-gray-600"/>
              </div>
              <div class="flex justify-end">
                <button type="submit" class="px-4 py-2 bg-primary text-white rounded-lg hover:opacity-90">Simpan Transaksi</button>
              </div>
            </form>

            <!-- Recent transactions -->
            <div class="mt-6 border-t pt-4">
              <h4 class="text-sm font-semibold mb-3">Transaksi Terakhir</h4>
              <div class="space-y-2 max-h-64 overflow-auto">
                @forelse($recentTransactions ?? [] as $t)
                  <div class="flex items-center justify-between text-sm p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                    <div>
                      <div class="font-medium">{{ $t->admin->name ?? 'System' }}</div>
                      <div class="text-xs text-gray-500">{{ $t->description ?? ($t->type === 'credit' ? 'Penyesuaian +':'Penyesuaian -') }} • {{ $t->created_at->format('d M Y H:i') }}</div>
                    </div>
                    <div class="text-sm font-semibold @if($t->type==='credit') text-green-700 @else text-red-700 @endif">
                      @if($t->type==='credit') + @else - @endif Rp {{ number_format($t->amount,0,',','.') }}
                    </div>
                  </div>
                @empty
                  <div class="text-sm text-gray-500 text-center py-3">Belum ada penyesuaian.</div>
                @endforelse
              </div>
              <div class="mt-3 text-center">
                <a href="{{ route('admin.finance.transactions.index') }}" class="text-sm text-primary hover:underline">Lihat Riwayat Lengkap →</a>
              </div>
            </div>
          </div>
        </div>

        <!-- Chart + Activity -->
        <div class="mb-8">
          <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4 flex items-center">
            <i class="fas fa-chart-line text-indigo-600 mr-2"></i> Aktivitas & Analytics
          </h3>
          <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            <!-- 1. Grafik Aktivitas Konten (7 Hari Terakhir) -->
            <div class="lg:col-span-2 bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-xl card-hover border border-gray-200 dark:border-gray-700">
              <div class="flex items-items-center justify-between mb-4">
                <h3 class="text-lg font-bold text-gray-800 dark:text-white">Aktivitas Konten (7 Hari)</h3>
                <div class="flex space-x-2">
                  <span class="flex items-center text-xs">
                    <i class="fas fa-circle text-blue-500 mr-1"></i> Blog
                  </span>
                  <span class="flex items-center text-xs">
                    <i class="fas fa-circle text-green-500 mr-1"></i> Layanan
                  </span>
                  <span class="flex items-center text-xs">
                    <i class="fas fa-circle text-orange-500 mr-1"></i> Portfolio
                  </span>
                </div>
              </div>
              <canvas id="contentChart" class="h-64"></canvas>
            </div>

            <!-- 2. Konten Terbaru (3 Item) -->
            <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-xl card-hover border border-gray-200 dark:border-gray-700">
              <h3 class="text-lg font-bold mb-4 text-gray-800 dark:text-white flex items-center">
                <i class="fas fa-sparkles text-yellow-500 mr-2"></i> Konten Terbaru
              </h3>
              <div class="space-y-4">

                @php
                  $latest = collect()
                    ->merge(\App\Models\Blog::latest()->take(1)->get()->map(fn($i) => ['type' => 'Blog', 'item' => $i, 'color' => 'blue']))
                    ->merge(\App\Models\Service::latest()->take(1)->get()->map(fn($i) => ['type' => 'Layanan', 'item' => $i, 'color' => 'green']))
                    ->merge(\App\Models\Portfolio::latest()->take(1)->get()->map(fn($i) => ['type' => 'Portfolio', 'item' => $i, 'color' => 'orange']))
                    ->sortByDesc(fn($i) => $i['item']->created_at)
                    ->take(3);
                @endphp

                @foreach($latest as $entry)
                  @php
                    $item = $entry['item'];
                    $color = $entry['color'];
                    $icon = $entry['type'] === 'Blog' ? 'fa-blog' : ($entry['type'] === 'Layanan' ? 'fa-box' : 'fa-briefcase');
                    $bgColor = $color === 'blue' ? 'bg-blue-100 text-blue-700' : 
                              ($color === 'green' ? 'bg-green-100 text-green-700' : 'bg-orange-100 text-orange-700');
                  @endphp
                 <div class="flex items-start space-x-3 p-3 rounded-lg bg-gray-50 dark:bg-gray-700 hover:bg-gray-100 dark:hover:bg-gray-600 transition-all">
            <div class="w-10 h-10 rounded-full {{ $bgColor }} flex items-center justify-center flex-shrink-0">
              <i class="fas {{ $icon }} text-sm"></i>
            </div>
            <div class="flex-1 min-w-0">
              <p class="font-medium text-sm truncate">{{ $item->title }}</p>
              <p class="text-xs text-gray-500 dark:text-gray-400">
                <span class="font-medium">{{ $entry['type'] }}</span> • {{ $item->created_at->diffForHumans() }}
              </p>
            </div>
          </div>
                @endforeach

              </div>
            </div>

          </div>
        </div>

        <!-- TABLE ADMIN -->
        <div class="mb-8">
          <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4 flex items-center">
            <i class="fas fa-users text-purple-600 mr-2"></i> Daftar Admin
          </h3>
          <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg overflow-hidden">
            <div class="overflow-x-auto">
              <table class="w-full">
                <thead class="bg-gray-50 dark:bg-gray-700">
                  <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Nama</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Email</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Bergabung</th>
                  </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                  @foreach(\App\Models\Admin::all() as $admin)
                  <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                    <td class="px-6 py-4 flex items-center space-x-3">
                      <img src="https://ui-avatars.com/api/?name={{ urlencode($admin->name) }}&background=4361ee&color=fff" class="w-8 h-8 rounded-full"/>
                      <span>{{ $admin->name }}</span>
                    </td>
                    <td class="px-6 py-4">{{ $admin->email }}</td>
                    <td class="px-6 py-4 text-sm text-gray-500">{{ $admin->created_at->format('d M Y') }}</td>
                  </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
          </div>
        </div>

        <!-- GROUP 6: DETAIL GAJI PER PROYEK -->
        <div class="mb-8">
          <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4 flex items-center">
            <i class="fas fa-calculator text-teal-600 mr-2"></i> Gaji per Proyek (Proyek Selesai)
          </h3>

          <div class="space-y-4">
            @forelse($projectPayrolls ?? [] as $p)
              <div class="bg-white dark:bg-gray-800 p-5 rounded-2xl shadow-md border border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between mb-3">
                  <div>
                    <div class="text-sm text-gray-500">Proyek</div>
                    <div class="text-lg font-bold text-gray-800 dark:text-white">{{ $p['name'] }}</div>
                    <div class="text-xs text-gray-500">Budget: Rp {{ number_format($p['budget'],0,',','.') }}</div>
                  </div>
                  <div class="text-right">
                    <div class="text-sm text-gray-500">QA (5%)</div>
                    <div class="font-semibold text-gray-800">Rp {{ number_format($p['qa_share'],0,',','.') }}</div>
                    <div class="text-sm text-gray-500 mt-2">Dev (25%)</div>
                    <div class="font-semibold text-gray-800">Rp {{ number_format($p['dev_share'],0,',','.') }}</div>
                  </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-3">
                  <div>
                    <div class="text-sm font-medium mb-2">Rincian QA</div>
                    @if(count($p['qa_details']) > 0)
                      <ul class="space-y-2">
                        @foreach($p['qa_details'] as $d)
                          <li class="flex items-center justify-between px-3 py-2 bg-gray-50 dark:bg-gray-700 rounded">
                            <div>
                              <div class="text-sm font-medium">{{ $d['name'] }}</div>
                              <div class="text-xs text-gray-500">{{ $d['role'] }}</div>
                            </div>
                            <div class="text-sm font-semibold">Rp {{ number_format($d['amount'],0,',','.') }}</div>
                          </li>
                        @endforeach
                      </ul>
                    @else
                      <div class="text-sm text-gray-500">Belum ada QA terdaftar. Unassigned: Rp {{ number_format($p['unassigned_qa'],0,',','.') }}</div>
                    @endif
                  </div>

                  <div>
                    <div class="text-sm font-medium mb-2">Rincian Developer</div>
                    @if(count($p['dev_details']) > 0)
                      <ul class="space-y-2">
                        @foreach($p['dev_details'] as $d)
                          <li class="flex items-center justify-between px-3 py-2 bg-gray-50 dark:bg-gray-700 rounded">
                            <div>
                              <div class="text-sm font-medium">{{ $d['name'] }}</div>
                              <div class="text-xs text-gray-500">{{ $d['role'] }}</div>
                            </div>
                            <div class="text-sm font-semibold">Rp {{ number_format($d['amount'],0,',','.') }}</div>
                          </li>
                        @endforeach
                      </ul>
                    @else
                      <div class="text-sm text-gray-500">Belum ada Developer terdaftar. Unassigned: Rp {{ number_format($p['unassigned_dev'],0,',','.') }}</div>
                    @endif
                  </div>
                </div>
              </div>
            @empty
              <div class="text-sm text-gray-500">Belum ada proyek selesai.</div>
            @endforelse
          </div>
        </div>

      </main>
    </div>
  </div>

  <!-- JavaScript -->
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

    // Chart
    const ctx = document.getElementById('contentChart').getContext('2d');
    new Chart(ctx, {
      type: 'line',
      data: {
        labels: ['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'],
        datasets: [
          {
            label: 'Blog',
            data: [12, 19, 13, 15, 18, 22, 20],
            borderColor: '#4361ee',
            backgroundColor: 'rgba(67, 97, 238, 0.1)',
            fill: true,
            tension: 0.4
          },
          {
            label: 'Layanan',
            data: [8, 10, 12, 9, 14, 11, 13],
            borderColor: '#10b981',
            backgroundColor: 'rgba(16, 185, 129, 0.1)',
            fill: true,
            tension: 0.4
          },
          {
            label: 'Portfolio',
            data: [5, 8, 6, 10, 7, 12, 9],
            borderColor: '#f97316',
            backgroundColor: 'rgba(249, 115, 22, 0.1)',
            fill: true,
            tension: 0.4
          }
        ]
      },
      options: {
        responsive: true,
        plugins: { legend: { display: false } },
        scales: { y: { beginAtZero: true } }
      }
    });
  </script>

</body>
</html>