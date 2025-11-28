<!DOCTYPE html>
<html lang="id" class="h  class="h-full scroll-smooth">
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

        <!-- Stats Cards -->
        <!-- Stats Cards -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 lg:gap-6 mb-8">

  <!-- 1. Total Blog -->
  <div class="bg-gradient-to-r from-blue-500 to-indigo-600 p-6 rounded-2xl text-white card-hover">
    <div class="flex items-center justify-between">
      <div>
        <p class="text-sm opacity-90">Total Blog</p>
        <p class="text-3xl font-bold mt-1">{{ \App\Models\Blog::count() }}</p>
        <p class="text-xs mt-2 opacity-80">Artikel dipublikasikan</p>
      </div>
      <i class="fas fa-blog text-4xl opacity-50"></i>
    </div>
  </div>

  <!-- 2. Total Service -->
  <div class="bg-gradient-to-r from-green-500 to-emerald-600 p-6 rounded-2xl text-white card-hover">
    <div class="flex items-center justify-between">
      <div>
        <p class="text-sm opacity-90">Total Layanan</p>
        <p class="text-3xl font-bold mt-1">{{ \App\Models\Service::count() }}</p>
        <p class="text-xs mt-2 opacity-80">Layanan tersedia</p>
      </div>
      <i class="fas fa-box-open text-4xl opacity-50"></i>
    </div>
  </div>

  <!-- 3. Total Portfolio -->
  <div class="bg-gradient-to-r from-orange-500 to-red-600 p-6 rounded-2xl text-white card-hover">
    <div class="flex items-center justify-between">
      <div>
        <p class="text-sm opacity-90">Total Portfolio</p>
        <p class="text-3xl font-bold mt-1">{{ \App\Models\Portfolio::count() }}</p>
        <p class="text-xs mt-2 opacity-80">Proyek selesai</p>
      </div>
      <i class="fas fa-briefcase text-4xl opacity-50"></i>
    </div>
  </div>

  <!-- 4. Laravel Version -->
  <div class="bg-gradient-to-r from-purple-500 to-pink-600 p-6 rounded-2xl text-white card-hover">
    <div class="flex items-center justify-between">
      <div>
        <p class="text-sm opacity-90">Laravel</p>
        <p class="text-3xl font-bold mt-1">{{ app()->version() }}</p>
        <p class="text-xs mt-2 opacity-80">Framework aktif</p>
      </div>
      <i class="fas fa-code text-4xl opacity-50"></i>
    </div>
  </div>

  <!-- CARD: Buka Web Invoice -->
<div class="bg-gradient-to-r from-emerald-500 to-teal-600 p-6 rounded-2xl text-white card-hover cursor-pointer transition-all duration-300 hover:shadow-2xl hover:scale-105"
     onclick="window.open('https://invoice.artdevata.net/', '_blank')">
  <div class="flex items-center justify-center h-full">
    <div class="text-center">
      <i class="fas fa-file-invoice-dollar text-5xl mb-3 opacity-80"></i>
      <p class="text-lg font-bold">Web Invoice</p>
      <p class="text-xs opacity-80 mt-1 flex items-center justify-center gap-1">
        <i class="fas fa-external-link-alt"></i> Buka Sekarang
      </p>
    </div>
  </div>
</div>


<div class="bg-gradient-to-r from-emerald-500 to-teal-600 p-6 rounded-2xl text-white card-hover cursor-pointer transition-all duration-300 hover:shadow-2xl hover:scale-105"
     onclick="window.open('https://invoice.artdevata.net/', '_blank')">
  <div class="flex items-center justify-center h-full">
    <div class="text-center">
      <!-- Icon Database (Font Awesome 6) -->
      <i class="fas fa-database text-5xl mb-3 opacity-80"></i>
      
      <p class="text-lg font-bold">Web Hosting Panel</p>
      <p class="text-xs opacity-80 mt-1 flex items-center justify-center gap-1">
        <i class="fas fa-external-link-alt"></i> Buka Sekarang
      </p>
    </div>
  </div>
</div>


</div>


</div>

        <!-- Chart + Activity -->
    <!-- Aktivitas Konten + Konten Terbaru -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">

  <!-- 1. Grafik Aktivitas Konten (7 Hari Terakhir) -->
  <div class="lg:col-span-2 bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-xl card-hover border border-gray-200 dark:border-gray-700">
    <div class="flex items-center justify-between mb-4">
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

        <!-- Table Admin -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg overflow-hidden">
          <div class="p-6 border-b dark:border-gray-700">
            <h3 class="text-lg font-semibold">Daftar Admin</h3>
          </div>
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

      </main>
    </div>
  </div>

  <!-- JavaScript (sama seperti asli) -->
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
    const ctx = document.getElementById('trafficChart').getContext('2d');
    new Chart(ctx, {
      type: 'line',
      data: {
        labels: ['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'],
        datasets: [{
          label: 'Login Admin',
          data: [2, 5, 3, 8, 4, 7, 6],
          borderColor: '#4361ee',
          backgroundColor: 'rgba(67, 97, 238, 0.1)',
          fill: true,
          tension: 0.4
        }]
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