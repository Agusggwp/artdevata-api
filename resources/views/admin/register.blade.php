<!DOCTYPE html>
<html lang="id" class="h-full bg-[#F8FAFC]">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <meta name="theme-color" content="#14433B"/>
  <title>Registrasi Administrator - ARTDEVATA Control Panel</title>
  <link rel="icon" type="image/png" href="{{ asset('logo.png') }}"/>

  <!-- Google Fonts: Inter & Plus Jakarta Sans -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=Plus+Jakarta+Sans:wght@500;600;700;800&display=swap" rel="stylesheet">
  
  <!-- Font Awesome Icons -->
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
              accentHover: '#1ab391',
              dark: '#0B443C',
              bg: '#F8FAFC',
            }
          },
          fontFamily: {
            sans: ['Inter', 'sans-serif'],
            display: ['Plus Jakarta Sans', 'Inter', 'sans-serif']
          }
        }
      }
    }
  </script>

  <style>
    body {
      font-family: 'Inter', sans-serif;
    }
    .heading-font {
      font-family: 'Plus Jakarta Sans', sans-serif;
    }
    .bg-grid-pattern {
      background-image: radial-gradient(rgba(33, 201, 164, 0.15) 1px, transparent 1px);
      background-size: 28px 28px;
    }
  </style>

  @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-[#F8FAFC] text-slate-800 antialiased selection:bg-[#21C9A4] selection:text-[#071A16] flex flex-col justify-between overflow-x-hidden relative">

  <!-- Subtle Decorative Background Orbs on White Background -->
  <div class="fixed -top-24 -left-24 w-96 h-96 bg-[#21C9A4]/15 rounded-full blur-3xl pointer-events-none"></div>
  <div class="fixed -bottom-24 -right-24 w-96 h-96 bg-[#14433B]/10 rounded-full blur-3xl pointer-events-none"></div>

  <!-- Main Container -->
  <main class="relative z-10 w-full flex-1 flex items-center justify-center p-4 sm:p-6 lg:p-10">
    <div class="w-full max-w-6xl grid grid-cols-1 lg:grid-cols-12 rounded-3xl overflow-hidden shadow-2xl border border-slate-200/90 bg-white">
      
      <!-- LEFT COLUMN: Brand Showcase -->
      <section class="lg:col-span-5 relative p-8 sm:p-12 flex flex-col justify-between overflow-hidden bg-gradient-to-br from-[#14433B] via-[#0E5D55] to-[#0A342E] text-white border-b lg:border-b-0 lg:border-r border-[#0E5D55]">
        
        <div class="absolute inset-0 bg-grid-pattern opacity-40 pointer-events-none"></div>
        <div class="absolute -right-24 -top-24 w-80 h-80 bg-[#21C9A4]/20 rounded-full blur-3xl pointer-events-none"></div>

        <!-- Header: Brand -->
        <div class="relative z-10">
          <div class="flex items-center justify-between gap-4 mb-8">
            <a href="{{ url('/') }}" class="flex items-center gap-3.5 group">
              <div class="w-12 h-12 rounded-2xl bg-white/10 p-1.5 shadow-lg shadow-[#21C9A4]/25 group-hover:scale-105 transition-transform duration-300 border border-white/15 flex items-center justify-center">
                <img src="{{ asset('logo.png') }}" alt="ARTDEVATA Logo" class="w-full h-full object-contain">
              </div>
              <div class="flex flex-col">
                <span class="text-xl font-extrabold tracking-tight text-white heading-font">ARTDEVATA</span>
                <span class="text-[11px] font-semibold tracking-wider text-[#21C9A4] uppercase">Internal Access</span>
              </div>
            </a>
          </div>

          <div class="space-y-3 mt-4">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-lg bg-[#21C9A4]/15 border border-[#21C9A4]/30 text-[#21C9A4] text-xs font-bold uppercase tracking-wider">
              <i class="fa-solid fa-user-shield text-xs"></i>
              Registrasi Akun Baru
            </div>
            <h2 class="text-2xl sm:text-3xl font-black text-white leading-tight heading-font">
              Akses Kendali Manajemen Terpadu.
            </h2>
            <p class="text-xs sm:text-sm text-slate-300 leading-relaxed">
              Daftarkan akun administrator untuk berkolaborasi dalam pengelolaan proyek, pemantauan leads prospek, dan transaksi operasional bisnis ARTDEVATA.
            </p>
          </div>
        </div>

        <!-- Security Notice on Left -->
        <div class="relative z-10 my-8 p-4 rounded-2xl bg-white/5 border border-white/10 backdrop-blur-xs space-y-2">
          <div class="flex items-center gap-2 text-xs font-bold text-[#21C9A4]">
            <i class="fa-solid fa-shield-halved"></i>
            <span>Standar Otorisasi Ketat</span>
          </div>
          <p class="text-[11px] text-slate-300 leading-relaxed">
            Setiap pendaftaran baru diawasi dan terintegrasi dengan sistem Role-Based Access Control (RBAC) untuk melindungi data klien dan keuangan.
          </p>
        </div>

        <!-- Footer Info -->
        <div class="relative z-10 pt-4 border-t border-white/10 flex items-center justify-between text-[11px] text-slate-400">
          <div class="flex items-center gap-2">
            <i class="fa-solid fa-lock text-[#21C9A4]"></i>
            <span>Keamanan Enkripsi End-to-End</span>
          </div>
          <span class="text-slate-500">v2.5</span>
        </div>

      </section>

      <!-- RIGHT COLUMN: Registration Form -->
      <section class="lg:col-span-7 bg-white p-8 sm:p-12 flex flex-col justify-center relative">
        
        <!-- Mobile Brand Header -->
        <div class="lg:hidden text-center mb-6">
          <div class="w-16 h-16 rounded-2xl bg-slate-50 p-2 shadow-md mx-auto mb-3 border border-slate-200 flex items-center justify-center">
            <img src="{{ asset('logo.png') }}" alt="ARTDEVATA Logo" class="w-full h-full object-contain">
          </div>
          <h1 class="text-2xl font-black text-slate-900 heading-font">ARTDEVATA</h1>
          <p class="text-xs text-slate-500 font-medium">Registrasi Akun Administrator</p>
        </div>

        <div class="max-w-md w-full mx-auto space-y-5">

          <!-- Card Header -->
          <div class="space-y-1.5">
            <div class="flex items-center justify-between">
              <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight heading-font">
                Daftar Admin Baru
              </h1>
              <img src="{{ asset('logo.png') }}" alt="ARTDEVATA Logo" class="hidden lg:block w-8 h-8 rounded-lg object-contain opacity-90 hover:opacity-100 transition-opacity">
            </div>
            <p class="text-xs sm:text-sm text-slate-600 font-medium">
              Lengkapi formulir di bawah ini untuk membuat profil administrator.
            </p>
          </div>

          <!-- Validation Errors Alert -->
          @if($errors->any())
            <div class="bg-rose-50 border border-rose-300 text-rose-800 p-4 rounded-2xl text-xs font-semibold space-y-1 shadow-xs">
              <div class="flex items-center gap-2 font-bold mb-1">
                <i class="fa-solid fa-circle-exclamation text-rose-600"></i>
                <span>Terdapat kesalahan pengisian:</span>
              </div>
              <ul class="list-disc list-inside space-y-0.5 text-[11px] pl-2">
                @foreach($errors->all() as $error)
                  <li>{{ $error }}</li>
                @endforeach
              </ul>
            </div>
          @endif

          <!-- Form Area -->
          <form id="adminRegisterForm" method="POST" action="{{ route('admin.register') }}" class="space-y-3.5">
            @csrf

            <!-- Name Field -->
            <div class="space-y-1">
              <label for="name" class="block text-xs font-bold text-slate-700">
                Nama Lengkap
              </label>
              <div class="relative group">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400 group-focus-within:text-[#14433B] transition-colors">
                  <i class="fa-solid fa-user text-sm"></i>
                </div>
                <input 
                  id="name"
                  type="text" 
                  name="name" 
                  value="{{ old('name') }}"
                  class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-xs sm:text-sm text-slate-900 font-medium placeholder-slate-400 focus:bg-white focus:border-[#14433B] focus:ring-4 focus:ring-[#14433B]/10 outline-hidden transition-all duration-200"
                  placeholder="Nama Lengkap Administrator" 
                  required 
                  autofocus
                >
              </div>
            </div>

            <!-- Email Field -->
            <div class="space-y-1">
              <label for="email" class="block text-xs font-bold text-slate-700">
                Email Administrator
              </label>
              <div class="relative group">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400 group-focus-within:text-[#14433B] transition-colors">
                  <i class="fa-solid fa-envelope text-sm"></i>
                </div>
                <input 
                  id="email"
                  type="email" 
                  name="email" 
                  value="{{ old('email') }}"
                  class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-xs sm:text-sm text-slate-900 font-medium placeholder-slate-400 focus:bg-white focus:border-[#14433B] focus:ring-4 focus:ring-[#14433B]/10 outline-hidden transition-all duration-200"
                  placeholder="admin@artdevata.com" 
                  required
                >
              </div>
            </div>

            <!-- Password Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
              <!-- Password -->
              <div class="space-y-1">
                <label for="password" class="block text-xs font-bold text-slate-700">
                  Password (Min. 8)
                </label>
                <div class="relative group">
                  <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400 group-focus-within:text-[#14433B] transition-colors">
                    <i class="fa-solid fa-lock text-sm"></i>
                  </div>
                  <input 
                    id="password"
                    type="password" 
                    name="password" 
                    class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-xs sm:text-sm text-slate-900 font-bold placeholder-slate-400 focus:bg-white focus:border-[#14433B] focus:ring-4 focus:ring-[#14433B]/10 outline-hidden transition-all duration-200"
                    placeholder="••••••••"
                    required
                  >
                </div>
              </div>

              <!-- Confirm Password -->
              <div class="space-y-1">
                <label for="password_confirmation" class="block text-xs font-bold text-slate-700">
                  Ulangi Password
                </label>
                <div class="relative group">
                  <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400 group-focus-within:text-[#14433B] transition-colors">
                    <i class="fa-solid fa-check-double text-sm"></i>
                  </div>
                  <input 
                    id="password_confirmation"
                    type="password" 
                    name="password_confirmation" 
                    class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-xs sm:text-sm text-slate-900 font-bold placeholder-slate-400 focus:bg-white focus:border-[#14433B] focus:ring-4 focus:ring-[#14433B]/10 outline-hidden transition-all duration-200"
                    placeholder="••••••••"
                    required
                  >
                </div>
              </div>
            </div>

            <!-- Submit Button -->
            <div class="pt-3">
              <button 
                type="submit" 
                id="regSubmitBtn"
                class="w-full relative group overflow-hidden bg-gradient-to-r from-[#14433B] via-[#0E5D55] to-[#14433B] hover:brightness-110 text-white font-extrabold py-3.5 px-5 rounded-xl text-xs sm:text-sm shadow-lg shadow-[#14433B]/25 hover:shadow-xl hover:shadow-[#14433B]/35 active:scale-[0.99] transition-all duration-200 flex items-center justify-center gap-2 cursor-pointer"
              >
                <div class="absolute inset-0 -translate-x-full group-hover:translate-x-full bg-gradient-to-r from-transparent via-white/15 to-transparent transition-transform duration-700"></div>

                <span id="regSubmitBtnText" class="flex items-center gap-2">
                  <i class="fa-solid fa-user-plus text-xs"></i>
                  <span>Daftarkan Administrator</span>
                </span>

                <span id="regSubmitBtnSpinner" class="hidden items-center gap-2">
                  <i class="fa-solid fa-circle-notch fa-spin text-sm"></i>
                  <span>Memproses Pendaftaran...</span>
                </span>
              </button>
            </div>

          </form>

          <!-- Back to Login Link -->
          <div class="pt-3 border-t border-slate-200 text-center">
            <p class="text-xs text-slate-600 font-medium">
              Sudah memiliki akun administrator? 
              <a href="{{ route('admin.login') }}" class="font-bold text-[#14433B] hover:underline transition-colors ml-1">
                Masuk di sini
              </a>
            </p>
          </div>

          <!-- Footer Copyright -->
          <div class="text-center pt-1">
            <p class="text-[11px] text-slate-500 font-medium">
              &copy; {{ date('Y') }} <span class="font-bold text-slate-800">ARTDEVATA</span>. Hak Cipta Dilindungi.
            </p>
          </div>

        </div>

      </section>

    </div>
  </main>

  <script>
    const regForm = document.getElementById('adminRegisterForm');
    const regSubmitBtn = document.getElementById('regSubmitBtn');
    const regSubmitBtnText = document.getElementById('regSubmitBtnText');
    const regSubmitBtnSpinner = document.getElementById('regSubmitBtnSpinner');

    if (regForm && regSubmitBtn) {
      regForm.addEventListener('submit', function() {
        regSubmitBtn.disabled = true;
        regSubmitBtn.classList.add('opacity-80', 'cursor-not-allowed');
        regSubmitBtnText.classList.add('hidden');
        regSubmitBtnSpinner.classList.remove('hidden');
        regSubmitBtnSpinner.classList.add('flex');
      });
    }
  </script>
</body>
</html>