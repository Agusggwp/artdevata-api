@extends('layouts.admin')

@section('title', 'Project Kanban Board')

@section('content')
<div class="space-y-6">

  <!-- Header Section -->
  <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
    <div>
      <h1 class="text-xl font-bold text-slate-800">Project Kanban Board</h1>
      <p class="text-xs text-slate-500">Visualisasi alur pengerjaan proyek dari Planning hingga Selesai.</p>
    </div>

    <div class="flex items-center space-x-2">
      <a href="{{ route('admin.projects.index') }}" class="px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold transition-colors flex items-center space-x-1.5">
        <i class="fa-solid fa-list"></i>
        <span>Tampilan Tabel</span>
      </a>

      @if(Auth::guard('admin')->user()?->hasPermission('projects.create'))
        <a href="{{ route('admin.projects.create') }}" class="px-4 py-2.5 rounded-xl bg-[#14433B] hover:bg-[#0B443C] text-white text-xs font-semibold shadow-sm transition-all flex items-center space-x-1.5">
          <i class="fa-solid fa-plus"></i>
          <span>Buat Proyek</span>
        </a>
      @endif
    </div>
  </div>

  <!-- Kanban Board Columns Grid -->
  <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-5 gap-4 overflow-x-auto pb-4">
    
    @php
      $columnConfigs = [
        'planning'    => ['title' => 'Planning', 'icon' => 'fa-lightbulb', 'color' => 'sky'],
        'in_progress' => ['title' => 'In Progress', 'icon' => 'fa-[#14433B] fa-[#14433B] fa-spinner', 'color' => 'blue'],
        'review'      => ['title' => 'Review', 'icon' => 'fa-magnifying-glass font-bold', 'color' => 'purple'],
        'completed'   => ['title' => 'Completed', 'icon' => 'fa-circle-check', 'color' => 'emerald'],
        'on_hold'     => ['title' => 'On Hold', 'icon' => 'fa-pause-circle', 'color' => 'amber'],
      ];
    @endphp

    @foreach($columnConfigs as $colKey => $config)
      @php
        $projectsInCol = $kanbanColumns[$colKey] ?? collect();
      @endphp
      
      <div class="bg-slate-100/80 rounded-2xl p-3 flex flex-col space-y-3 min-w-[240px] border border-slate-200/60">
        
        <!-- Column Header -->
        <div class="flex items-center justify-between px-2 py-1">
          <div class="flex items-center space-x-2">
            <span class="w-2.5 h-2.5 rounded-full bg-{{ $config['color'] }}-500"></span>
            <h3 class="text-xs font-bold text-slate-800 uppercase tracking-wider">{{ $config['title'] }}</h3>
          </div>
          <span class="px-2 py-0.5 rounded-full bg-white text-slate-700 font-bold text-[10px] shadow-2xs">
            {{ $projectsInCol->count() }}
          </span>
        </div>

        <!-- Cards Container -->
        <div class="space-y-3 flex-1 overflow-y-auto max-h-[calc(100vh-260px)] p-1">
          @forelse($projectsInCol as $project)
            <div class="bg-white rounded-xl p-4 shadow-sm border border-slate-200/80 hover:shadow-card hover:border-[#14433B]/40 transition-all space-y-3">
              
              <div class="flex items-start justify-between gap-2">
                <a href="{{ route('admin.projects.show', $project->id) }}" class="font-bold text-slate-900 text-xs hover:text-[#14433B] block line-clamp-2">
                  {{ $project->name }}
                </a>
                {!! $project->priority_badge !!}
              </div>

              <div class="text-[11px] text-slate-500 font-medium">
                <i class="fa-regular fa-building text-slate-400 mr-1"></i> {{ $project->client?->name ?? $project->client ?? 'Perorangan' }}
              </div>

              <!-- Progress Bar -->
              <div class="space-y-1">
                <div class="flex justify-between text-[10px] text-slate-500 font-semibold">
                  <span>Progress</span>
                  <span>{{ $project->progress }}%</span>
                </div>
                <div class="w-full h-1.5 rounded-full bg-slate-100 overflow-hidden">
                  <div class="h-full bg-[#21C9A4] rounded-full" style="width: {{ $project->progress }}%"></div>
                </div>
              </div>

              <!-- Card Footer: Staff & Deadline -->
              <div class="flex items-center justify-between text-[10px] text-slate-500 pt-2 border-t border-slate-100">
                <div class="flex items-center space-x-1.5">
                  <div class="w-5 h-5 rounded-full bg-[#14433B] text-white font-bold flex items-center justify-center text-[9px]">
                    {{ strtoupper(substr($project->assignedStaff?->name ?? 'A', 0, 1)) }}
                  </div>
                  <span class="truncate max-w-[80px]">{{ $project->assignedStaff?->name ?? 'Unassigned' }}</span>
                </div>

                <div class="text-slate-400">
                  @if($project->deadline)
                    <i class="fa-regular fa-clock mr-0.5"></i> {{ $project->deadline->format('d M') }}
                  @endif
                </div>
              </div>

              <!-- Quick Status Switch Dropdown -->
              <div class="pt-1">
                <form method="POST" action="{{ route('admin.projects.update-status', $project->id) }}">
                  @csrf
                  <select name="status" onchange="this.form.submit()" class="w-full text-[10px] py-1 px-2 rounded-lg border-slate-200 bg-slate-50 text-slate-600 font-semibold focus:ring-0">
                    <option value="planning" {{ $project->status == 'planning' ? 'selected' : '' }}>-> Planning</option>
                    <option value="in_progress" {{ in_array($project->status, ['in_progress', 'ongoing']) ? 'selected' : '' }}>-> In Progress</option>
                    <option value="review" {{ $project->status == 'review' ? 'selected' : '' }}>-> Review</option>
                    <option value="completed" {{ $project->status == 'completed' ? 'selected' : '' }}>-> Completed</option>
                    <option value="on_hold" {{ in_array($project->status, ['on_hold', 'pending']) ? 'selected' : '' }}>-> On Hold</option>
                    <option value="cancelled" {{ $project->status == 'cancelled' ? 'selected' : '' }}>-> Cancelled</option>
                  </select>
                </form>
              </div>

            </div>
          @empty
            <div class="p-6 text-center text-slate-400 bg-white/50 border border-dashed border-slate-200 rounded-xl">
              <span class="text-[11px]">Kosong</span>
            </div>
          @endforelse
        </div>

      </div>
    @endforeach

  </div>

</div>
@endsection
