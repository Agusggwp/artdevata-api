@extends('layouts.admin')

@section('title', 'Kelola Proyek')

@section('content')

<!-- Header Bar -->
<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
  <div>
    <h1 class="text-xl font-bold text-slate-900">Daftar Proyek Software & IT</h1>
    <p class="text-xs text-slate-700 font-medium">Monitoring progres pengerjaan, tim pengembang, dan anggaran proyek</p>
  </div>
  
  <a href="{{ route('admin.projects.create') }}" class="px-4 py-2.5 rounded-xl bg-[#14433B] hover:bg-[#0B443C] text-white text-xs font-semibold shadow-md shadow-[#14433B]/20 transition-all duration-200 flex items-center space-x-2 w-fit">
    <i class="fa-solid fa-plus text-xs"></i>
    <span>Tambah Proyek Baru</span>
  </a>
</div>

<!-- Table Card -->
<div class="bg-white rounded-2xl shadow-card border border-slate-200 overflow-hidden">
  
  <div class="overflow-x-auto">
    <table class="w-full text-left border-collapse">
      <thead>
        <tr class="border-b border-slate-200 bg-slate-100/70 text-[11px] uppercase tracking-wider text-slate-700 font-bold">
          <th class="py-3.5 px-4 sm:px-6">Nama Proyek</th>
          <th class="py-3.5 px-4 sm:px-6">Klien</th>
          <th class="py-3.5 px-4 sm:px-6">Status & Progress</th>
          <th class="py-3.5 px-4 sm:px-6">Tim Pengembang</th>
          <th class="py-3.5 px-4 sm:px-6">Tanggal & Budget</th>
          <th class="py-3.5 px-4 sm:px-6 text-right">Aksi</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-slate-100 text-xs">
        @forelse($projects as $project)
          <tr class="hover:bg-slate-50 transition-colors">
            
            <!-- Project Name -->
            <td class="py-4 px-4 sm:px-6">
              <div class="flex items-center space-x-3">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-[#14433B] to-[#0E5D55] text-[#21C9A4] flex items-center justify-center font-bold text-sm shrink-0 shadow-xs">
                  <i class="fa-solid fa-diagram-project"></i>
                </div>
                <div>
                  <a href="{{ route('admin.projects.show', $project) }}" class="font-bold text-slate-900 hover:text-[#14433B] text-sm block">
                    {{ $project->name }}
                  </a>
                  <span class="text-[10px] text-slate-600 font-semibold">ID: #PRJ-{{ $project->id }}</span>
                </div>
              </div>
            </td>

            <!-- Client -->
            <td class="py-4 px-4 sm:px-6 text-slate-800 font-medium whitespace-nowrap">
              {{ $project->client ?? '-' }}
            </td>

            <!-- Status & Progress -->
            <td class="py-4 px-4 sm:px-6 min-w-[160px]">
              <div class="space-y-1.5">
                <div class="flex items-center justify-between">
                  @if($project->status === 'completed')
                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-900 border border-emerald-300">
                      Selesai
                    </span>
                  @elseif($project->status === 'ongoing')
                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-blue-100 text-blue-900 border border-blue-300">
                      Berjalan
                    </span>
                  @else
                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-900 border border-amber-300">
                      Tertunda
                    </span>
                  @endif
                  <span class="text-[11px] font-extrabold text-slate-900">{{ $project->progress }}%</span>
                </div>
                <div class="w-full bg-slate-200 h-2 rounded-full overflow-hidden">
                  <div class="bg-[#14433B] h-full rounded-full transition-all duration-300" style="width: {{ $project->progress }}%"></div>
                </div>
              </div>
            </td>

            <!-- Team Members -->
            <td class="py-4 px-4 sm:px-6 max-w-xs">
              <div class="flex flex-wrap gap-1">
                @forelse($project->team as $member)
                  <span class="inline-flex items-center px-2 py-0.5 bg-slate-100 text-slate-800 border border-slate-200 text-[10px] rounded-md font-semibold">
                    <i class="fa-solid fa-user-gear text-[8px] mr-1 text-[#14433B]"></i>
                    {{ $member->name }} {{ $member->pivot->role ? '('.$member->pivot->role.')' : '' }}
                  </span>
                @empty
                  <span class="text-slate-500 text-[11px] font-medium">-</span>
                @endforelse
              </div>
            </td>

            <!-- Start Date & Budget -->
            <td class="py-4 px-4 sm:px-6 whitespace-nowrap">
              <div class="flex flex-col">
                <span class="font-extrabold text-slate-900 text-xs">Rp {{ number_format($project->budget, 0, ',', '.') }}</span>
                <span class="text-[10px] text-slate-600 font-semibold">Mulai: {{ $project->start_date ? $project->start_date->format('d M Y') : '-' }}</span>
              </div>
            </td>

            <!-- Actions -->
            <td class="py-4 px-4 sm:px-6 text-right whitespace-nowrap">
              <div class="flex items-center justify-end space-x-2">
                <a href="{{ route('admin.projects.show', $project) }}" class="p-2 rounded-lg text-slate-700 hover:text-[#14433B] hover:bg-slate-100 transition-colors" title="Detail Proyek">
                  <i class="fa-solid fa-eye"></i>
                </a>
                <a href="{{ route('admin.projects.edit', $project) }}" class="p-2 rounded-lg text-slate-700 hover:text-amber-700 hover:bg-amber-50 transition-colors" title="Edit Proyek">
                  <i class="fa-solid fa-pen-to-square"></i>
                </a>
                <button type="button" onclick="confirmDelete('{{ route('admin.projects.destroy', $project) }}', 'Apakah Anda yakin ingin menghapus proyek \'{{ addslashes($project->name) }}\'?')" class="p-2 rounded-lg text-slate-600 hover:text-rose-600 hover:bg-rose-50 transition-colors" title="Hapus Proyek">
                  <i class="fa-solid fa-trash-can"></i>
                </button>
              </div>
            </td>

          </tr>
        @empty
          <tr>
            <td colspan="6" class="py-12 text-center text-slate-500">
              <div class="flex flex-col items-center justify-center space-y-3">
                <div class="w-16 h-16 rounded-full bg-slate-100 flex items-center justify-center text-slate-400 text-2xl">
                  <i class="fa-solid fa-diagram-project"></i>
                </div>
                <div>
                  <p class="text-sm font-semibold text-slate-800">Belum Ada Proyek</p>
                  <p class="text-xs text-slate-600 mt-0.5">Buat proyek baru untuk mulai alokasi tim dan anggaran.</p>
                </div>
                <a href="{{ route('admin.projects.create') }}" class="px-4 py-2 rounded-xl bg-[#14433B] text-white text-xs font-semibold shadow-sm hover:bg-[#0B443C] transition-colors">
                  + Tambah Proyek Baru
                </a>
              </div>
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  @if($projects->hasPages())
    <div class="px-6 py-4 border-t border-slate-100">
      {{ $projects->links() }}
    </div>
  @endif

</div>

@endsection