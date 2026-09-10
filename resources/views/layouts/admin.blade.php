<!DOCTYPE html>
<html lang="id" class="h-full bg-[#F8FAFC]">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>@yield('title', 'Admin Panel') - ARTDEVATA</title>

  <!-- Google Fonts: Inter -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  
  <!-- Font Awesome Icons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />

  <!-- Chart.js CDN -->
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

  <!-- Tailwind CSS CDN Fallback & Config -->
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      theme: {
        extend: {
          colors: {
            art: {
              primary: '#14433B',
              secondary: '#0E5D55',
              accent: '#21C9A4',
              dark: '#0B443C',
              bg: '#F8FAFC',
              card: '#FFFFFF',
              text: '#0F172A',
              sub: '#64748B',
              border: '#E2E8F0',
              success: '#16A34A',
              warning: '#EAB308',
              danger: '#DC2626'
            }
          },
          fontFamily: {
            sans: ['Inter', 'sans-serif'],
          },
          boxShadow: {
            'soft': '0 1px 3px 0 rgba(0, 0, 0, 0.05), 0 1px 2px -1px rgba(0, 0, 0, 0.05)',
            'card': '0 4px 20px -2px rgba(15, 23, 42, 0.05)',
            'dropdown': '0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.05)',
          }
        }
      }
    }
  </script>

  @vite(['resources/css/app.css', 'resources/js/app.js'])

  <style>
    body { font-family: 'Inter', sans-serif; }
    .nav-item-active {
      background-color: rgba(33, 201, 164, 0.12);
      color: #21C9A4;
      border-right: 3px solid #21C9A4;
      font-weight: 600;
    }
    .nav-item-hover:hover {
      background-color: rgba(255, 255, 255, 0.06);
      color: #FFFFFF;
    }
    /* Custom Scrollbar */
    ::-webkit-scrollbar { width: 5px; height: 5px; }
    ::-webkit-scrollbar-track { background: #0B443C; }
    ::-webkit-scrollbar-thumb { background: #0E5D55; border-radius: 4px; }
    ::-webkit-scrollbar-thumb:hover { background: #21C9A4; }
  </style>

  @stack('styles')
</head>
<body class="h-full bg-[#F8FAFC] text-slate-800 font-sans antialiased selection:bg-[#21C9A4]/20 selection:text-[#14433B]">

  <div class="h-screen flex overflow-hidden bg-[#F8FAFC]">

    <!-- Sidebar Backdrop (Mobile) -->
    <div id="sidebar-backdrop" onclick="toggleSidebar()" class="fixed inset-0 bg-slate-900/50 backdrop-blur-xs z-40 lg:hidden hidden transition-opacity duration-300 opacity-0"></div>

    <!-- Sidebar Component -->
    <aside id="sidebar" class="fixed inset-y-0 left-0 z-50 w-64 h-full bg-[#14433B] text-slate-300 flex flex-col transition-all duration-300 ease-in-out lg:sticky lg:top-0 lg:h-screen lg:translate-x-0 -translate-x-full shadow-xl shrink-0">
      
      <!-- Brand Logo -->
      <div class="h-16 flex items-center justify-between px-6 border-b border-[#0E5D55]/60">
        <a href="{{ route('admin.panel') }}" class="flex items-center space-x-3 group">
          <div class="w-9 h-9 rounded-xl bg-gradient-to-tr from-[#21C9A4] to-[#0E5D55] flex items-center justify-center text-white font-bold text-lg shadow-md group-hover:scale-105 transition-transform duration-200">
            A
          </div>
          <div class="flex flex-col">
            <span class="font-bold text-white tracking-wide text-base leading-tight">ARTDEVATA</span>
            <span class="text-[10px] text-[#21C9A4] tracking-widest font-semibold uppercase">Admin SaaS</span>
          </div>
        </a>
        <button onclick="toggleSidebar()" class="lg:hidden text-slate-400 hover:text-white focus:outline-hidden p-1">
          <i class="fa-solid fa-xmark text-lg"></i>
        </button>
      </div>

      <!-- Navigation Section -->
      <div class="flex-1 overflow-y-auto py-4 px-3 space-y-6">
        
        <!-- Main Nav Group -->
        <div>
          <div class="px-3 mb-2 text-[11px] font-bold tracking-wider text-[#21C9A4] uppercase">Menu Utama</div>
          <nav class="space-y-1">
            
            <!-- Dashboard -->
            <a href="{{ route('admin.panel') }}" class="flex items-center px-3.5 py-2.5 rounded-xl text-sm font-medium transition-all duration-150 {{ request()->routeIs('admin.panel') ? 'nav-item-active' : 'text-slate-200 nav-item-hover' }}">
              <i class="fa-solid fa-chart-pie w-5 mr-3 text-center {{ request()->routeIs('admin.panel') ? 'text-[#21C9A4]' : 'text-emerald-200/70' }}"></i>
              <span>Dashboard</span>
            </a>

            @if(Auth::guard('admin')->user()?->hasPermission('projects.view'))
              <!-- Projects -->
              <a href="{{ route('admin.projects.index') }}" class="flex items-center px-3.5 py-2.5 rounded-xl text-sm font-medium transition-all duration-150 {{ request()->routeIs('admin.projects.*') ? 'nav-item-active' : 'text-slate-200 nav-item-hover' }}">
                <i class="fa-solid fa-diagram-project w-5 mr-3 text-center {{ request()->routeIs('admin.projects.*') ? 'text-[#21C9A4]' : 'text-emerald-200/70' }}"></i>
                <span>Proyek</span>
              </a>
            @endif

            @if(Auth::guard('admin')->user()?->hasPermission('services.view'))
              <!-- Services -->
              <a href="{{ route('admin.services.index') }}" class="flex items-center px-3.5 py-2.5 rounded-xl text-sm font-medium transition-all duration-150 {{ request()->routeIs('admin.services.*') ? 'nav-item-active' : 'text-slate-200 nav-item-hover' }}">
                <i class="fa-solid fa-cubes w-5 mr-3 text-center {{ request()->routeIs('admin.services.*') ? 'text-[#21C9A4]' : 'text-emerald-200/70' }}"></i>
                <span>Layanan</span>
              </a>
            @endif

            @if(Auth::guard('admin')->user()?->hasPermission('portfolios.view'))
              <!-- Portfolios -->
              <a href="{{ route('admin.portfolios.index') }}" class="flex items-center px-3.5 py-2.5 rounded-xl text-sm font-medium transition-all duration-150 {{ request()->routeIs('admin.portfolios.*') ? 'nav-item-active' : 'text-slate-200 nav-item-hover' }}">
                <i class="fa-solid fa-briefcase w-5 mr-3 text-center {{ request()->routeIs('admin.portfolios.*') ? 'text-[#21C9A4]' : 'text-emerald-200/70' }}"></i>
                <span>Portfolio</span>
              </a>
            @endif

            @if(Auth::guard('admin')->user()?->hasPermission('blogs.view'))
              <!-- Blogs -->
              <a href="{{ route('admin.blogs.index') }}" class="flex items-center px-3.5 py-2.5 rounded-xl text-sm font-medium transition-all duration-150 {{ request()->routeIs('admin.blogs.*') ? 'nav-item-active' : 'text-slate-200 nav-item-hover' }}">
                <i class="fa-solid fa-newspaper w-5 mr-3 text-center {{ request()->routeIs('admin.blogs.*') ? 'text-[#21C9A4]' : 'text-emerald-200/70' }}"></i>
                <span>Blog & Berita</span>
              </a>
            @endif

            @if(Auth::guard('admin')->user()?->hasPermission('clients.view'))
              <!-- Clients -->
              <a href="{{ route('admin.clients.index') }}" class="flex items-center px-3.5 py-2.5 rounded-xl text-sm font-medium transition-all duration-150 {{ request()->routeIs('admin.clients.*') ? 'nav-item-active' : 'text-slate-200 nav-item-hover' }}">
                <i class="fa-solid fa-users-rectangle w-5 mr-3 text-center {{ request()->routeIs('admin.clients.*') ? 'text-[#21C9A4]' : 'text-emerald-200/70' }}"></i>
                <span>Klien</span>
              </a>
            @endif

            @if(Auth::guard('admin')->user()?->hasPermission('documentations.view'))
              <!-- Documentations -->
              <a href="{{ route('admin.documentations.index') }}" class="flex items-center px-3.5 py-2.5 rounded-xl text-sm font-medium transition-all duration-150 {{ request()->routeIs('admin.documentations.*') ? 'nav-item-active' : 'text-slate-200 nav-item-hover' }}">
                <i class="fa-solid fa-camera-retro w-5 mr-3 text-center {{ request()->routeIs('admin.documentations.*') ? 'text-[#21C9A4]' : 'text-emerald-200/70' }}"></i>
                <span>Dokumentasi</span>
              </a>
            @endif

          </nav>
        </div>

        <!-- Financial Management Group -->
        <div>
          <div class="px-3 mb-2 text-[11px] font-bold tracking-wider text-[#21C9A4] uppercase">Keuangan & Bisnis</div>
          <nav class="space-y-1">
            
            @if(Auth::guard('admin')->user()?->hasPermission('invoices.view'))
              <!-- Invoices -->
              <a href="{{ route('admin.invoices.index') }}" class="flex items-center px-3.5 py-2.5 rounded-xl text-sm font-medium transition-all duration-150 {{ request()->routeIs('admin.invoices.*') ? 'nav-item-active' : 'text-slate-200 nav-item-hover' }}">
                <i class="fa-solid fa-file-invoice-dollar w-5 mr-3 text-center {{ request()->routeIs('admin.invoices.*') ? 'text-[#21C9A4]' : 'text-emerald-200/70' }}"></i>
                <span>Invoices</span>
              </a>
            @endif

            @if(Auth::guard('admin')->user()?->hasPermission('finance.view'))
              <!-- Transactions -->
              <a href="{{ route('admin.finance.transactions.index') }}" class="flex items-center px-3.5 py-2.5 rounded-xl text-sm font-medium transition-all duration-150 {{ request()->routeIs('admin.finance.transactions.*') ? 'nav-item-active' : 'text-slate-200 nav-item-hover' }}">
                <i class="fa-solid fa-wallet w-5 mr-3 text-center {{ request()->routeIs('admin.finance.transactions.*') ? 'text-[#21C9A4]' : 'text-emerald-200/70' }}"></i>
                <span>Transaksi</span>
              </a>
            @endif

            @if(Auth::guard('admin')->user()?->hasPermission('salaries.view'))
              <!-- Salaries -->
              <a href="{{ route('admin.salaries.index') }}" class="flex items-center px-3.5 py-2.5 rounded-xl text-sm font-medium transition-all duration-150 {{ request()->routeIs('admin.salaries.*') ? 'nav-item-active' : 'text-slate-200 nav-item-hover' }}">
                <i class="fa-solid fa-money-bill-wave w-5 mr-3 text-center {{ request()->routeIs('admin.salaries.*') ? 'text-[#21C9A4]' : 'text-emerald-200/70' }}"></i>
                <span>Gaji & Payroll</span>
              </a>
            @endif

          </nav>
        </div>

        <!-- System & Security Group -->
        <div>
          <div class="px-3 mb-2 text-[11px] font-bold tracking-wider text-[#21C9A4] uppercase">Tim & Keamanan</div>
          <nav class="space-y-1">
            
            @if(Auth::guard('admin')->user()?->hasPermission('admins.view'))
              <!-- User Admin Management -->
              <a href="{{ route('admin.users.index') }}" class="flex items-center px-3.5 py-2.5 rounded-xl text-sm font-medium transition-all duration-150 {{ request()->routeIs('admin.users.*') ? 'nav-item-active' : 'text-slate-200 nav-item-hover' }}">
                <i class="fa-solid fa-users-gear w-5 mr-3 text-center {{ request()->routeIs('admin.users.*') ? 'text-[#21C9A4]' : 'text-emerald-200/70' }}"></i>
                <span>Kelola Admin</span>
              </a>
            @endif

            @if(Auth::guard('admin')->user()?->hasPermission('roles.manage'))
              <!-- Roles & Permissions -->
              <a href="{{ route('admin.roles.index') }}" class="flex items-center px-3.5 py-2.5 rounded-xl text-sm font-medium transition-all duration-150 {{ request()->routeIs('admin.roles.*') ? 'nav-item-active' : 'text-slate-200 nav-item-hover' }}">
                <i class="fa-solid fa-user-shield w-5 mr-3 text-center {{ request()->routeIs('admin.roles.*') ? 'text-[#21C9A4]' : 'text-emerald-200/70' }}"></i>
                <span>Peran & Hak Akses</span>
              </a>
            @endif

            @if(Auth::guard('admin')->user()?->hasPermission('security.view'))
              <!-- Security Dashboard -->
              <a href="{{ route('admin.security.index') }}" class="flex items-center px-3.5 py-2.5 rounded-xl text-sm font-medium transition-all duration-150 {{ request()->routeIs('admin.security.*') ? 'nav-item-active' : 'text-slate-200 nav-item-hover' }}">
                <i class="fa-solid fa-shield-halved w-5 mr-3 text-center {{ request()->routeIs('admin.security.*') ? 'text-[#21C9A4]' : 'text-emerald-200/70' }}"></i>
                <span>Security Overview</span>
              </a>
            @endif

            @if(Auth::guard('admin')->user()?->hasPermission('activity_logs.view'))
              <!-- Activity Logs -->
              <a href="{{ route('admin.activity-logs.index') }}" class="flex items-center px-3.5 py-2.5 rounded-xl text-sm font-medium transition-all duration-150 {{ request()->routeIs('admin.activity-logs.*') ? 'nav-item-active' : 'text-slate-200 nav-item-hover' }}">
                <i class="fa-solid fa-clock-rotate-left w-5 mr-3 text-center {{ request()->routeIs('admin.activity-logs.*') ? 'text-[#21C9A4]' : 'text-emerald-200/70' }}"></i>
                <span>Audit Logs</span>
              </a>
            @endif

            @if(Auth::guard('admin')->user()?->hasPermission('login_history.view'))
              <!-- Login History -->
              <a href="{{ route('admin.login-histories.index') }}" class="flex items-center px-3.5 py-2.5 rounded-xl text-sm font-medium transition-all duration-150 {{ request()->routeIs('admin.login-histories.*') ? 'nav-item-active' : 'text-slate-200 nav-item-hover' }}">
                <i class="fa-solid fa-key w-5 mr-3 text-center {{ request()->routeIs('admin.login-histories.*') ? 'text-[#21C9A4]' : 'text-emerald-200/70' }}"></i>
                <span>Riwayat Login</span>
              </a>
            @endif

            <!-- My Account Security -->
            <a href="{{ route('admin.account.security') }}" class="flex items-center px-3.5 py-2.5 rounded-xl text-sm font-medium transition-all duration-150 {{ request()->routeIs('admin.account.security') ? 'nav-item-active' : 'text-slate-200 nav-item-hover' }}">
              <i class="fa-solid fa-lock w-5 mr-3 text-center {{ request()->routeIs('admin.account.security') ? 'text-[#21C9A4]' : 'text-emerald-200/70' }}"></i>
              <span>Keamanan Akun Saya</span>
            </a>

          </nav>
        </div>

      </div>

      <!-- User Admin Profile Footer -->
      <div class="p-3 border-t border-[#0E5D55]/60 bg-[#0B443C]/50">
        <div class="flex items-center justify-between p-2 rounded-xl">
          <div class="flex items-center space-x-3 overflow-hidden">
            <div class="w-9 h-9 rounded-full bg-[#21C9A4] text-[#14433B] font-bold flex items-center justify-center shrink-0">
              {{ strtoupper(substr(Auth::guard('admin')->user()->name ?? 'A', 0, 1)) }}
            </div>
            <div class="flex flex-col truncate">
              <span class="text-xs font-semibold text-white truncate">{{ Auth::guard('admin')->user()->name ?? 'Administrator' }}</span>
              <span class="text-[10px] text-emerald-200/80 truncate">{{ Auth::guard('admin')->user()->email ?? 'admin@artdevata.com' }}</span>
            </div>
          </div>
          <form method="POST" action="{{ route('admin.logout') }}" class="inline">
            @csrf
            <button type="submit" title="Logout" class="p-2 text-emerald-200/80 hover:text-rose-300 transition-colors duration-150 rounded-lg hover:bg-white/10">
              <i class="fa-solid fa-arrow-right-from-bracket"></i>
            </button>
          </form>
        </div>
      </div>

    </aside>

    <!-- Content Area Container -->
    <div class="flex-1 flex flex-col min-w-0 h-screen overflow-hidden">
      
      <!-- Topbar Header -->
      <header class="h-16 bg-white border-b border-slate-200 flex items-center justify-between px-4 lg:px-8 shrink-0 shadow-xs z-30">
        
        <div class="flex items-center space-x-4">
          <!-- Mobile Sidebar Toggle -->
          <button onclick="toggleSidebar()" class="p-2 text-slate-700 hover:text-[#14433B] hover:bg-slate-100 rounded-xl transition-colors focus:outline-hidden">
            <i class="fa-solid fa-bars-staggered text-lg"></i>
          </button>

          <!-- Breadcrumb -->
          <nav class="hidden sm:flex items-center space-x-2 text-xs text-slate-600">
            <a href="{{ route('admin.panel') }}" class="hover:text-[#14433B] transition-colors"><i class="fa-solid fa-house"></i></a>
            <span>/</span>
            <span class="font-bold text-slate-900">@yield('title', 'Dashboard')</span>
          </nav>
        </div>

        <!-- Right Action Controls -->
        <div class="flex items-center space-x-3">
          
          <!-- System Status Badge -->
          <div class="hidden md:flex items-center space-x-2 px-3 py-1.5 rounded-full bg-emerald-100/70 border border-emerald-300/80 text-emerald-900 text-xs font-semibold">
            <span class="w-2 h-2 rounded-full bg-emerald-600 animate-pulse"></span>
            <span>SaaS Online</span>
          </div>

          <!-- Notification Bell Dropdown Button -->
          <div class="relative" x-data="{ open: false }">
            <button @click="open = !open" class="relative p-2 text-slate-600 hover:text-[#14433B] hover:bg-slate-100 rounded-xl transition-colors focus:outline-hidden">
              <i class="fa-regular fa-bell text-lg"></i>
              <span class="absolute top-1.5 right-1.5 w-2 h-2 rounded-full bg-[#14433B]"></span>
            </button>
          </div>

          <div class="h-6 w-px bg-slate-200"></div>

          <!-- Admin User Profile Dropdown -->
          <div class="relative" x-data="{ open: false }">
            <button @click="open = !open" class="flex items-center space-x-2.5 p-1.5 rounded-xl hover:bg-slate-100 transition-colors focus:outline-hidden">
              <div class="w-8 h-8 rounded-full bg-[#14433B] text-white font-bold text-xs flex items-center justify-center shadow-xs">
                {{ strtoupper(substr(Auth::guard('admin')->user()->name ?? 'A', 0, 1)) }}
              </div>
              <span class="hidden md:inline text-xs font-bold text-slate-800">{{ Auth::guard('admin')->user()->name ?? 'Admin' }}</span>
              <i class="fa-solid fa-chevron-down text-[10px] text-slate-500"></i>
            </button>

            <!-- Dropdown Menu -->
            <div x-show="open" @click.away="open = false" x-transition:enter="transition ease-out duration-100" x-transition:enter-start="transform opacity-0 scale-95" x-transition:enter-end="transform opacity-100 scale-100" class="absolute right-0 mt-2 w-48 bg-white rounded-xl shadow-dropdown border border-slate-100 py-1.5 z-50 divide-y divide-slate-100" style="display: none;">
              <div class="px-4 py-2">
                <p class="text-xs font-semibold text-slate-800">{{ Auth::guard('admin')->user()->name ?? 'Admin' }}</p>
                <p class="text-[11px] text-slate-500 truncate">{{ Auth::guard('admin')->user()->email ?? 'admin@artdevata.com' }}</p>
              </div>
              <div class="py-1">
                <a href="{{ route('admin.account.security') }}" class="w-full text-left px-4 py-2 text-xs text-slate-700 hover:bg-slate-50 flex items-center space-x-2 transition-colors">
                  <i class="fa-solid fa-lock text-[#14433B]"></i>
                  <span>Keamanan Akun</span>
                </a>
                <form method="POST" action="{{ route('admin.logout') }}">
                  @csrf
                  <button type="submit" class="w-full text-left px-4 py-2 text-xs text-rose-600 hover:bg-rose-50 flex items-center space-x-2 transition-colors">
                    <i class="fa-solid fa-arrow-right-from-bracket"></i>
                    <span>Keluar / Logout</span>
                  </button>
                </form>
              </div>
            </div>

          </div>

        </div>

      </header>

      <!-- Main Body Scrollable View Area -->
      <main class="flex-1 overflow-y-auto p-4 md:p-6 lg:p-8 space-y-6">
        
        <!-- Flash Toast Messages -->
        @if(session('success'))
          <div class="toast-alert flex items-center justify-between p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 shadow-sm transition-all duration-300">
            <div class="flex items-center space-x-3">
              <div class="w-8 h-8 rounded-lg bg-emerald-500 text-white flex items-center justify-center shrink-0">
                <i class="fa-solid fa-check text-sm"></i>
              </div>
              <span class="text-xs font-medium">{{ session('success') }}</span>
            </div>
            <button onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700 p-1"><i class="fa-solid fa-xmark"></i></button>
          </div>
        @endif

        @if(session('error'))
          <div class="toast-alert flex items-center justify-between p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 shadow-sm transition-all duration-300">
            <div class="flex items-center space-x-3">
              <div class="w-8 h-8 rounded-lg bg-rose-500 text-white flex items-center justify-center shrink-0">
                <i class="fa-solid fa-triangle-exclamation text-sm"></i>
              </div>
              <span class="text-xs font-medium">{{ session('error') }}</span>
            </div>
            <button onclick="this.parentElement.remove()" class="text-rose-500 hover:text-rose-700 p-1"><i class="fa-solid fa-xmark"></i></button>
          </div>
        @endif

        @if($errors->any())
          <div class="toast-alert p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 shadow-sm transition-all duration-300">
            <div class="flex items-start space-x-3">
              <div class="w-8 h-8 rounded-lg bg-rose-500 text-white flex items-center justify-center shrink-0 mt-0.5">
                <i class="fa-solid fa-triangle-exclamation text-sm"></i>
              </div>
              <div class="flex-1">
                <span class="text-xs font-semibold block mb-1">Terdapat kesalahan input:</span>
                <ul class="list-disc list-inside text-xs space-y-0.5 text-rose-700">
                  @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                  @endforeach
                </ul>
              </div>
              <button onclick="this.parentElement.parentElement.remove()" class="text-rose-500 hover:text-rose-700 p-1"><i class="fa-solid fa-xmark"></i></button>
            </div>
          </div>
        @endif

        <!-- Content Placeholder -->
        @yield('content')

      </main>

      <!-- Minimalist Footer -->
      <footer class="h-12 border-t border-slate-200 bg-white px-6 flex items-center justify-between text-[11px] text-slate-500 shrink-0">
        <div>&copy; {{ date('Y') }} <span class="font-semibold text-[#14433B]">ARTDEVATA</span>. All rights reserved.</div>
        <div class="flex items-center space-x-4">
          <span class="text-emerald-600 font-medium">v2.0 SaaS</span>
        </div>
      </footer>

    </div>

  </div>

  <!-- Global Delete Confirmation Modal -->
  <div id="delete-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-900/50 backdrop-blur-xs p-4">
    <div class="bg-white rounded-2xl max-w-sm w-full p-6 shadow-2xl border border-slate-100 transform transition-all scale-95 duration-200">
      <div class="w-12 h-12 rounded-full bg-rose-100 text-rose-600 flex items-center justify-center mx-auto mb-4">
        <i class="fa-solid fa-trash-can text-xl"></i>
      </div>
      <h3 class="text-center text-base font-bold text-slate-800 mb-1">Konfirmasi Hapus</h3>
      <p id="delete-modal-message" class="text-center text-xs text-slate-500 mb-6">Apakah Anda yakin ingin menghapus data ini? Tindakan ini tidak dapat dibatalkan.</p>
      
      <form id="delete-modal-form" method="POST" action="">
        @csrf
        @method('DELETE')
        <div class="flex items-center justify-center space-x-3">
          <button type="button" onclick="closeDeleteModal()" class="px-4 py-2.5 rounded-xl border border-slate-200 text-slate-700 text-xs font-semibold hover:bg-slate-50 transition-colors w-full">Batal</button>
          <button type="submit" class="px-4 py-2.5 rounded-xl bg-rose-600 hover:bg-rose-700 text-white text-xs font-semibold shadow-sm transition-colors w-full">Hapus Data</button>
        </div>
      </form>
    </div>
  </div>

  <!-- Alpine.js for lightweight dropdown state -->
  <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

  <script>
    // Toggle Mobile Sidebar
    function toggleSidebar() {
      const sidebar = document.getElementById('sidebar');
      const backdrop = document.getElementById('sidebar-backdrop');
      
      sidebar.classList.toggle('-translate-x-full');
      if (backdrop.classList.contains('hidden')) {
        backdrop.classList.remove('hidden');
        setTimeout(() => backdrop.classList.remove('opacity-0'), 10);
      } else {
        backdrop.classList.add('opacity-0');
        setTimeout(() => backdrop.classList.add('hidden'), 300);
      }
    }

    // Delete Modal Trigger
    function confirmDelete(url, message = 'Apakah Anda yakin ingin menghapus data ini?') {
      const modal = document.getElementById('delete-modal');
      const form = document.getElementById('delete-modal-form');
      const msg = document.getElementById('delete-modal-message');

      form.action = url;
      msg.textContent = message;
      modal.classList.remove('hidden');
      modal.classList.add('flex');
    }

    function closeDeleteModal() {
      const modal = document.getElementById('delete-modal');
      modal.classList.add('hidden');
      modal.classList.remove('flex');
    }

    // Auto dismiss toasts after 5 seconds
    document.addEventListener('DOMContentLoaded', () => {
      setTimeout(() => {
        document.querySelectorAll('.toast-alert').forEach(toast => {
          toast.style.opacity = '0';
          setTimeout(() => toast.remove(), 300);
        });
      }, 5000);
    });
  </script>

  @stack('scripts')
</body>
</html>
