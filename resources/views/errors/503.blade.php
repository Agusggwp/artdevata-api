<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>ArtDevata – </title>

    <!-- Tailwind CDN -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Font Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">

    <style>
      body { font-family: 'Inter', sans-serif; background: radial-gradient(circle at 30% 20%, #0d4d45 0%, #043b35 45%, #022b27 90%); }
      .fade-in { animation: fadeIn 1.2s ease-out forwards; }
      @keyframes fadeIn { from { opacity: 0; transform: translateY(10px) scale(.98); } to { opacity: 1; transform: translateY(0) scale(1); } }

      .particle { position: absolute; width: 7px; height: 7px; background: rgba(255,255,255,0.12); border-radius: 50%; filter: blur(1px); animation: float 7s infinite ease-in-out; }
      @keyframes float { 0%{ transform: translateY(0); opacity:0.25 } 50%{ transform: translateY(-25px); opacity:0.5 } 100%{ transform: translateY(0); opacity:0.25 } }

      .blob { position: absolute; border-radius: 50%; filter: blur(60px); opacity: 0.35; animation: blobMove 14s infinite ease-in-out; }
      @keyframes blobMove { 0%{ transform: translate(0,0) scale(1) } 50%{ transform: translate(60px,-40px) scale(1.2) } 100%{ transform: translate(0,0) scale(1) } }

      /* Countdown styles */
      .count-box { min-width: 68px; }
      .count-num { font-variant-numeric: tabular-nums; font-weight:700; font-size:1.6rem; }
      @media (min-width: 768px) { .count-num { font-size:2rem; } }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center relative overflow-hidden text-white">

    <!-- Animated background blobs -->
    <div class="blob w-[350px] h-[350px] bg-emerald-300/30 top-10 left-10"></div>
    <div class="blob w-[300px] h-[300px] bg-teal-400/20 bottom-20 right-20"></div>

    <!-- Particles -->
    <div class="particle" style="top:18%; left:12%; animation-delay:0s"></div>
    <div class="particle" style="top:65%; left:75%; animation-delay:1s"></div>
    <div class="particle" style="top:40%; left:46%; animation-delay:2s"></div>
    <div class="particle" style="top:78%; left:28%; animation-delay:2.8s"></div>

    <!-- Card -->
    <div class="fade-in backdrop-blur-xl bg-white/6 border border-white/8 shadow-[0_0_40px_rgba(0,0,0,0.35)] rounded-2xl p-10 max-w-2xl w-[92%] text-center z-10">
        <!-- Logo -->
        <div class="mb-4">
            <img src="https://admin.artdevata.net/logo/artdevata.png" class="mx-auto w-20 opacity-95 drop-shadow-[0_0_20px_rgba(0,255,200,0.18)]" alt="ArtDevata">
        </div>

        <h1 class="text-3xl md:text-4xl font-extrabold mb-2 text-white/95">Sedang Dalam Pemeliharaan</h1>
        <p class="text-white/75 mb-6 max-w-xl mx-auto">Kami sedang melakukan upgrade sistem. Terima kasih atas kesabaran Anda — kami akan kembali online tepat pada waktunya.</p>

        <!-- COUNTDOWN -->
        <div class="mt-6 flex items-center justify-center gap-3">
          <div class="bg-white/4 px-4 py-3 rounded-xl count-box">
            <div id="days" class="count-num text-white">--</div>
            <div class="text-xs text-white/60">Hari</div>
          </div>
          <div class="bg-white/4 px-4 py-3 rounded-xl count-box">
            <div id="hours" class="count-num text-white">--</div>
            <div class="text-xs text-white/60">Jam</div>
          </div>
          <div class="bg-white/4 px-4 py-3 rounded-xl count-box">
            <div id="minutes" class="count-num text-white">--</div>
            <div class="text-xs text-white/60">Menit</div>
          </div>
          <div class="bg-white/4 px-4 py-3 rounded-xl count-box">
            <div id="seconds" class="count-num text-white">--</div>
            <div class="text-xs text-white/60">Detik</div>
          </div>
        </div>

        <!-- Message when finished -->
        <div id="doneMsg" class="hidden mt-6 text-green-200 font-semibold">Selesai — situs sudah kembali!</div>

        <!-- Buttons -->
        <div class="mt-8 flex items-center justify-center gap-4">
            <a href="https://artdevata.net" class="px-5 py-3 rounded-xl bg-white/10 hover:bg-white/20 transition font-medium">Kembali ke Beranda</a>
            <a href="mailto:hello@artdevata.net" class="px-5 py-3 rounded-xl bg-emerald-400 text-black font-semibold hover:bg-emerald-500 transition">Hubungi Kami</a>
        </div>

        <p class="mt-6 text-sm text-white/40">© {{ date('Y') }} ArtDevata — All rights reserved</p>
    </div>

    <script>
      // === SET TARGET WAKTU DI SINI ===
      // Gunakan format ISO, misal: "2025-12-01T10:30:00+07:00"
      // Atau gunakan "YYYY-MM-DDTHH:MM:SS" (server timezone akan berlaku)
      const targetISO = "2025-12-01T10:30:00+07:00";

      // Elements
      const daysEl = document.getElementById('days');
      const hoursEl = document.getElementById('hours');
      const minutesEl = document.getElementById('minutes');
      const secondsEl = document.getElementById('seconds');
      const doneMsg = document.getElementById('doneMsg');

      function pad(n){ return String(n).padStart(2,'0'); }

      function updateCountdown(){
        const now = new Date();
        const target = new Date(targetISO);
        let diff = Math.floor((target - now) / 1000);

        if (isNaN(target.getTime())) {
          // invalid date
          daysEl.innerText = hoursEl.innerText = minutesEl.innerText = secondsEl.innerText = "--";
          return;
        }

        if (diff <= 0) {
          // selesai
          daysEl.innerText = "00";
          hoursEl.innerText = "00";
          minutesEl.innerText = "00";
          secondsEl.innerText = "00";
          doneMsg.classList.remove('hidden');
          return;
        }

        const days = Math.floor(diff / 86400); diff %= 86400;
        const hrs = Math.floor(diff / 3600); diff %= 3600;
        const mins = Math.floor(diff / 60); const secs = diff % 60;

        daysEl.innerText = pad(days);
        hoursEl.innerText = pad(hrs);
        minutesEl.innerText = pad(mins);
        secondsEl.innerText = pad(secs);
      }

      // initial + interval
      updateCountdown();
      const interval = setInterval(() => {
        updateCountdown();
        // stop interval when done (optional)
        if (new Date(targetISO) - new Date() <= 0) {
          clearInterval(interval);
        }
      }, 1000);
    </script>
</body>
</html>
