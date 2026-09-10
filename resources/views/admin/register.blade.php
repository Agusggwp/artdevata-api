<!DOCTYPE html>
<html lang="id" class="h-full bg-[#F8FAFC]">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Daftar Admin Baru - ARTDEVATA</title>

  <!-- Google Fonts: Inter -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
  
  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"/>

  <!-- Tailwind CSS CDN -->
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
            }
          },
          fontFamily: { sans: ['Inter', 'sans-serif'] }
        }
      }
    }
  </script>

  @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full bg-[#F8FAFC] flex items-center justify-center p-4 min-h-screen relative overflow-hidden font-sans antialiased text-slate-800">

  <!-- Subtle Decorative Background Orbs -->
  <div class="absolute -top-24 -left-24 w-96 h-96 bg-[#21C9A4]/15 rounded-full blur-3xl pointer-events-none"></div>
  <div class="absolute -bottom-24 -right-24 w-96 h-96 bg-[#14433B]/10 rounded-full blur-3xl pointer-events-none"></div>

  <div class="w-full max-w-md relative z-10 my-8">

    <!-- Brand Header -->
    <div class="text-center mb-6">
      <div class="w-14 h-14 bg-[#14433B] text-[#21C9A4] font-black text-2xl rounded-2xl flex items-center justify-center mx-auto mb-3 shadow-lg border border-[#0E5D55]">
        A
      </div>
      <h1 class="text-2xl font-black tracking-tight text-slate-900">ARTDEVATA</h1>
      <p class="text-xs text-slate-700 font-semibold mt-1">Registrasi Akun Administrator Baru</p>
    </div>

    <!-- SaaS Register Card -->
    <div class="bg-white rounded-2xl p-6 sm:p-8 shadow-xl border border-slate-300">
      
      <!-- Session Alerts / Validation Errors -->
      @if($errors->any())
        <div class="bg-rose-50 border border-rose-300 text-rose-900 p-3.5 rounded-xl mb-5 text-xs font-semibold">
          <ul class="list-disc list-inside space-y-1">
            @foreach($errors->all() as $error)
              <li>{{ $error }}</li>
            @endforeach
          </ul>
        </div>
      @endif

      <!-- Form -->
      <form method="POST" action="{{ route('admin.register') }}" class="space-y-4">
        @csrf

        <!-- Name Field -->
        <div>
          <label for="name" class="block text-xs font-bold text-slate-800 mb-1.5">Nama Lengkap</label>
          <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-[#14433B] text-xs">
              <i class="fa-solid fa-user"></i>
            </div>
            <input 
              id="name"
              type="text" 
              name="name" 
              value="{{ old('name') }}"
              class="w-full pl-9 pr-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-xs text-slate-900 font-medium placeholder-slate-500 focus:bg-white focus:border-[#14433B] focus:ring-2 focus:ring-[#14433B]/20 outline-hidden transition-all duration-200"
              placeholder="Nama Admin" 
              required 
              autofocus
            >
          </div>
        </div>

        <!-- Email Field -->
        <div>
          <label for="email" class="block text-xs font-bold text-slate-800 mb-1.5">Email Administrator</label>
          <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-[#14433B] text-xs">
              <i class="fa-solid fa-envelope"></i>
            </div>
            <input 
              id="email"
              type="email" 
              name="email" 
              value="{{ old('email') }}"
              class="w-full pl-9 pr-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-xs text-slate-900 font-medium placeholder-slate-500 focus:bg-white focus:border-[#14433B] focus:ring-2 focus:ring-[#14433B]/20 outline-hidden transition-all duration-200"
              placeholder="admin@artdevata.com" 
              required
            >
          </div>
        </div>

        <!-- Password Field -->
        <div>
          <label for="password" class="block text-xs font-bold text-slate-800 mb-1.5">Password</label>
          <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-[#14433B] text-xs">
              <i class="fa-solid fa-lock"></i>
            </div>
            <input 
              id="password"
              type="password" 
              name="password" 
              class="w-full pl-9 pr-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-xs text-slate-900 font-bold placeholder-slate-500 focus:bg-white focus:border-[#14433B] focus:ring-2 focus:ring-[#14433B]/20 outline-hidden transition-all duration-200"
              placeholder="••••••••"
              required
            >
          </div>
        </div>

        <!-- Confirm Password Field -->
        <div>
          <label for="password_confirmation" class="block text-xs font-bold text-slate-800 mb-1.5">Konfirmasi Password</label>
          <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-[#14433B] text-xs">
              <i class="fa-solid fa-lock"></i>
            </div>
            <input 
              id="password_confirmation"
              type="password" 
              name="password_confirmation" 
              class="w-full pl-9 pr-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-xs text-slate-900 font-bold placeholder-slate-500 focus:bg-white focus:border-[#14433B] focus:ring-2 focus:ring-[#14433B]/20 outline-hidden transition-all duration-200"
              placeholder="••••••••"
              required
            >
          </div>
        </div>

        <!-- Submit Button -->
        <div class="pt-2">
          <button type="submit" class="w-full bg-[#14433B] hover:bg-[#0B443C] text-white font-extrabold py-3 px-4 rounded-xl text-sm shadow-md shadow-[#14433B]/20 hover:shadow-lg transition-all duration-200 flex items-center justify-center space-x-2 group">
            <i class="fa-solid fa-user-plus text-xs"></i>
            <span>Daftar Sekarang</span>
          </button>
        </div>

      </form>

      <!-- Login Link -->
      <div class="mt-6 pt-4 border-t border-slate-200 text-center">
        <p class="text-xs text-slate-700 font-medium">Sudah memiliki akun admin? <a href="{{ route('admin.login') }}" class="font-bold text-[#14433B] hover:text-[#0E5D55] underline">Masuk di sini</a></p>
      </div>

    </div>

    <!-- Footer -->
    <p class="text-center text-[11px] text-slate-600 font-medium mt-6">
      &copy; {{ date('Y') }} <span class="font-bold text-[#14433B]">ARTDEVATA</span>. All rights reserved.
    </p>

  </div>
</body>
</html>