@extends('layouts.admin')

@section('title', 'Kelola Blog & Berita')

@section('content')

<!-- Header Bar -->
<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
  <div>
    <h1 class="text-xl font-bold text-slate-900">Daftar Artikel Blog</h1>
    <p class="text-xs text-slate-700 font-medium">Kelola artikel, berita perusahaan, dan wawasan edukasi ARTDEVATA</p>
  </div>
  
  <a href="{{ route('admin.blogs.create') }}" class="px-4 py-2.5 rounded-xl bg-[#14433B] hover:bg-[#0B443C] text-white text-xs font-semibold shadow-md shadow-[#14433B]/20 transition-all duration-200 flex items-center space-x-2 w-fit">
    <i class="fa-solid fa-plus text-xs"></i>
    <span>Tulis Artikel Baru</span>
  </a>
</div>

<!-- Table Card -->
<div class="bg-white rounded-2xl shadow-card border border-slate-200 overflow-hidden">
  
  <div class="overflow-x-auto">
    <table class="w-full text-left border-collapse">
      <thead>
        <tr class="border-b border-slate-200 bg-slate-100/70 text-[11px] uppercase tracking-wider text-slate-700 font-bold">
          <th class="py-3.5 px-4 sm:px-6">Artikel</th>
          <th class="py-3.5 px-4 sm:px-6">Kategori</th>
          <th class="py-3.5 px-4 sm:px-6">Ringkasan Konten</th>
          <th class="py-3.5 px-4 sm:px-6 text-right">Aksi</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-slate-100 text-xs">
        @forelse($blogs as $blog)
          <tr class="hover:bg-slate-50 transition-colors">
            
            <!-- Article Title & Thumbnail -->
            <td class="py-4 px-4 sm:px-6">
              <div class="flex items-center space-x-3">
                @if($blog->image)
                  <img src="{{ Storage::url($blog->image) }}" alt="{{ $blog->title }}" class="w-14 h-14 object-cover rounded-xl border border-slate-200 shadow-xs shrink-0">
                @else
                  <div class="w-14 h-14 rounded-xl bg-[#14433B]/10 text-[#14433B] flex items-center justify-center font-bold text-base shrink-0">
                    <i class="fa-solid fa-newspaper"></i>
                  </div>
                @endif
                <div>
                  <span class="font-bold text-slate-900 block text-sm">{{ $blog->title }}</span>
                  <span class="text-[10px] text-slate-600 font-semibold">Dibuat: {{ $blog->created_at ? $blog->created_at->format('d M Y') : '-' }}</span>
                </div>
              </div>
            </td>

            <!-- Category -->
            <td class="py-4 px-4 sm:px-6 whitespace-nowrap">
              <span class="px-2.5 py-1 bg-emerald-100 text-emerald-950 border border-emerald-300 text-[11px] font-bold rounded-md">
                {{ $blog->category ?? 'Umum' }}
              </span>
            </td>

            <!-- Content excerpt -->
            <td class="py-4 px-4 sm:px-6 text-slate-800 font-medium max-w-sm">
              <p class="line-clamp-2 text-xs leading-relaxed">{{ Str::limit(strip_tags($blog->content), 90) }}</p>
            </td>

            <!-- Actions -->
            <td class="py-4 px-4 sm:px-6 text-right whitespace-nowrap">
              <div class="flex items-center justify-end space-x-2">
                <a href="{{ route('admin.blogs.edit', $blog) }}" class="p-2 rounded-lg text-slate-700 hover:text-[#14433B] hover:bg-slate-100 transition-colors" title="Edit Artikel">
                  <i class="fa-solid fa-pen-to-square"></i>
                </a>
                <button type="button" onclick="confirmDelete('{{ route('admin.blogs.destroy', $blog) }}', 'Apakah Anda yakin ingin menghapus artikel \'{{ addslashes($blog->title) }}\'?')" class="p-2 rounded-lg text-slate-600 hover:text-rose-600 hover:bg-rose-50 transition-colors" title="Hapus Artikel">
                  <i class="fa-solid fa-trash-can"></i>
                </button>
              </div>
            </td>

          </tr>
        @empty
          <tr>
            <td colspan="4" class="py-12 text-center text-slate-500">
              <div class="flex flex-col items-center justify-center space-y-3">
                <div class="w-16 h-16 rounded-full bg-slate-100 flex items-center justify-center text-slate-400 text-2xl">
                  <i class="fa-solid fa-feather"></i>
                </div>
                <div>
                  <p class="text-sm font-semibold text-slate-800">Belum Ada Artikel Blog</p>
                  <p class="text-xs text-slate-600 mt-0.5">Tulis artikel wawasan teknologi pertama untuk pembaca Anda.</p>
                </div>
                <a href="{{ route('admin.blogs.create') }}" class="px-4 py-2 rounded-xl bg-[#14433B] text-white text-xs font-semibold shadow-sm hover:bg-[#0B443C] transition-colors">
                  + Tulis Artikel Baru
                </a>
              </div>
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>

</div>

@endsection