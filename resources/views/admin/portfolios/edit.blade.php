<!DOCTYPE html>
<html lang="id" class="h-full scroll-smooth">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Edit Portfolio - AdminPro</title>
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
          fontFamily: { sans: ['Inter', 'sans-serif'] }
        }
      }
    }
  </script>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
  <style>
    .sidebar-active { @apply bg-gradient-to-r from-primary to-secondary text-white; }
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
        <a href="{{ route('admin.portfolios.index') }}" class="flex items-center px-6 py-3 text-gray-700 dark:text-gray-300 sidebar-hover sidebar-active rounded-l-full mr-3">
          <i class="fas fa-chart-line mr-3"></i> Portfolio
        </a>
        <a href="{{ route('admin.blogs.index') }}" class="flex items-center px-6 py-3 text-gray-700 dark:text-gray-300 sidebar-hover rounded-l-full mr-3">
          <i class="fas fa-cog mr-3"></i> Blog
        </a>
      </nav>
    </div>

    <div id="overlay" class="fixed inset-0 bg-black bg-opacity-50 z-40 hidden lg:hidden"></div>

    <!-- Main Content -->
    <div class="flex-1 flex flex-col overflow-hidden">
      <header class="bg-white dark:bg-gray-800 shadow-md">
        <div class="flex items-center justify-between px-4 py-3 lg:px-8">
          <div class="flex items-center space-x-4">
            <button id="open-sidebar" class="lg:hidden text-primary"><i class="fas fa-bars text-xl"></i></button>
            <h2 class="text-xl font-semibold">Edit Portfolio</h2>
          </div>
          <div class="flex items-center space-x-4">
            <button id="dark-toggle" class="p-2 rounded-full hover:bg-gray-100 dark:hover:bg-gray-700"><i class="fas fa-moon text-lg"></i></button>
            <div class="flex items-center space-x-3">
              <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::guard('admin')->user()->name) }}&background=4361ee&color=fff" class="w-10 h-10 rounded-full ring-2 ring-primary"/>
              <div class="hidden sm:block">
                <p class="text-sm font-medium">{{ Auth::guard('admin')->user()->name }}</p>
                <p class="text-xs text-gray-500 dark:text-gray-400">{{ Auth::guard('admin')->user()->email }}</p>
              </div>
            </div>
            <form method="POST" action="{{ route('admin.logout') }}">@csrf <button class="text-sm text-red-600 hover:underline">Logout</button></form>
          </div>
        </div>
      </header>

      <main class="flex-1 overflow-y-auto p-4 lg:p-8">
        <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-lg max-w-3xl mx-auto">
          <h3 class="text-lg font-semibold mb-6 flex items-center gap-2">
            <i class="fas fa-edit text-primary"></i> Form Edit Portfolio
          </h3>

          @if(session('success'))
            <div class="bg-green-100 text-green-700 p-4 rounded-lg mb-6 flex items-center gap-2">
              <i class="fas fa-check-circle"></i> {{ session('success') }}
            </div>
          @endif

          <form method="POST" action="{{ route('admin.portfolios.update', $portfolio) }}" enctype="multipart/form-data">
            @csrf @method('PUT')

            <!-- Judul -->
            <div class="mb-5">
              <label class="block text-sm font-medium mb-2 flex items-center gap-2">
                <i class="fas fa-heading text-primary"></i> Judul Portfolio
              </label>
              <input 
                type="text" 
                name="title" 
                value="{{ old('title', $portfolio->title) }}" 
                class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary dark:bg-gray-700 @error('title') border-red-500 @enderror" 
                required
                placeholder="Contoh: Website E-Commerce"
              >
              @error('title')
                <span class="text-red-500 text-xs mt-1 flex items-center gap-1">
                  <i class="fas fa-info-circle"></i> {{ $message }}
                </span>
              @enderror
            </div>

            <!-- Deskripsi -->
            <div class="mb-5">
              <label class="block text-sm font-medium mb-2 flex items-center gap-2">
                <i class="fas fa-align-left text-primary"></i> Deskripsi
              </label>
              <textarea 
                name="description" 
                rows="6" 
                class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary dark:bg-gray-700 @error('description') border-red-500 @enderror" 
                required
                placeholder="Jelaskan proyek ini..."
              >{{ old('description', $portfolio->description) }}</textarea>
              @error('description')
                <span class="text-red-500 text-xs mt-1 flex items-center gap-1">
                  <i class="fas fa-info-circle"></i> {{ $message }}
                </span>
              @enderror
            </div>

            <!-- Tautan Proyek -->
            <div class="mb-5">
              <label class="block text-sm font-medium mb-2 flex items-center gap-2">
                <i class="fas fa-link text-primary"></i> Tautan Proyek (opsional)
              </label>
              <input 
                type="url" 
                name="link" 
                value="{{ old('link', $portfolio->link) }}" 
                class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary dark:bg-gray-700 @error('link') border-red-500 @enderror" 
                placeholder="https://github.com/nama/proyek"
              >
              <p class="text-xs text-gray-500 mt-1">Contoh: https://contoh.com</p>
              @error('link')
                <span class="text-red-500 text-xs mt-1 flex items-center gap-1">
                  <i class="fas fa-info-circle"></i> {{ $message }}
                </span>
              @enderror
            </div>

            <!-- Gambar Saat Ini -->
            <div class="mb-6">
              <label class="block text-sm font-medium mb-2 flex items-center gap-2">
                <i class="fas fa-image text-primary"></i> Gambar Portfolio
              </label>
              @if($portfolio->image)
                <div class="mb-3">
                  <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">Gambar saat ini:</p>
                  <img src="{{ Storage::url($portfolio->image) }}" alt="Portfolio" class="w-48 h-48 object-cover rounded-lg shadow">
                </div>
                <p class="text-xs text-gray-500 mb-2">Ganti gambar (opsional):</p>
              @else
                <p class="text-sm text-gray-500 mb-2">Belum ada gambar.</p>
              @endif
              <input 
                type="file" 
                name="image" 
                accept="image/*" 
                class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-primary file:text-white hover:file:bg-secondary @error('image') border-red-500 @enderror"
              >
              @error('image')
                <span class="text-red-500 text-xs mt-1 flex items-center gap-1">
                  <i class="fas fa-info-circle"></i> {{ $message }}
                </span>
              @enderror
              <p class="text-xs text-gray-500 mt-2">Maksimal 2MB. Format: JPG, PNG, GIF.</p>
            </div>

            <!-- Tombol -->
            <div class="flex space-x-3">
              <button type="submit" class="bg-gradient-to-r from-primary to-secondary text-white font-semibold py-3 px-6 rounded-lg hover:shadow-lg transition-shadow flex items-center gap-2">
                <i class="fas fa-save"></i> UPDATE PORTFOLIO
              </button>
              <a href="{{ route('admin.portfolios.index') }}" class="bg-gray-300 text-gray-700 font-semibold py-3 px-6 rounded-lg hover:bg-gray-400 transition-colors flex items-center gap-2">
                <i class="fas fa-arrow-left"></i> BATAL
              </a>
            </div>
          </form>
        </div>
      </main>
    </div>
  </div>

  <!-- JavaScript -->
  <script>
    // Sidebar & Dark Mode
    const sidebar = document.getElementById('sidebar');
    const openBtn = document.getElementById('open-sidebar');
    const closeBtn = document.getElementById('close-sidebar');
    const overlay = document.getElementById('overlay');
    openBtn?.addEventListener('click', () => { sidebar.classList.remove('-translate-x-full'); overlay.classList.remove('hidden'); });
    closeBtn?.addEventListener('click', () => { sidebar.classList.add('-translate-x-full'); overlay.classList.add('hidden'); });
    overlay?.addEventListener('click', () => { sidebar.classList.add('-translate-x-full'); overlay.classList.add('hidden'); });

    const darkToggle = document.getElementById('dark-toggle');
    const html = document.documentElement;
    darkToggle?.addEventListener('click', () => {
      html.classList.toggle('dark');
      const isDark = html.classList.contains('dark');
      darkToggle.innerHTML = isDark ? '<i class="fas fa-sun text-lg"></i>' : '<i class="fas fa-moon text-lg"></i>';
      localStorage.setItem('darkMode', isDark);
    });
    if (localStorage.getItem('darkMode') === 'true' || (!localStorage.getItem('darkMode') && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
      html.classList.add('dark'); darkToggle.innerHTML = '<i class="fas fa-sun text-lg"></i>';
    }
  </script>
</body>
</html>