<!DOCTYPE html>
<html lang="id" class="h-full bg-[#F8FAFC]">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <meta name="theme-color" content="#14433B"/>
  <title>Login Administrator - ARTDEVATA Control Panel</title>
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
  <div class="fixed top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[700px] h-[700px] bg-[#21C9A4]/5 rounded-full blur-[160px] pointer-events-none"></div>

  <!-- Main Container -->
  <main class="relative z-10 w-full flex-1 flex items-center justify-center p-4 sm:p-6 lg:p-10">
    <div class="w-full max-w-6xl grid grid-cols-1 lg:grid-cols-12 rounded-3xl overflow-hidden shadow-2xl border border-slate-200/90 bg-white">
      
      <!-- LEFT COLUMN: Brand Showcase & Enterprise Features (Hidden on mobile, visible lg+) -->
      <section class="lg:col-span-6 relative p-8 sm:p-12 flex flex-col justify-between overflow-hidden bg-gradient-to-br from-[#14433B] via-[#0E5D55] to-[#0A342E] text-white border-b lg:border-b-0 lg:border-r border-[#0E5D55]">
        
        <!-- Background Grid & Highlights -->
        <div class="absolute inset-0 bg-grid-pattern opacity-40 pointer-events-none"></div>
        <div class="absolute -right-24 -top-24 w-80 h-80 bg-[#21C9A4]/20 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute -left-20 -bottom-20 w-80 h-80 bg-[#14433B]/60 rounded-full blur-3xl pointer-events-none"></div>

        <!-- Header: Brand & Live Status -->
        <div class="relative z-10">
          <div class="flex items-center justify-between gap-4 mb-8">
            <!-- Brand Logo -->
            <a href="{{ url('/') }}" class="flex items-center gap-3.5 group">
              <div class="w-12 h-12 rounded-2xl bg-white/10 p-1.5 shadow-lg shadow-[#21C9A4]/25 group-hover:scale-105 transition-transform duration-300 border border-white/15 flex items-center justify-center">
                <img src="{{ asset('logo.png') }}" alt="ARTDEVATA Logo" class="w-full h-full object-contain">
              </div>
              <div class="flex flex-col">
                <span class="text-xl font-extrabold tracking-tight text-white heading-font">ARTDEVATA</span>
                <span class="text-[11px] font-semibold tracking-wider text-[#21C9A4] uppercase">Enterprise Panel</span>
              </div>
            </a>

            <!-- System Status Indicator Badge -->
            <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-emerald-950/80 border border-emerald-500/30 text-emerald-300 text-xs font-semibold shadow-xs">
              <span class="relative flex h-2 w-2">
                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
              </span>
              <span>Sistem Aktif</span>
            </div>
          </div>

          <!-- Hero Pitch -->
          <div class="space-y-3 mt-6">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-lg bg-[#21C9A4]/15 border border-[#21C9A4]/30 text-[#21C9A4] text-xs font-bold uppercase tracking-wider">
              <i class="fa-solid fa-layer-group text-xs"></i>
              Executive Management Suite
            </div>
            <h2 class="text-2xl sm:text-3xl lg:text-4xl font-black text-white leading-tight heading-font">
              Kendali Bisnis & Ekosistem Digital Terintegrasi.
            </h2>
            <p class="text-xs sm:text-sm text-slate-300 leading-relaxed max-w-lg">
              Solusi all-in-one untuk memantau performa keuangan perusahaan, pipeline penawaran, manajemen proyek aktif, hingga otomatisasi invoice dan payroll dalam satu kendali presisi.
            </p>
          </div>
        </div>

        <!-- Middle Feature Cards List -->
        <div class="relative z-10 my-8 space-y-3.5">
          
          <!-- Item 1: Real-time Cashflow -->
          <div class="p-3.5 rounded-2xl bg-white/5 border border-white/10 hover:border-[#21C9A4]/40 hover:bg-white/10 transition-all duration-300 flex items-center gap-3.5 group backdrop-blur-xs">
            <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-[#14433B] to-[#0E5D55] text-[#21C9A4] border border-[#21C9A4]/30 flex items-center justify-center shrink-0 group-hover:scale-110 transition-transform">
              <i class="fa-solid fa-chart-line text-sm"></i>
            </div>
            <div>
              <h3 class="text-xs font-bold text-white group-hover:text-[#21C9A4] transition-colors">Visibilitas Finansial Real-Time</h3>
              <p class="text-[11px] text-slate-300 mt-0.5">Pemantauan saldo kas terpusat, status termin invoice, & kalkulasi laba akurat.</p>
            </div>
          </div>

          <!-- Item 2: Project Management -->
          <div class="p-3.5 rounded-2xl bg-white/5 border border-white/10 hover:border-[#21C9A4]/40 hover:bg-white/10 transition-all duration-300 flex items-center gap-3.5 group backdrop-blur-xs">
            <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-[#14433B] to-[#0E5D55] text-[#21C9A4] border border-[#21C9A4]/30 flex items-center justify-center shrink-0 group-hover:scale-110 transition-transform">
              <i class="fa-solid fa-diagram-project text-sm"></i>
            </div>
            <div>
              <h3 class="text-xs font-bold text-white group-hover:text-[#21C9A4] transition-colors">Proyek & Manajemen Klien</h3>
              <p class="text-[11px] text-slate-300 mt-0.5">Alur kerja transparan mulai dari prospek leads, quotation, hingga serah terima.</p>
            </div>
          </div>

          <!-- Item 3: Security & Audit Trail -->
          <div class="p-3.5 rounded-2xl bg-white/5 border border-white/10 hover:border-[#21C9A4]/40 hover:bg-white/10 transition-all duration-300 flex items-center gap-3.5 group backdrop-blur-xs">
            <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-[#14433B] to-[#0E5D55] text-[#21C9A4] border border-[#21C9A4]/30 flex items-center justify-center shrink-0 group-hover:scale-110 transition-transform">
              <i class="fa-solid fa-shield-halved text-sm"></i>
            </div>
            <div>
              <h3 class="text-xs font-bold text-white group-hover:text-[#21C9A4] transition-colors">Keamanan Berlapis & Audit Trail</h3>
              <p class="text-[11px] text-slate-300 mt-0.5">Proteksi rate-limiting cerdas, riwayat login sesi, dan rekaman aktivitas ketat.</p>
            </div>
          </div>

        </div>

        <!-- Footer / Trust Badges -->
        <div class="relative z-10 pt-4 border-t border-white/10 flex items-center justify-between text-[11px] text-slate-400">
          <div class="flex items-center gap-2">
            <i class="fa-solid fa-lock text-[#21C9A4]"></i>
            <span>TLS 1.3 / Enkripsi AES-256</span>
          </div>
          <span class="text-slate-500">v2.5 Enterprise</span>
        </div>

      </section>


      <!-- RIGHT COLUMN: Login Form Gateway -->
      <section class="lg:col-span-6 bg-white p-8 sm:p-12 flex flex-col justify-center relative">
        
        <!-- Mobile Brand Header (Displayed only on small screens) -->
        <div class="lg:hidden text-center mb-6">
          <div class="w-16 h-16 rounded-2xl bg-slate-50 p-2 shadow-md mx-auto mb-3 border border-slate-200 flex items-center justify-center">
            <img src="{{ asset('logo.png') }}" alt="ARTDEVATA Logo" class="w-full h-full object-contain">
          </div>
          <h1 class="text-2xl font-black text-slate-900 heading-font">ARTDEVATA</h1>
          <p class="text-xs text-slate-500 font-medium mt-0.5">Management Control Panel</p>
        </div>

        <div class="max-w-md w-full mx-auto space-y-6">

          <!-- Card Header -->
          <div class="space-y-2">
            <div class="flex items-center justify-between">
              <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-bold tracking-wide uppercase bg-emerald-50 text-[#14433B] border border-emerald-200">
                <i class="fa-solid fa-fingerprint text-[#14433B]"></i>
                <span>Autentikasi Administrator</span>
              </div>
              <img src="{{ asset('logo.png') }}" alt="ARTDEVATA Logo" class="hidden lg:block w-8 h-8 rounded-lg object-contain opacity-90 hover:opacity-100 transition-opacity">
            </div>
            <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight heading-font">
              Selamat Datang
            </h1>
            <p class="text-xs sm:text-sm text-slate-600 font-medium">
              Masukkan kredensial terdaftar untuk mengakses panel operasional.
            </p>
          </div>

          <!-- Alert Notifications -->
          @if(session('error'))
            <div class="relative bg-rose-50 border border-rose-300 text-rose-800 p-4 rounded-2xl text-xs font-semibold flex items-start gap-3 shadow-xs">
              <div class="w-6 h-6 rounded-lg bg-rose-100 text-rose-600 flex items-center justify-center shrink-0 mt-0.5">
                <i class="fa-solid fa-circle-exclamation text-xs"></i>
              </div>
              <div class="flex-1 leading-relaxed">
                <span class="block font-bold mb-0.5">Akses Ditolak</span>
                {{ session('error') }}
              </div>
            </div>
          @endif

          @if(session('success'))
            <div class="relative bg-emerald-50 border border-emerald-300 text-emerald-800 p-4 rounded-2xl text-xs font-semibold flex items-start gap-3 shadow-xs">
              <div class="w-6 h-6 rounded-lg bg-emerald-100 text-emerald-600 flex items-center justify-center shrink-0 mt-0.5">
                <i class="fa-solid fa-circle-check text-xs"></i>
              </div>
              <div class="flex-1 leading-relaxed">
                <span class="block font-bold mb-0.5">Berhasil</span>
                {{ session('success') }}
              </div>
            </div>
          @endif

          <!-- Form Area -->
          <form id="adminLoginForm" method="POST" action="{{ route('admin.login') }}" class="space-y-4">
            @csrf

            <!-- Email Field -->
            <div class="space-y-1.5">
              <label for="email" class="block text-xs font-bold text-slate-700">
                Alamat Email Administrator
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
                  class="w-full pl-10 pr-4 py-3 bg-slate-50 border border-slate-300 rounded-xl text-xs sm:text-sm text-slate-900 font-medium placeholder-slate-400 focus:bg-white focus:border-[#14433B] focus:ring-4 focus:ring-[#14433B]/10 outline-hidden transition-all duration-200"
                  placeholder="admin@artdevata.com" 
                  required 
                  autofocus
                >
              </div>
              @error('email')
                <p class="text-rose-600 text-[11px] font-semibold mt-1 flex items-center gap-1.5">
                  <i class="fa-solid fa-circle-info"></i> {{ $message }}
                </p>
              @enderror
            </div>

            <!-- Password Field -->
            <div class="space-y-1.5">
              <div class="flex items-center justify-between">
                <label for="password" class="block text-xs font-bold text-slate-700">
                  Password
                </label>
                <button 
                  type="button" 
                  onclick="openForgotPasswordModal()"
                  class="text-xs font-bold text-[#14433B] hover:underline focus:outline-hidden transition-colors"
                >
                  Lupa Password?
                </button>
              </div>
              <div class="relative group">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400 group-focus-within:text-[#14433B] transition-colors">
                  <i class="fa-solid fa-lock text-sm"></i>
                </div>
                <input 
                  id="password"
                  type="password" 
                  name="password" 
                  class="w-full pl-10 pr-11 py-3 bg-slate-50 border border-slate-300 rounded-xl text-xs sm:text-sm text-slate-900 font-bold placeholder-slate-400 focus:bg-white focus:border-[#14433B] focus:ring-4 focus:ring-[#14433B]/10 outline-hidden transition-all duration-200"
                  placeholder="••••••••••••"
                  required
                >
                <div class="absolute inset-y-0 right-0 pr-3.5 flex items-center">
                  <button 
                    type="button" 
                    id="togglePasswordBtn"
                    onclick="togglePasswordVisibility()" 
                    class="text-slate-400 hover:text-slate-700 focus:outline-hidden p-1 flex items-center justify-center transition-colors cursor-pointer"
                    title="Lihat / Sembunyikan Password"
                  >
                    <i id="togglePasswordIcon" class="fa-solid fa-eye text-sm leading-none"></i>
                  </button>
                </div>
              </div>
              @error('password')
                <p class="text-rose-600 text-[11px] font-semibold mt-1 flex items-center gap-1.5">
                  <i class="fa-solid fa-circle-info"></i> {{ $message }}
                </p>
              @enderror
            </div>

            <!-- Remember Me & Session Security -->
            <div class="pt-1 flex items-center justify-between">
              <label class="relative flex items-center gap-2.5 cursor-pointer select-none">
                <input 
                  type="checkbox" 
                  name="remember" 
                  id="remember"
                  value="1" 
                  class="w-4 h-4 rounded-md border-slate-300 text-[#14433B] focus:ring-[#21C9A4] focus:ring-offset-0 focus:ring-2 cursor-pointer transition-all"
                >
                <span class="text-xs font-semibold text-slate-600">Ingat sesi di perangkat ini</span>
              </label>
            </div>

            <!-- Submit Button -->
            <div class="pt-2">
              <button 
                type="submit" 
                id="submitBtn"
                class="w-full relative group overflow-hidden bg-gradient-to-r from-[#14433B] via-[#0E5D55] to-[#14433B] hover:brightness-110 text-white font-extrabold py-3.5 px-5 rounded-xl text-xs sm:text-sm shadow-lg shadow-[#14433B]/25 hover:shadow-xl hover:shadow-[#14433B]/35 active:scale-[0.99] transition-all duration-200 flex items-center justify-center gap-2 cursor-pointer"
              >
                <div class="absolute inset-0 -translate-x-full group-hover:translate-x-full bg-gradient-to-r from-transparent via-white/15 to-transparent transition-transform duration-700"></div>

                <span id="submitBtnText" class="flex items-center gap-2">
                  <span>Masuk ke Dashboard</span>
                  <i class="fa-solid fa-arrow-right text-xs group-hover:translate-x-1 transition-transform"></i>
                </span>

                <span id="submitBtnSpinner" class="hidden items-center gap-2">
                  <i class="fa-solid fa-circle-notch fa-spin text-sm"></i>
                  <span>Mengautentikasi...</span>
                </span>
              </button>
            </div>

          </form>

          <!-- Register Link if enabled -->
          @if (Route::has('admin.register'))
            <div class="pt-4 border-t border-slate-200 text-center">
              <p class="text-xs text-slate-600 font-medium">
                Belum memiliki akses administrator? 
                <a href="{{ route('admin.register') }}" class="font-bold text-[#14433B] hover:underline transition-colors ml-1">
                  Registrasi Akun Baru
                </a>
              </p>
            </div>
          @endif

          <!-- Security Notice Info Box -->
          <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200 flex items-start gap-2.5">
            <i class="fa-solid fa-shield-cat text-[#14433B] text-xs mt-0.5"></i>
            <p class="text-[11px] text-slate-500 leading-relaxed">
              Semua aktivitas login dan alamat IP Anda dicatat dalam sistem audit untuk keamanan data operasional ARTDEVATA.
            </p>
          </div>

          <!-- Footer Copyright -->
          <div class="text-center pt-2">
            <p class="text-[11px] text-slate-500 font-medium">
              &copy; {{ date('Y') }} <span class="font-bold text-slate-800">ARTDEVATA</span>. Hak Cipta Dilindungi.
            </p>
          </div>

        </div>

      </section>

    </div>
  </main>

  <!-- Modal: Lupa Password Information -->
  <div id="forgotPasswordModal" class="fixed inset-0 z-50 hidden flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-xs transition-opacity duration-300">
    <div class="bg-white rounded-3xl max-w-md w-full p-6 sm:p-8 shadow-2xl border border-slate-200 relative animate-in fade-in zoom-in-95 duration-200">
      
      <!-- Close Button -->
      <button 
        type="button" 
        onclick="closeForgotPasswordModal()" 
        class="absolute top-5 right-5 text-slate-400 hover:text-slate-700 p-2 rounded-xl hover:bg-slate-100 transition-colors"
      >
        <i class="fa-solid fa-xmark text-sm"></i>
      </button>

      <!-- Modal Content -->
      <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-[#14433B] border border-emerald-200 flex items-center justify-center mb-4 text-xl">
        <i class="fa-solid fa-key"></i>
      </div>

      <h3 class="text-lg font-black text-slate-900 heading-font">
        Pemulihan Akun Administrator
      </h3>
      <p class="text-xs text-slate-600 leading-relaxed mt-2">
        Demi alasan keamanan operasional dan integritas data, reset password administrator tidak dilakukan secara otomatis melalui email publik.
      </p>

      <div class="mt-4 p-3.5 bg-slate-50 rounded-2xl border border-slate-200 space-y-2">
        <div class="flex items-center gap-2 text-xs font-bold text-slate-800">
          <i class="fa-solid fa-headset text-[#14433B]"></i>
          <span>Langkah Pemulihan:</span>
        </div>
        <ul class="text-[11px] text-slate-600 space-y-1.5 list-disc pl-4">
          <li>Hubungi <strong>Super Administrator</strong> atau Technical Lead ARTDEVATA.</li>
          <li>Sebutkan alamat email terdaftar dan konfirmasi identitas Anda.</li>
          <li>Superadmin akan menerbitkan kredensial sementara atau token reset internal.</li>
        </ul>
      </div>

      <div class="mt-6 flex justify-end">
        <button 
          type="button" 
          onclick="closeForgotPasswordModal()" 
          class="w-full sm:w-auto px-5 py-2.5 rounded-xl bg-[#14433B] hover:bg-[#0E5D55] text-white text-xs font-bold shadow-md transition-colors"
        >
          Mengerti & Tutup
        </button>
      </div>

    </div>
  </div>

  <!-- Scripts -->
  <script>
    // Password Visibility Toggle
    function togglePasswordVisibility() {
      const passwordInput = document.getElementById('password');
      const toggleIcon = document.getElementById('togglePasswordIcon');
      
      if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        toggleIcon.classList.remove('fa-eye');
        toggleIcon.classList.add('fa-eye-slash');
      } else {
        passwordInput.type = 'password';
        toggleIcon.classList.remove('fa-eye-slash');
        toggleIcon.classList.add('fa-eye');
      }
    }

    // Modal Handlers
    function openForgotPasswordModal() {
      const modal = document.getElementById('forgotPasswordModal');
      modal.classList.remove('hidden');
      modal.classList.add('flex');
    }

    function closeForgotPasswordModal() {
      const modal = document.getElementById('forgotPasswordModal');
      modal.classList.add('hidden');
      modal.classList.remove('flex');
    }

    // Close modal on Escape key
    document.addEventListener('keydown', function(e) {
      if (e.key === 'Escape') {
        closeForgotPasswordModal();
      }
    });

    // Submit Loading State UX
    const loginForm = document.getElementById('adminLoginForm');
    const submitBtn = document.getElementById('submitBtn');
    const submitBtnText = document.getElementById('submitBtnText');
    const submitBtnSpinner = document.getElementById('submitBtnSpinner');

    if (loginForm && submitBtn) {
      loginForm.addEventListener('submit', function() {
        submitBtn.disabled = true;
        submitBtn.classList.add('opacity-80', 'cursor-not-allowed');
        submitBtnText.classList.add('hidden');
        submitBtnSpinner.classList.remove('hidden');
        submitBtnSpinner.classList.add('flex');
      });
    }
  </script>
</body>
</html>