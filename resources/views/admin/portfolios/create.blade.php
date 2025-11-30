<!DOCTYPE html>
<html lang="id" class="h-full scroll-smooth">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Tambah Portfolio - AdminPro</title>
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
        <a href="{{ route('admin.portfolios.index') }}" class="flex items-center px-6 py-3 text-gray-700 dark:text-gray-300 sidebar-active rounded-l-full mr-3">
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
            <h2 class="text-xl font-semibold">Tambah Portfolio</h2>
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
        <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-lg max-w-4xl mx-auto">

          <!-- SUCCESS MESSAGE -->
          @if(session('success'))
            <div class="bg-green-100 text-green-700 p-4 rounded-lg mb-6 flex items-center gap-2">
              <i class="fas fa-check-circle"></i> {{ session('success') }}
            </div>
          @endif

          <!-- FORM -->
          <form method="POST" action="{{ route('admin.portfolios.store') }}" enctype="multipart/form-data">
            @csrf

            <!-- Judul -->
            <div class="mb-5">
              <label class="block text-sm font-medium mb-2">Judul Portfolio</label>
              <input type="text" name="title" value="{{ old('title') }}" required placeholder="Website E-Commerce"
                class="w-full px-4 py-3 border rounded-lg @error('title') border-red-500 @enderror">
              @error('title')<span class="text-red-500 text-xs">{{ $message }}</span>@enderror
            </div>

            <!-- Deskripsi -->
            <div class="mb-5">
              <label class="block text-sm font-medium mb-2">Deskripsi</label>
              <textarea name="description" rows="6" required placeholder="Jelaskan proyek ini..."
                class="w-full px-4 py-3 border rounded-lg @error('description') border-red-500 @enderror">{{ old('description') }}</textarea>
              @error('description')<span class="text-red-500 text-xs">{{ $message }}</span>@enderror
            </div>

            <!-- Kategori -->
            <div class="mb-5">
              <label class="block text-sm font-medium mb-2">Kategori (opsional)</label>
              <input type="text" name="category" value="{{ old('category') }}" placeholder="Web, Mobile, Branding..."
                class="w-full px-4 py-3 border rounded-lg @error('category') border-red-500 @enderror">
              @error('category')<span class="text-red-500 text-xs">{{ $message }}</span>@enderror
            </div>

            <!-- Client -->
            <div class="mb-5">
              <label class="block text-sm font-medium mb-2">Client (opsional)</label>
              <input type="text" name="client" value="{{ old('client') }}" placeholder="Nama Client"
                class="w-full px-4 py-3 border rounded-lg @error('client') border-red-500 @enderror">
              @error('client')<span class="text-red-500 text-xs">{{ $message }}</span>@enderror
            </div>

            <!-- Tanggal -->
            <div class="mb-5">
              <label class="block text-sm font-medium mb-2">Tanggal Proyek (opsional)</label>
              <input type="text" name="date" value="{{ old('date') }}" placeholder="2025-11-30"
                class="w-full px-4 py-3 border rounded-lg @error('date') border-red-500 @enderror">
              @error('date')<span class="text-red-500 text-xs">{{ $message }}</span>@enderror
            </div>

            <!-- Durasi -->
            <div class="mb-5">
              <label class="block text-sm font-medium mb-2">Durasi (opsional)</label>
              <input type="text" name="duration" value="{{ old('duration') }}" placeholder="2 bulan, 3 minggu..."
                class="w-full px-4 py-3 border rounded-lg @error('duration') border-red-500 @enderror">
              @error('duration')<span class="text-red-500 text-xs">{{ $message }}</span>@enderror
            </div>

            <!-- Challenge -->
            <div class="mb-5">
              <label class="block text-sm font-medium mb-2">Challenge (opsional)</label>
              <textarea name="challenge" rows="3" placeholder="Tantangan proyek..."
                class="w-full px-4 py-3 border rounded-lg @error('challenge') border-red-500 @enderror">{{ old('challenge') }}</textarea>
              @error('challenge')<span class="text-red-500 text-xs">{{ $message }}</span>@enderror
            </div>

            <!-- Solution -->
            <div class="mb-5">
              <label class="block text-sm font-medium mb-2">Solution (opsional)</label>
              <textarea name="solution" rows="3" placeholder="Solusi proyek..."
                class="w-full px-4 py-3 border rounded-lg @error('solution') border-red-500 @enderror">{{ old('solution') }}</textarea>
              @error('solution')<span class="text-red-500 text-xs">{{ $message }}</span>@enderror
            </div>

            <!-- Results (array) -->
            <div class="mb-5">
              <label class="block text-sm font-medium mb-2">Results / Pencapaian (opsional)</label>
              <input type="text" name="results[]" value="{{ old('results.0') }}" placeholder="Contoh: Meningkatkan penjualan 20%"
                class="w-full px-4 py-3 border rounded-lg mb-2 @error('results') border-red-500 @enderror">
              <input type="text" name="results[]" value="{{ old('results.1') }}" placeholder="Contoh: 1000+ user terdaftar"
                class="w-full px-4 py-3 border rounded-lg mb-2">
              <p class="text-xs text-gray-500">Tambahkan lebih banyak di controller jika diperlukan</p>
              @error('results')<span class="text-red-500 text-xs">{{ $message }}</span>@enderror
            </div>

            <!-- Technologies (array) -->
            <div class="mb-5">
              <label class="block text-sm font-medium mb-2">Technologies (opsional)</label>
              <input type="text" name="technologies[]" value="{{ old('technologies.0') }}" placeholder="Laravel"
                class="w-full px-4 py-3 border rounded-lg mb-2 @error('technologies') border-red-500 @enderror">
              <input type="text" name="technologies[]" value="{{ old('technologies.1') }}" placeholder="TailwindCSS"
                class="w-full px-4 py-3 border rounded-lg mb-2">
              <p class="text-xs text-gray-500">Tambahkan lebih banyak di controller jika diperlukan</p>
              @error('technologies')<span class="text-red-500 text-xs">{{ $message }}</span>@enderror
            </div>

            <!-- Tautan -->
            <div class="mb-5">
              <label class="block text-sm font-medium mb-2">Tautan Proyek (opsional)</label>
              <input type="url" name="link" value="{{ old('link') }}" placeholder="https://contoh.com"
                class="w-full px-4 py-3 border rounded-lg @error('link') border-red-500 @enderror">
              @error('link')<span class="text-red-500 text-xs">{{ $message }}</span>@enderror
            </div>

            <!-- Gambar Utama -->
            <div class="mb-5">
              <label class="block text-sm font-medium mb-2">Gambar Utama (maks 2MB)</label>
              <input type="file" name="image" accept="image/*"
                class="w-full px-4 py-3 border rounded-lg @error('image') border-red-500 @enderror">
              @error('image')<span class="text-red-500 text-xs">{{ $message }}</span>@enderror
            </div>

            <!-- Gallery Images -->
            <div class="mb-5">
              <label class="block text-sm font-medium mb-2">Gallery Images (opsional, multiple)</label>
              <input type="file" name="images[]" accept="image/*" multiple
                class="w-full px-4 py-3 border rounded-lg @error('images') border-red-500 @enderror">
              <p class="text-xs text-gray-500">Upload beberapa gambar sekaligus, maksimal 2MB per file</p>
              @error('images')<span class="text-red-500 text-xs">{{ $message }}</span>@enderror
            </div>

            <!-- Tombol -->
            <div class="flex space-x-3">
              <button type="submit" class="bg-gradient-to-r from-primary to-secondary text-white font-semibold py-3 px-6 rounded-lg hover:shadow-lg flex items-center gap-2">
                <i class="fas fa-save"></i> SIMPAN PORTFOLIO
              </button>
              <a href="{{ route('admin.portfolios.index') }}" class="bg-gray-300 text-gray-700 font-semibold py-3 px-6 rounded-lg hover:bg-gray-400 flex items-center gap-2">
                <i class="fas fa-arrow-left"></i> BATAL
              </a>
            </div>
          </form>
        </div>
      </main>
    </div>
  </div>

  <!-- JS Sidebar & Dark Mode -->
  <script>
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
