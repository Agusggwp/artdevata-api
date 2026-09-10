@extends('layouts.admin')

@section('title', 'Detail Proyek - ' . $project->name)

@section('content')

<div class="max-w-4xl mx-auto space-y-6">
  
  <!-- Header Bar -->
  <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div class="flex items-center space-x-3">
      <div class="w-12 h-12 rounded-2xl bg-gradient-to-tr from-[#14433B] to-[#0E5D55] text-[#21C9A4] flex items-center justify-center font-bold text-xl shadow-xs">
        <i class="fa-solid fa-diagram-project"></i>
      </div>
      <div>
        <h1 class="text-2xl font-black text-slate-900 tracking-tight">{{ $project->name }}</h1>
        <p class="text-xs text-slate-500">ID Proyek: #PRJ-{{ $project->id }} • Klien: {{ $project->client ?? '-' }}</p>
      </div>
    </div>

    <div class="flex items-center space-x-2">
      <a href="{{ route('admin.projects.edit', $project) }}" class="px-4 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-600 text-white text-xs font-semibold shadow-xs transition-colors flex items-center space-x-2">
        <i class="fa-solid fa-pen-to-square text-xs"></i>
        <span>Edit Proyek</span>
      </a>
      <a href="{{ route('admin.projects.index') }}" class="px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold transition-colors flex items-center space-x-2">
        <i class="fa-solid fa-arrow-left text-xs"></i>
        <span>Kembali</span>
      </a>
    </div>
  </div>

  <!-- Key Metrics & Overview Grid -->
  <div class="bg-white rounded-2xl p-6 shadow-card border border-slate-100">
    
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-6 mb-8">
      
      <!-- Status -->
      <div class="space-y-1">
        <span class="text-[11px] font-semibold uppercase tracking-wider text-slate-400">Status Pengerjaan</span>
        <div>
          @if($project->status === 'completed')
            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200/60">
              <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 mr-1.5"></span> Selesai
            </span>
          @elseif($project->status === 'ongoing')
            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-blue-50 text-blue-700 border border-blue-200/60">
              <span class="w-1.5 h-1.5 rounded-full bg-blue-500 mr-1.5"></span> Sedang Berjalan
            </span>
          @else
            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-amber-50 text-amber-700 border border-amber-200/60">
              <span class="w-1.5 h-1.5 rounded-full bg-amber-500 mr-1.5"></span> Tertunda
            </span>
          @endif
        </div>
      </div>

      <!-- Budget -->
      <div class="space-y-1">
        <span class="text-[11px] font-semibold uppercase tracking-wider text-slate-400">Anggaran (Budget)</span>
        <div class="text-lg font-bold text-slate-900">
          Rp {{ number_format($project->budget, 0, ',', '.') }}
        </div>
      </div>

      <!-- Start Date -->
      <div class="space-y-1">
        <span class="text-[11px] font-semibold uppercase tracking-wider text-slate-400">Tanggal Mulai</span>
        <div class="text-xs font-semibold text-slate-800">
          {{ $project->start_date ? $project->start_date->format('d M Y') : '-' }}
        </div>
      </div>

      <!-- End Date -->
      <div class="space-y-1">
        <span class="text-[11px] font-semibold uppercase tracking-wider text-slate-400">Target Selesai</span>
        <div class="text-xs font-semibold text-slate-800">
          {{ $project->end_date ? $project->end_date->format('d M Y') : '-' }}
        </div>
      </div>

    </div>

    <!-- Progress Bar -->
    <div class="p-4 rounded-xl bg-slate-50 border border-slate-100 mb-6">
      <div class="flex items-center justify-between mb-2">
        <span class="text-xs font-bold text-slate-800">Pencapaian Milestones (Progress)</span>
        <span class="text-sm font-black text-[#14433B]">{{ $project->progress }}%</span>
      </div>
      <div class="w-full bg-slate-200 h-2.5 rounded-full overflow-hidden">
        <div class="bg-gradient-to-r from-[#14433B] to-[#21C9A4] h-full rounded-full transition-all duration-300" style="width: {{ $project->progress }}%"></div>
      </div>
    </div>

    <!-- Description -->
    <div class="border-t border-slate-100 pt-6">
      <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-2">Deskripsi & Catatan Proyek</h3>
      <p class="text-xs text-slate-700 leading-relaxed whitespace-pre-line">{{ $project->description ?? 'Belum ada deskripsi spesifik.' }}</p>
    </div>

  </div>

  <!-- Team Members Card -->
  <div class="bg-white rounded-2xl p-6 shadow-card border border-slate-100">
    <h2 class="text-base font-bold text-slate-900 mb-4">Tim Pengembang (Project Team)</h2>
    
    @if($project->team->count() > 0)
      <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        @foreach($project->team as $member)
          <div class="flex items-center space-x-3.5 p-3.5 rounded-xl bg-slate-50 border border-slate-100">
            <div class="w-10 h-10 rounded-full bg-[#14433B] text-[#21C9A4] font-bold text-sm flex items-center justify-center shrink-0">
              {{ strtoupper(substr($member->name, 0, 1)) }}
            </div>
            <div class="flex-1 truncate">
              <span class="font-bold text-slate-900 text-xs block truncate">{{ $member->name }}</span>
              <span class="text-[10px] text-slate-500 block truncate">{{ $member->email }}</span>
            </div>
            @if($member->pivot->role)
              <span class="px-2.5 py-1 rounded-md bg-emerald-50 text-[#14433B] border border-emerald-100 text-[10px] font-semibold shrink-0">
                {{ $member->pivot->role }}
              </span>
            @endif
          </div>
        @endforeach
      </div>
    @else
      <div class="py-8 text-center text-slate-400">
        <i class="fa-solid fa-users text-3xl mb-2 text-slate-300"></i>
        <p class="text-xs">Belum ada anggota tim yang ditugaskan untuk proyek ini.</p>
      </div>
    @endif
  </div>

</div>

@endsection