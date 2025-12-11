<!DOCTYPE html>
<html lang="id" class="h-full scroll-smooth">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Tambah Proyek - ARTDEVATA Admin</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
</head>
<body class="h-full bg-gray-50 text-gray-800 font-sans">

  <div class="flex h-screen overflow-hidden">
    <div class="flex-1 flex flex-col overflow-hidden">
      <main class="flex-1 overflow-y-auto p-4 lg:p-8">
        <div class="max-w-3xl mx-auto">

          <h1 class="text-3xl font-bold text-gray-800 mb-8">Tambah Proyek Baru</h1>

          <form action="{{ route('admin.projects.store') }}" method="POST" class="bg-white p-8 rounded-2xl shadow-lg">
            @csrf

            <!-- Nama Proyek -->
            <div class="mb-6">
              <label class="block text-sm font-semibold text-gray-700 mb-2">Nama Proyek</label>
              <input type="text" name="name" required class="w-full px-4 py-3 border border-gray-300 bg-white rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="Nama proyek...">
              @error('name')<span class="text-red-600 text-sm">{{ $message }}</span>@enderror
            </div>

            <!-- Deskripsi -->
            <div class="mb-6">
              <label class="block text-sm font-semibold text-gray-700 mb-2">Deskripsi</label>
              <textarea name="description" rows="4" class="w-full px-4 py-3 border border-gray-300 bg-white rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="Deskripsi proyek..."></textarea>
              @error('description')<span class="text-red-600 text-sm">{{ $message }}</span>@enderror
            </div>

            <!-- Grid 2 Kolom -->
            <div class="grid grid-cols-2 gap-6 mb-6">
              <!-- Klien -->
              <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Klien</label>
                <input type="text" name="client" class="w-full px-4 py-3 border border-gray-300 bg-white rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="Nama klien...">
              </div>

              <!-- Status -->
              <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Status</label>
                <select name="status" required class="w-full px-4 py-3 border border-gray-300 bg-white rounded-lg focus:ring-2 focus:ring-blue-500">
                  <option value="pending">Tertunda</option>
                  <option value="ongoing">Sedang Berjalan</option>
                  <option value="completed">Selesai</option>
                </select>
              </div>
            </div>

            <!-- Tanggal Mulai & Selesai -->
            <div class="grid grid-cols-2 gap-6 mb-6">
              <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Tanggal Mulai</label>
                <input type="date" name="start_date" class="w-full px-4 py-3 border border-gray-300 bg-white rounded-lg focus:ring-2 focus:ring-blue-500">
              </div>
              <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Tanggal Selesai</label>
                <input type="date" name="end_date" class="w-full px-4 py-3 border border-gray-300 bg-white rounded-lg focus:ring-2 focus:ring-blue-500">
              </div>
            </div>

            <!-- Budget & Progress -->
            <div class="grid grid-cols-2 gap-6 mb-6">
              <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Budget (Rp)</label>
                <input type="number" name="budget" step="0.01" class="w-full px-4 py-3 border border-gray-300 bg-white rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="0">
              </div>
              <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Progress (%)</label>
                <input type="number" name="progress" min="0" max="100" value="0" class="w-full px-4 py-3 border border-gray-300 bg-white rounded-lg focus:ring-2 focus:ring-blue-500">
              </div>
            </div>

            <!-- Tim Proyek -->
            <div class="mb-6">
              <label class="block text-sm font-semibold text-gray-700 mb-2">Tim Proyek</label>
              <div id="team-container" class="space-y-3">
                <div class="team-member-row flex gap-3 items-end">
                  <div class="flex-1">
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Anggota Tim</label>
                    <select name="team_members[]" class="w-full px-4 py-3 border border-gray-300 bg-white rounded-lg focus:ring-2 focus:ring-blue-500">
                      <option value="">-- Pilih Anggota --</option>
                      @forelse($admins as $admin)
                        <option value="{{ $admin->id }}">{{ $admin->name }}</option>
                      @empty
                        <option disabled>Tidak ada admin tersedia</option>
                      @endforelse
                    </select>
                  </div>
                  <div class="flex-1">
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Role</label>
                    <select name="team_roles[]" class="w-full px-4 py-3 border border-gray-300 bg-white rounded-lg focus:ring-2 focus:ring-blue-500">
                      <option value="">-- Pilih Role --</option>
                      <option value="Developer">Developer</option>
                      <option value="Designer">Designer</option>
                      <option value="Project Manager">Project Manager</option>
                      <option value="QA">QA</option>
                      <option value="Frontend">Frontend</option>
                      <option value="Backend">Backend</option>
                      <option value="DevOps">DevOps</option>
                    </select>
                  </div>
                  <button type="button" class="px-4 py-3 bg-red-500 hover:bg-red-600 text-white rounded-lg remove-team-btn">
                    <i class="fas fa-trash"></i>
                  </button>
                </div>
              </div>
              <button type="button" id="add-team-btn" class="mt-3 px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 rounded-lg text-sm font-semibold">
                <i class="fas fa-plus mr-2"></i> Tambah Anggota
              </button>
            </div>

            <!-- Tombol -->
            <div class="flex space-x-4">
              <button type="submit" class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-semibold">
                <i class="fas fa-save mr-2"></i> Simpan
              </button>
              <a href="{{ route('admin.projects.index') }}" class="px-6 py-3 border border-gray-300 rounded-lg hover:bg-gray-100">
                <i class="fas fa-arrow-left mr-2"></i> Kembali
              </a>
            </div>
          </form>

        </div>
      </main>
    </div>
  </div>

  <script>
    const addTeamBtn = document.getElementById('add-team-btn');
    const teamContainer = document.getElementById('team-container');
    const admins = @json($admins);

    addTeamBtn.addEventListener('click', () => {
      const row = document.createElement('div');
      row.className = 'team-member-row flex gap-3 items-end';
      row.innerHTML = `
        <div class="flex-1">
          <select name="team_members[]" class="w-full px-4 py-3 border border-gray-300 bg-white rounded-lg focus:ring-2 focus:ring-blue-500">
            <option value="">-- Pilih Anggota --</option>
            ${admins.map(admin => `<option value="${admin.id}">${admin.name}</option>`).join('')}
          </select>
        </div>
        <div class="flex-1">
          <select name="team_roles[]" class="w-full px-4 py-3 border border-gray-300 bg-white rounded-lg focus:ring-2 focus:ring-blue-500">
            <option value="">-- Pilih Role --</option>
            <option value="Developer">Developer</option>
            <option value="Designer">Designer</option>
            <option value="Project Manager">Project Manager</option>
            <option value="QA">QA</option>
            <option value="Frontend">Frontend</option>
            <option value="Backend">Backend</option>
            <option value="DevOps">DevOps</option>
          </select>
        </div>
        <button type="button" class="px-4 py-3 bg-red-500 hover:bg-red-600 text-white rounded-lg remove-team-btn">
          <i class="fas fa-trash"></i>
        </button>
      `;
      teamContainer.appendChild(row);
      row.querySelector('.remove-team-btn').addEventListener('click', () => row.remove());
    });

    // Remove button untuk row awal
    document.querySelectorAll('.remove-team-btn').forEach(btn => {
      btn.addEventListener('click', (e) => {
        e.currentTarget.closest('.team-member-row').remove();
      });
    });
  </script>

</body>
</html>