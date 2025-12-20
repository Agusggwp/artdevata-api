<!DOCTYPE html>
<html lang="id" class="h-full scroll-smooth">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Edit Client - AdminPro</title>

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
    .sidebar-active { @apply bg-gradient-to-r from-primary to-secondary text-white; }
    .sidebar-hover:hover { @apply bg-gray-100 dark:bg-gray-700; }
    .card-hover:hover { @apply shadow-xl transform -translate-y-1 transition-all duration-300; }
    .btn { @apply px-4 py-2 rounded-lg font-medium transition-all duration-200; }
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

        <a href="{{ route('admin.clients.index') }}" class="flex items-center px-6 py-3 sidebar-active rounded-l-full mr-3">
          <i class="fas fa-users mr-3"></i> Client
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
            <h2 class="text-xl font-semibold">Edit Client</h2>
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

            <!-- Logout -->
            <form method="POST" action="{{ route('admin.logout') }}">
              @csrf
              <button class="text-sm text-red-600 hover:underline">Logout</button>
            </form>
          </div>
        </div>
      </header>

      <!-- Content -->
      <main class="flex-1 overflow-y-auto p-4 lg:p-8">

        <!-- Back Button -->
        <div class="mb-6">
          <a href="{{ route('admin.clients.index') }}" class="text-primary hover:underline flex items-center">
            <i class="fas fa-arrow-left mr-2"></i> Kembali ke Daftar Client
          </a>
        </div>

        <!-- Form -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-6">
          <h3 class="text-lg font-semibold mb-6">Form Edit Client</h3>

          @if($errors->any())
            <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg">
              <ul class="list-disc list-inside">
                @foreach($errors->all() as $error)
                  <li>{{ $error }}</li>
                @endforeach
              </ul>
            </div>
          @endif

          <form action="{{ route('admin.clients.update', $client) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <!-- Nama -->
              <div>
                <label class="block text-sm font-medium mb-2">Nama Client <span class="text-red-500">*</span></label>
                <input type="text" name="name" value="{{ old('name', $client->name) }}" required 
                  class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent dark:bg-gray-700"
                  placeholder="Masukkan nama client">
              </div>

              <!-- Email -->
              <div>
                <label class="block text-sm font-medium mb-2">Email</label>
                <input type="email" name="email" value="{{ old('email', $client->email) }}" 
                  class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent dark:bg-gray-700"
                  placeholder="email@example.com">
              </div>

              <!-- Telepon -->
              <div>
                <label class="block text-sm font-medium mb-2">Telepon</label>
                <input type="text" name="phone" value="{{ old('phone', $client->phone) }}" 
                  class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent dark:bg-gray-700"
                  placeholder="+62 xxx xxxx xxxx">
              </div>

              <!-- Perusahaan -->
              <div>
                <label class="block text-sm font-medium mb-2">Perusahaan</label>
                <input type="text" name="company" value="{{ old('company', $client->company) }}" 
                  class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent dark:bg-gray-700"
                  placeholder="Nama perusahaan">
              </div>

              <!-- Status -->
              <div>
                <label class="block text-sm font-medium mb-2">Status <span class="text-red-500">*</span></label>
                <select name="status" required 
                  class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent dark:bg-gray-700">
                  <option value="active" {{ old('status', $client->status) === 'active' ? 'selected' : '' }}>Aktif</option>
                  <option value="inactive" {{ old('status', $client->status) === 'inactive' ? 'selected' : '' }}>Tidak Aktif</option>
                </select>
              </div>

              <!-- Logo -->
              <div>
                <label class="block text-sm font-medium mb-2">Logo</label>
                @if($client->logo)
                  <div class="mb-2 flex items-center space-x-3">
                    <img src="{{ asset('storage/' . $client->logo) }}" alt="Current Logo" class="w-16 h-16 rounded-lg object-cover border">
                    <span class="text-sm text-gray-500">Logo saat ini</span>
                  </div>
                @endif
                <input type="file" name="logo" accept="image/*"
                  class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent dark:bg-gray-700">
                <p class="text-xs text-gray-500 mt-1">Format: JPG, PNG, GIF, SVG. Max: 2MB. Kosongkan jika tidak ingin mengubah.</p>
              </div>
            </div>

            <!-- Alamat -->
            <div>
              <label class="block text-sm font-medium mb-2">Alamat</label>
              <textarea name="address" rows="3"
                class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent dark:bg-gray-700"
                placeholder="Alamat lengkap">{{ old('address', $client->address) }}</textarea>
            </div>

            <!-- Catatan -->
            <div>
              <label class="block text-sm font-medium mb-2">Catatan</label>
              <textarea name="notes" rows="3"
                class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent dark:bg-gray-700"
                placeholder="Catatan tambahan...">{{ old('notes', $client->notes) }}</textarea>
            </div>

            <!-- Submit -->
            <div class="flex justify-end space-x-4">
              <a href="{{ route('admin.clients.index') }}" class="btn bg-gray-200 text-gray-700 hover:bg-gray-300">
                Batal
              </a>
              <button type="submit" class="btn bg-gradient-to-r from-primary to-secondary text-white hover:shadow-lg">
                <i class="fas fa-save mr-2"></i> Update Client
              </button>
            </div>
          </form>
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
  </script>

</body>
</html>
