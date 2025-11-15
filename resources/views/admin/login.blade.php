<!DOCTYPE html>
<html lang="id" class="h-full scroll-smooth">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Login Admin - AdminPro</title>

  <!-- Tailwind CSS -->
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
          fontFamily: { sans: ['Inter', 'sans-serif'] },
          boxShadow: { 'glow': '0 0 20px rgba(67, 97, 238, 0.4)' }
        }
      }
    }
  </script>

  <!-- Fonts & Icons -->
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>

  <style>
    .glass { 
      background: rgba(255, 255, 255, 0.15);
      backdrop-filter: blur(12px);
      -webkit-backdrop-filter: blur(12px);
      border: 1px solid rgba(255, 255, 255, 0.2);
    }
    .input-focus { @apply focus:ring-2 focus:ring-primary focus:border-transparent outline-none; }
    .btn-gradient { @apply bg-gradient-to-r from-primary to-secondary text-white font-bold py-3 px-6 rounded-xl hover:shadow-xl transform hover:-translate-y-1 transition-all duration-300; }
    .card-hover { @apply hover:shadow-2xl transition-all duration-300; }
  </style>
</head>
<body class="h-full bg-gradient-to-br from-blue-50 via-indigo-50 to-purple-50 dark:from-gray-900 dark:via-indigo-900 dark:to-purple-900 flex items-center justify-center p-4 min-h-screen">

  <!-- Background Pattern -->
  <div class="absolute inset-0 opacity-10">
    <div class="absolute inset-0" style="background: url('data:image/svg+xml,%3Csvg width=\"60\" height=\"60\" viewBox=\"0 0 60 60\" xmlns=\"http://www.w3.org/2000/svg\"%3E%3Cg fill=\"none\" fill-rule=\"evenodd\"%3E%3Cg fill=\"%234361ee\" fill-opacity=\"0.1\"%3E%3Cpath d=\"M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z\"/%3E%3C/g%3E%3C/g%3E%3C/svg%3E')"></div>
  </div>

  <div class="w-full max-w-md relative z-10">
    <!-- Login Card -->
    <div class="glass rounded-3xl p-8 card-hover border border-white/20">
      
      <!-- Logo & Title -->
      <div class="text-center mb-8">
        <div class="w-20 h-20 bg-gradient-to-br from-primary to-secondary rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-lg">
          <i class="fas fa-shield-alt text-3xl text-white"></i>
        </div>
        <h1 class="text-3xl font-bold bg-gradient-to-r from-primary to-secondary bg-clip-text text-transparent">ArtDevata</h1>
        <p class="text-gray-600 dark:text-gray-300 mt-2 text-sm">Masuk ke Panel Admin Profesional</p>
      </div>

      <!-- Alert Messages -->
      @if(session('error'))
        <div class="bg-red-100 dark:bg-red-900/30 border border-red-400 dark:border-red-700 text-red-700 dark:text-red-300 p-4 rounded-xl mb-5 text-sm flex items-center gap-2">
          <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
        </div>
      @endif
      @if(session('success'))
        <div class="bg-green-100 dark:bg-green-900/30 border border-green-400 dark:border-green-700 text-green-700 dark:text-green-300 p-4 rounded-xl mb-5 text-sm flex items-center gap-2">
          <i class="fas fa-check-circle"></i> {{ session('success') }}
        </div>
      @endif

      <!-- Login Form -->
      <form method="POST" action="{{ route('admin.login') }}" class="space-y-5">
        @csrf

        <!-- Email -->
        <div>
          <label class="flex items-center gap-2 text-sm font-semibold text-gray-700 dark:text-gray-200 mb-2">
            <i class="fas fa-envelope text-primary"></i> Email
          </label>
          <input 
            type="email" 
            name="email" 
            value="{{ old('email') }}"
            class="w-full px-4 py-3 bg-white/80 dark:bg-gray-700/80 border border-gray-300 dark:border-gray-600 rounded-xl input-focus transition-all duration-200 placeholder-gray-400"
            placeholder="admin@artdevata.net" 
            required 
            autofocus
          >
          @error('email')
            <p class="text-red-500 text-xs mt-1 flex items-center gap-1">
              <i class="fas fa-info-circle"></i> {{ $message }}
            </p>
          @enderror
        </div>

        <!-- Password -->
        <div>
          <label class="flex items-center gap-2 text-sm font-semibold text-gray-700 dark:text-gray-200 mb-2">
            <i class="fas fa-lock text-primary"></i> Password
          </label>
          <div class="relative">
            <input 
              type="password" 
              name="password" 
              class="w-full px-4 py-3 pr-12 bg-white/80 dark:bg-gray-700/80 border border-gray-300 dark:border-gray-600 rounded-xl input-focus transition-all duration-200 placeholder-gray-400"
              placeholder="••••••••"
              required
            >
            <button type="button" onclick="togglePassword(this)" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 hover:text-primary">
              <i class="fas fa-eye"></i>
            </button>
          </div>
          @error('password')
            <p class="text-red-500 text-xs mt-1 flex items-center gap-1">
              <i class="fas fa-info-circle"></i> {{ $message }}
            </p>
          @enderror
        </div>

        <!-- Submit Button -->
        <button type="submit" class="w-full btn-gradient text-lg shadow-glow">
          <i class="fas fa-sign-in-alt mr-2"></i> MASUK KE PANEL
        </button>
      </form>

      <!-- Register Link -->
     

      <!-- Footer -->
      <p class="text-center text-xs text-gray-500 dark:text-gray-400 mt-8">
        © {{ date('Y') }} <span class="font-bold text-primary">ART DEVATA</span> • All Rights Reserved
      </p>
    </div>
  </div>

  <!-- JavaScript -->
  <script>
    // Toggle Password Visibility
    function togglePassword(btn) {
      const input = btn.previousElementSibling;
      const icon = btn.querySelector('i');
      if (input.type === 'password') {
        input.type = 'text';
        icon.classList.replace('fa-eye', 'fa-eye-slash');
      } else {
        input.type = 'password';
        icon.classList.replace('fa-eye-slash', 'fa-eye');
      }
    }

    // Auto-focus email on load
    window.addEventListener('load', () => {
      document.querySelector('input[name="email"]').focus();
    });
  </script>
</body>
</html>