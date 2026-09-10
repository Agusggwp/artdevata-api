@extends('layouts.admin')

@section('title', 'Tambah Peran / Role Baru')

@section('content')

<!-- Header Bar -->
<div class="flex items-center justify-between mb-6">
  <div>
    <h1 class="text-xl font-bold text-slate-900">Tambah Peran / Role Baru</h1>
    <p class="text-xs text-slate-700 font-medium">Buat tingkat kewenangan baru dan atur matriks izin aksesnya</p>
  </div>

  <a href="{{ route('admin.roles.index') }}" class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-800 text-xs font-bold border border-slate-300 transition-colors flex items-center space-x-2">
    <i class="fa-solid fa-arrow-left"></i>
    <span>Kembali</span>
  </a>
</div>

<!-- Form Card -->
<div class="bg-white rounded-2xl p-6 sm:p-8 shadow-card border border-slate-200">
  
  <form method="POST" action="{{ route('admin.roles.store') }}" class="space-y-6">
    @csrf

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
      <!-- Role Name -->
      <div>
        <label for="name" class="block text-xs font-bold text-slate-800 mb-1.5">Nama Peran <span class="text-rose-500">*</span></label>
        <input 
          id="name"
          type="text" 
          name="name" 
          value="{{ old('name') }}"
          oninput="document.getElementById('slug').value = this.value.toLowerCase().replace(/[^a-z0-9]/g, '-').replace(/-+/g, '-')"
          class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-xs text-slate-900 font-medium focus:bg-white focus:border-[#14433B] focus:ring-2 focus:ring-[#14433B]/20 outline-hidden transition-all"
          placeholder="Contoh: Quality Assurance" 
          required 
          autofocus
        >
        @error('name')
          <p class="text-rose-600 text-[11px] font-semibold mt-1">{{ $message }}</p>
        @enderror
      </div>

      <!-- Slug -->
      <div>
        <label for="slug" class="block text-xs font-bold text-slate-800 mb-1.5">Slug Unique Identifier <span class="text-rose-500">*</span></label>
        <input 
          id="slug"
          type="text" 
          name="slug" 
          value="{{ old('slug') }}"
          class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-xs text-slate-900 font-mono focus:bg-white focus:border-[#14433B] focus:ring-2 focus:ring-[#14433B]/20 outline-hidden transition-all"
          placeholder="quality-assurance" 
          required
        >
        @error('slug')
          <p class="text-rose-600 text-[11px] font-semibold mt-1">{{ $message }}</p>
        @enderror
      </div>
    </div>

    <!-- Description -->
    <div>
      <label for="description" class="block text-xs font-bold text-slate-800 mb-1.5">Deskripsi Peran</label>
      <input 
        id="description"
        type="text" 
        name="description" 
        value="{{ old('description') }}"
        class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-xs text-slate-900 font-medium focus:bg-white focus:border-[#14433B] focus:ring-2 focus:ring-[#14433B]/20 outline-hidden transition-all"
        placeholder="Tuliskan keterangan kewenangan peran ini..." 
      >
    </div>

    <!-- Permission Matrix Grouped by Module -->
    <div class="pt-4 border-t border-slate-200">
      <div class="flex items-center justify-between mb-4">
        <div>
          <h3 class="text-sm font-bold text-slate-900">Matriks Izin Akses (Permissions)</h3>
          <p class="text-xs text-slate-600">Centang bidang izin yang diperbolehkan untuk peran ini</p>
        </div>
        <button type="button" onclick="toggleAllPermissions(true)" class="text-xs text-[#14433B] font-bold hover:underline">Pilih Semua</button>
      </div>

      <div class="space-y-6">
        @foreach($permissions as $module => $modulePermissions)
          <div class="bg-slate-50 rounded-xl p-4 border border-slate-200">
            <div class="flex items-center justify-between pb-2 mb-3 border-b border-slate-200">
              <h4 class="text-xs font-bold text-[#14433B] uppercase tracking-wide flex items-center space-x-2">
                <i class="fa-solid fa-[#14433B] fa-shield-halved text-xs"></i>
                <span>Modul {{ $module }}</span>
              </h4>
              <label class="text-[11px] text-slate-600 font-semibold cursor-pointer select-none">
                <input type="checkbox" onchange="toggleGroup(this, 'module-{{ Str::slug($module) }}')" class="rounded border-slate-300 text-[#14433B] focus:ring-[#14433B] mr-1">
                Pilih Modul Ini
              </label>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3 module-{{ Str::slug($module) }}">
              @foreach($modulePermissions as $perm)
                <label class="flex items-start space-x-2.5 p-2 rounded-lg bg-white border border-slate-200 hover:border-[#14433B]/40 cursor-pointer transition-all">
                  <input 
                    type="checkbox" 
                    name="permissions[]" 
                    value="{{ $perm->id }}"
                    class="perm-checkbox rounded border-slate-300 text-[#14433B] focus:ring-[#14433B] mt-0.5"
                  >
                  <div class="flex flex-col">
                    <span class="text-xs font-bold text-slate-800">{{ $perm->name }}</span>
                    <span class="text-[10px] text-slate-500 leading-tight">{{ $perm->description }}</span>
                  </div>
                </label>
              @endforeach
            </div>
          </div>
        @endforeach
      </div>
    </div>

    <!-- Action Buttons -->
    <div class="pt-4 border-t border-slate-200 flex items-center justify-end space-x-3">
      <a href="{{ route('admin.roles.index') }}" class="px-5 py-2.5 rounded-xl border border-slate-300 text-slate-700 text-xs font-bold hover:bg-slate-50 transition-colors">
        Batal
      </a>
      <button type="submit" class="px-6 py-2.5 rounded-xl bg-[#14433B] hover:bg-[#0B443C] text-white text-xs font-extrabold shadow-md shadow-[#14433B]/20 transition-all flex items-center space-x-2">
        <i class="fa-solid fa-check text-xs"></i>
        <span>Simpan Peran</span>
      </button>
    </div>

  </form>

</div>

@push('scripts')
<script>
  function toggleGroup(source, groupClass) {
    const checkboxes = document.querySelectorAll('.' + groupClass + ' .perm-checkbox');
    checkboxes.forEach(cb => cb.checked = source.checked);
  }

  function toggleAllPermissions(status) {
    document.querySelectorAll('.perm-checkbox').forEach(cb => cb.checked = status);
  }
</script>
@endpush

@endsection
