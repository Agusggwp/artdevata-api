<!DOCTYPE html>
<html lang="id" class="h-full scroll-smooth">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Tambah Layanan - AdminPro</title>
  <script src="https://cdn.tailwindcss.com"></script>

  <script>
    tailwind.config = {
      darkMode: 'class',
      theme: {
        extend: {
          colors: { primary: '#4361ee', secondary: '#3f37c9' }
        }
      }
    }
  </script>

  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>

  <style> body { font-family: 'Inter', sans-serif; } </style>
</head>

<body class="h-full bg-gray-50 dark:bg-gray-900 text-gray-800 dark:text-gray-100">

<div class="flex h-screen overflow-hidden">


  <!-- SIDEBAR -->
  <div id="sidebar" class="fixed inset-y-0 left-0 z-50 w-64 bg-white dark:bg-gray-800 shadow-2xl transform -translate-x-full lg:translate-x-0 lg:static lg:inset-0 transition-transform duration-300 ease-in-out">
    <div class="flex items-center justify-between p-6 border-b dark:border-gray-700">
      <h1 class="text-2xl font-bold bg-gradient-to-r from-primary to-secondary bg-clip-text text-transparent">
        ARTDEVATA
      </h1>
      <button id="close-sidebar" class="lg:hidden text-gray-500 hover:text-primary">
        <i class="fas fa-times text-xl"></i>
      </button>
    </div>

    <nav class="mt-6">
      <a href="{{ route('admin.panel') }}" class="flex items-center px-6 py-3 sidebar-hover rounded-l-full mr-3">
        <i class="fas fa-tachometer-alt mr-3"></i> Dashboard
      </a>

      <a href="{{ route('admin.services.index') }}" class="flex items-center px-6 py-3 sidebar-hover rounded-l-full mr-3 bg-primary text-white">
        <i class="fas fa-box mr-3"></i> Layanan
      </a>

      <a href="{{ route('admin.portfolios.index') }}" class="flex items-center px-6 py-3 sidebar-hover rounded-l-full mr-3">
        <i class="fas fa-chart-line mr-3"></i> Portfolio
      </a>

      <a href="{{ route('admin.blogs.index') }}" class="flex items-center px-6 py-3 sidebar-hover rounded-l-full mr-3">
        <i class="fas fa-cog mr-3"></i> Blog
      </a>
    </nav>
  </div>


  <div id="overlay" class="fixed inset-0 bg-black bg-opacity-50 z-40 hidden lg:hidden"></div>


  <!-- MAIN -->
  <div class="flex-1 flex flex-col overflow-hidden">

    <!-- HEADER -->
    <header class="bg-white dark:bg-gray-800 shadow-md">
      <div class="flex items-center justify-between px-4 py-3 lg:px-8">
        <div class="flex items-center space-x-4">
          <button id="open-sidebar" class="lg:hidden text-primary">
            <i class="fas fa-bars text-xl"></i>
          </button>
          <h2 class="text-xl font-semibold">Tambah Layanan</h2>
        </div>

        <div class="flex items-center space-x-4">
          <button id="dark-toggle" class="p-2 rounded-full hover:bg-gray-100 dark:hover:bg-gray-700">
            <i class="fas fa-moon text-lg"></i>
          </button>

          <div class="flex items-center space-x-3">
            <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::guard('admin')->user()->name) }}&background=4361ee&color=fff"
                 class="w-10 h-10 rounded-full ring-2 ring-primary"/>

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


    <!-- CONTENT -->
    <main class="flex-1 overflow-y-auto p-4 lg:p-8">
      <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-lg max-w-3xl mx-auto">
        <h3 class="text-lg font-semibold mb-6">Form Tambah Layanan</h3>

        <form method="POST" action="{{ route('admin.services.store') }}" enctype="multipart/form-data">
          @csrf

          <!-- JUDUL -->
          <div class="mb-5">
            <label class="block text-sm font-medium mb-2">Judul Layanan</label>
            <input type="text" name="title"
                   class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700"
                   required>
          </div>

          <!-- DESKRIPSI -->
          <div class="mb-5">
            <label class="block text-sm font-medium mb-2">Deskripsi</label>
            <textarea name="description" rows="5"
                      class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700"
                      required></textarea>
          </div>

          <!-- FITUR (DINAMIS) -->
          <div class="mb-5">
            <label class="block text-sm font-medium mb-2">Fitur Layanan</label>

            <div id="feature-list" class="space-y-3"></div>

            <button type="button" id="add-feature"
                    class="mt-3 bg-primary text-white px-3 py-2 rounded-lg hover:bg-secondary">
              + Tambah Fitur
            </button>
          </div>

          <!-- GAMBAR -->
          <div class="mb-6">
            <label class="block text-sm font-medium mb-2">Gambar (Opsional)</label>
            <input type="file" name="image" accept="image/*"
                   class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg file:bg-primary file:text-white file:rounded-full">
          </div>

          <!-- BUTTONS -->
          <div class="flex space-x-3">
            <button type="submit"
                    class="bg-gradient-to-r from-primary to-secondary text-white font-semibold py-3 px-6 rounded-lg hover:shadow-lg">
              SIMPAN
            </button>

            <a href="{{ route('admin.services.index') }}"
               class="bg-gray-300 text-gray-700 font-semibold py-3 px-6 rounded-lg hover:bg-gray-400">
              BATAL
            </a>
          </div>

        </form>
      </div>
    </main>
  </div>
</div>


<!-- SCRIPT FITUR -->
<script>
  const list = document.getElementById("feature-list");
  const btnAdd = document.getElementById("add-feature");

  btnAdd.addEventListener("click", () => {
    const div = document.createElement("div");
    div.className = "flex space-x-3";

    div.innerHTML = `
        <input type="text" name="features[]" placeholder="Contoh: Desain Modern"
               class="w-full px-4 py-2 border rounded-lg dark:bg-gray-700">
        <button type="button" class="remove-feature bg-red-500 text-white px-3 rounded-lg">X</button>
    `;

    list.appendChild(div);

    div.querySelector(".remove-feature").addEventListener("click", () => div.remove());
  });
</script>


<!-- SIDEBAR + DARKMODE -->
<script>
  const sidebar = document.getElementById('sidebar');
  const openBtn = document.getElementById('open-sidebar');
  const closeBtn = document.getElementById('close-sidebar');
  const overlay = document.getElementById('overlay');

  openBtn?.addEventListener('click', () => {
    sidebar.classList.remove('-translate-x-full');
    overlay.classList.remove('hidden');
  });

  closeBtn?.addEventListener('click', () => {
    sidebar.classList.add('-translate-x-full');
    overlay.classList.add('hidden');
  });

  const darkToggle = document.getElementById('dark-toggle');
  const html = document.documentElement;

  darkToggle?.addEventListener('click', () => {
    html.classList.toggle('dark');
    const isDark = html.classList.contains('dark');
    darkToggle.innerHTML = isDark
      ? '<i class="fas fa-sun text-lg"></i>'
      : '<i class="fas fa-moon text-lg"></i>';
    localStorage.setItem('darkMode', isDark);
  });

  if (localStorage.getItem('darkMode') === 'true') {
    html.classList.add('dark');
  }
</script>

</body>
</html>
