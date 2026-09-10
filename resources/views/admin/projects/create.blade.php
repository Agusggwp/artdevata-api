@extends('layouts.admin')

@section('title', 'Tambah Proyek Baru')

@section('content')

<div class="max-w-4xl mx-auto">
  
  <div class="mb-6 flex items-center justify-between">
    <div>
      <h1 class="text-xl font-bold text-slate-900">Buat Proyek Baru</h1>
      <p class="text-xs text-slate-500">Inisialisasi proyek software, alokasikan tim & tentukan anggaran</p>
    </div>
    <a href="{{ route('admin.projects.index') }}" class="px-3.5 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold transition-colors flex items-center space-x-1.5">
      <i class="fa-solid fa-arrow-left text-xs"></i>
      <span>Kembali</span>
    </a>
  </div>

  <div class="bg-white rounded-2xl p-6 sm:p-8 shadow-card border border-slate-100">
    <form action="{{ route('admin.projects.store') }}" method="POST" class="space-y-6">
      @csrf

      <!-- Nama Proyek -->
      <div>
        <label for="name" class="block text-xs font-semibold text-slate-700 mb-1.5">Nama Proyek <span class="text-rose-500">*</span></label>
        <input 
          id="name"
          type="text" 
          name="name" 
          value="{{ old('name') }}"
          placeholder="Contoh: Modernisasi Sistem ERP PT XYZ"
          class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 placeholder-slate-400 focus:bg-white focus:border-[#21C9A4] focus:ring-2 focus:ring-[#21C9A4]/20 outline-hidden transition-all duration-200"
          required
        >
        @error('name')<span class="text-rose-500 text-[11px] mt-1 block">{{ $message }}</span>@enderror
      </div>

      <!-- Deskripsi -->
      <div>
        <label for="description" class="block text-xs font-semibold text-slate-700 mb-1.5">Deskripsi / Ruang Lingkup Proyek</label>
        <textarea 
          id="description"
          name="description" 
          rows="4" 
          placeholder="Jelaskan kebutuhan, ruang lingkup pengerjaan, dan milestone utama proyek..."
          class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 placeholder-slate-400 focus:bg-white focus:border-[#21C9A4] focus:ring-2 focus:ring-[#21C9A4]/20 outline-hidden transition-all duration-200"
        >{{ old('description') }}</textarea>
        @error('description')<span class="text-rose-500 text-[11px] mt-1 block">{{ $message }}</span>@enderror
      </div>

      <!-- Klien & Status -->
      <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
        <div>
          <label for="client" class="block text-xs font-semibold text-slate-700 mb-1.5">Nama Klien</label>
          <input 
            id="client"
            type="text" 
            name="client" 
            value="{{ old('client') }}"
            placeholder="Contoh: PT XYZ Indonesia"
            class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 placeholder-slate-400 focus:bg-white focus:border-[#21C9A4] focus:ring-2 focus:ring-[#21C9A4]/20 outline-hidden transition-all duration-200"
          >
        </div>

        <div>
          <label for="status" class="block text-xs font-semibold text-slate-700 mb-1.5">Status Proyek <span class="text-rose-500">*</span></label>
          <select 
            id="status"
            name="status" 
            class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:bg-white focus:border-[#21C9A4] focus:ring-2 focus:ring-[#21C9A4]/20 outline-hidden transition-all duration-200"
            required
          >
            <option value="pending" {{ old('status') === 'pending' ? 'selected' : '' }}>Tertunda (Pending)</option>
            <option value="ongoing" {{ old('status') === 'ongoing' ? 'selected' : '' }}>Sedang Berjalan (Ongoing)</option>
            <option value="completed" {{ old('status') === 'completed' ? 'selected' : '' }}>Selesai (Completed)</option>
          </select>
        </div>
      </div>

      <!-- Tanggal Mulai & Selesai -->
      <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
        <div>
          <label for="start_date" class="block text-xs font-semibold text-slate-700 mb-1.5">Tanggal Mulai Proyek</label>
          <input 
            id="start_date"
            type="date" 
            name="start_date" 
            value="{{ old('start_date') }}"
            class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:bg-white focus:border-[#21C9A4] focus:ring-2 focus:ring-[#21C9A4]/20 outline-hidden transition-all duration-200"
          >
        </div>

        <div>
          <label for="end_date" class="block text-xs font-semibold text-slate-700 mb-1.5">Target Tanggal Selesai</label>
          <input 
            id="end_date"
            type="date" 
            name="end_date" 
            value="{{ old('end_date') }}"
            class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:bg-white focus:border-[#21C9A4] focus:ring-2 focus:ring-[#21C9A4]/20 outline-hidden transition-all duration-200"
          >
        </div>
      </div>

      <!-- Budget & Progress -->
      <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
        <div>
          <label for="budget" class="block text-xs font-semibold text-slate-700 mb-1.5">Anggaran Proyek (Budget Rp)</label>
          <input 
            id="budget"
            type="number" 
            name="budget" 
            step="0.01" 
            value="{{ old('budget', 0) }}"
            placeholder="0"
            class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:bg-white focus:border-[#21C9A4] focus:ring-2 focus:ring-[#21C9A4]/20 outline-hidden transition-all duration-200"
          >
        </div>

        <div>
          <label for="progress" class="block text-xs font-semibold text-slate-700 mb-1.5">Progres Pengerjaan (%)</label>
          <input 
            id="progress"
            type="number" 
            name="progress" 
            min="0" 
            max="100" 
            value="{{ old('progress', 0) }}"
            class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:bg-white focus:border-[#21C9A4] focus:ring-2 focus:ring-[#21C9A4]/20 outline-hidden transition-all duration-200"
          >
        </div>
      </div>

      <!-- Dynamic Team Members -->
      <div class="pt-4 border-t border-slate-100">
        <div class="flex items-center justify-between mb-3">
          <div>
            <label class="block text-xs font-bold text-slate-800">Tim Pengembang & Role</label>
            <p class="text-[11px] text-slate-400">Pilih administrator/developer yang terlibat dan tentukan perannya</p>
          </div>
          <button type="button" id="add-team-btn" class="px-3 py-1.5 rounded-xl bg-emerald-50 hover:bg-emerald-100 text-[#14433B] text-xs font-semibold transition-colors flex items-center space-x-1.5">
            <i class="fa-solid fa-plus text-xs text-[#21C9A4]"></i>
            <span>Tambah Anggota</span>
          </button>
        </div>

        <div id="team-container" class="space-y-3">
          <div class="team-member-row flex items-center gap-3">
            <div class="flex-1">
              <label class="block text-[11px] font-semibold text-slate-500 mb-1">Anggota Admin/Dev</label>
              <select name="team_members[]" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:bg-white focus:border-[#21C9A4] outline-hidden">
                <option value="">-- Pilih Anggota --</option>
                @foreach($admins as $admin)
                  <option value="{{ $admin->id }}">{{ $admin->name }} ({{ $admin->email }})</option>
                @endforeach
              </select>
            </div>
            <div class="flex-1">
              <label class="block text-[11px] font-semibold text-slate-500 mb-1">Role / Peran</label>
              <select name="team_roles[]" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:bg-white focus:border-[#21C9A4] outline-hidden">
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
            <button type="button" class="remove-team-btn p-2 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-xl mt-5 transition-colors">
              <i class="fa-solid fa-xmark text-sm"></i>
            </button>
          </div>
        </div>
      </div>

      <!-- Form Actions -->
      <div class="pt-6 border-t border-slate-100 flex items-center justify-end space-x-3">
        <a href="{{ route('admin.projects.index') }}" class="px-5 py-2.5 rounded-xl border border-slate-200 text-slate-700 text-xs font-semibold hover:bg-slate-50 transition-colors">
          Batal
        </a>
        <button type="submit" class="px-6 py-2.5 rounded-xl bg-[#14433B] hover:bg-[#0B443C] text-white text-xs font-semibold shadow-md shadow-[#14433B]/20 transition-all flex items-center space-x-2">
          <i class="fa-solid fa-floppy-disk text-xs"></i>
          <span>Simpan Proyek</span>
        </button>
      </div>

    </form>
  </div>

</div>

@endsection

@push('scripts')
<script>
  const addTeamBtn = document.getElementById('add-team-btn');
  const teamContainer = document.getElementById('team-container');
  const admins = @json($admins);

  addTeamBtn.addEventListener('click', () => {
    const row = document.createElement('div');
    row.className = 'team-member-row flex items-center gap-3';
    row.innerHTML = `
      <div class="flex-1">
        <select name="team_members[]" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:bg-white focus:border-[#21C9A4] outline-hidden">
          <option value="">-- Pilih Anggota --</option>
          ${admins.map(admin => `<option value="${admin.id}">${admin.name} (${admin.email})</option>`).join('')}
        </select>
      </div>
      <div class="flex-1">
        <select name="team_roles[]" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:bg-white focus:border-[#21C9A4] outline-hidden">
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
      <button type="button" class="remove-team-btn p-2 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-xl transition-colors">
        <i class="fa-solid fa-xmark text-sm"></i>
      </button>
    `;
    teamContainer.appendChild(row);
    row.querySelector('.remove-team-btn').addEventListener('click', () => row.remove());
  });

  document.querySelectorAll('.remove-team-btn').forEach(btn => {
    btn.addEventListener('click', (e) => {
      e.currentTarget.closest('.team-member-row').remove();
    });
  });
</script>
@endpush