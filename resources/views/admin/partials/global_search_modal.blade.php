<!-- Global Search Modal (Ctrl + K) -->
<div id="global-search-modal" class="fixed inset-0 z-50 hidden items-start justify-center pt-16 md:pt-24 bg-slate-900/50 backdrop-blur-xs p-4">
  <div class="bg-white rounded-2xl max-w-2xl w-full shadow-2xl border border-slate-100 overflow-hidden transform transition-all scale-95 duration-200">
    
    <!-- Search Bar Input Header -->
    <div class="p-4 border-b border-slate-100 flex items-center space-x-3 bg-slate-50/50">
      <i class="fa-solid fa-magnifying-glass text-slate-400 text-lg"></i>
      <input type="text" id="global-search-input" placeholder="Cari Lead, Client, Quotation, Project, Invoice... (Esc untuk keluar)" class="w-full bg-transparent border-none text-sm font-medium text-slate-800 focus:outline-none focus:ring-0" autocomplete="off" />
      <span class="px-2 py-1 bg-slate-200 text-slate-600 rounded text-[10px] font-bold uppercase tracking-wider">ESC</span>
    </div>

    <!-- Loading Indicator -->
    <div id="global-search-loading" class="hidden p-8 text-center text-slate-500">
      <i class="fa-solid fa-spinner animate-spin text-2xl text-[#14433B] mb-2"></i>
      <p class="text-xs">Mencari data bisnis...</p>
    </div>

    <!-- Results Container -->
    <div id="global-search-results" class="max-h-96 overflow-y-auto p-4 space-y-4">
      <div class="text-center py-8 text-slate-400">
        <i class="fa-solid fa-[#14433B] fa-[#14433B] text-3xl mb-2 text-slate-300"></i>
        <p class="text-xs">Ketik minimal 2 karakter untuk mencari seluruh data bisnis.</p>
      </div>
    </div>

    <!-- Footer shortcut info -->
    <div class="px-4 py-2.5 bg-slate-50 border-t border-slate-100 flex items-center justify-between text-[11px] text-slate-400">
      <div class="flex items-center space-x-3">
        <span><kbd class="px-1.5 py-0.5 bg-white border rounded text-[10px]">Ctrl</kbd> + <kbd class="px-1.5 py-0.5 bg-white border rounded text-[10px]">K</kbd> Buka Pencarian</span>
      </div>
      <span>ARTDEVATA Business Search</span>
    </div>

  </div>
</div>

<script>
  document.addEventListener('keydown', function(e) {
    if ((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === 'k') {
      e.preventDefault();
      openGlobalSearch();
    }
    if (e.key === 'Escape') {
      closeGlobalSearch();
    }
  });

  function openGlobalSearch() {
    const modal = document.getElementById('global-search-modal');
    const input = document.getElementById('global-search-input');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
    input.focus();
  }

  function closeGlobalSearch() {
    const modal = document.getElementById('global-search-modal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
  }

  const searchInput = document.getElementById('global-search-input');
  let searchTimeout = null;

  if (searchInput) {
    searchInput.addEventListener('input', function() {
      clearTimeout(searchTimeout);
      const query = this.value.trim();
      const resultsContainer = document.getElementById('global-search-results');
      const loading = document.getElementById('global-search-loading');

      if (query.length < 2) {
        resultsContainer.innerHTML = '<div class="text-center py-8 text-slate-400"><p class="text-xs">Ketik minimal 2 karakter untuk mencari seluruh data bisnis.</p></div>';
        return;
      }

      loading.classList.remove('hidden');

      searchTimeout = setTimeout(() => {
        fetch(`{{ route('admin.global-search') }}?q=${encodeURIComponent(query)}`)
          .then(res => res.json())
          .then(data => {
            loading.classList.add('hidden');
            let html = '';

            const sections = [
              { key: 'leads', title: 'Leads', icon: 'fa-user-tag', color: 'text-sky-600' },
              { key: 'clients', title: 'Clients', icon: 'fa-users-rectangle', color: 'text-indigo-600' },
              { key: 'quotations', title: 'Quotations', icon: 'fa-file-signature', color: 'text-amber-600' },
              { key: 'projects', title: 'Projects', icon: 'fa-diagram-project', color: 'text-emerald-600' },
              { key: 'invoices', title: 'Invoices', icon: 'fa-file-invoice-dollar', color: 'text-blue-600' },
            ];

            let hasResults = false;

            sections.forEach(sec => {
              const items = data[sec.key] || [];
              if (items.length > 0) {
                hasResults = true;
                html += `<div class="mb-3">
                  <div class="text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-1.5 flex items-center space-x-1.5">
                    <i class="fa-solid ${sec.icon} ${sec.color}"></i>
                    <span>${sec.title}</span>
                  </div>
                  <div class="space-y-1">`;
                items.forEach(item => {
                  html += `<a href="${item.url}" class="block p-2.5 rounded-xl hover:bg-slate-50 border border-transparent hover:border-slate-200 transition-all">
                    <div class="text-xs font-bold text-slate-800">${item.title}</div>
                    <div class="text-[11px] text-slate-500">${item.subtitle}</div>
                  </a>`;
                });
                html += `</div></div>`;
              }
            });

            if (!hasResults) {
              html = '<div class="text-center py-8 text-slate-400"><p class="text-xs">Tidak ditemukan data bisnis yang cocok dengan "' + query + '".</p></div>';
            }

            resultsContainer.innerHTML = html;
          })
          .catch(() => {
            loading.classList.add('hidden');
            resultsContainer.innerHTML = '<div class="text-center py-8 text-rose-500"><p class="text-xs">Gagal memuat hasil pencarian.</p></div>';
          });
      }, 300);
    });
  }
</script>
