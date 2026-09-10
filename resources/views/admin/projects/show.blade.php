@extends('layouts.admin')

@section('title', 'Detail Proyek ' . $project->name)

@section('content')
<div class="space-y-6">

  <!-- Header Section -->
  <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
    <div class="flex items-center space-x-3">
      <div class="w-12 h-12 rounded-2xl bg-[#14433B] text-white flex items-center justify-center font-bold text-lg">
        <i class="fa-solid fa-diagram-project text-xl"></i>
      </div>
      <div>
        <div class="flex items-center space-x-2">
          <h1 class="text-xl font-bold text-slate-800">{{ $project->name }}</h1>
          {!! $project->status_badge !!}
          {!! $project->priority_badge !!}
        </div>
        <p class="text-xs text-slate-500">
          No. Proyek: <strong class="text-slate-800">{{ $project->project_number ?? '-' }}</strong> • Klien: <span class="font-bold text-slate-800">{{ $project->client?->name ?? $project->client ?? 'Perorangan' }}</span>
        </p>
      </div>
    </div>

    <div class="flex items-center space-x-2">
      @if($project->quotation_id)
        <a href="{{ route('admin.quotations.show', $project->quotation_id) }}" class="px-4 py-2.5 rounded-xl bg-amber-100 text-amber-900 hover:bg-amber-200 text-xs font-semibold transition-colors flex items-center space-x-1.5">
          <i class="fa-solid fa-file-signature"></i>
          <span>Lihat Quotation</span>
        </a>
      @endif

      @if(Auth::guard('admin')->user()?->hasPermission('projects.edit'))
        <a href="{{ route('admin.projects.edit', $project->id) }}" class="px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold transition-colors">
          <i class="fa-solid fa-pen-to-square mr-1"></i> Edit Proyek
        </a>
      @endif

      <a href="{{ route('admin.projects.index') }}" class="px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold transition-colors">
        Kembali
      </a>
    </div>
  </div>

  <!-- Progress Bar & Key Stats -->
  <div class="bg-white rounded-2xl p-6 shadow-card border border-slate-100 space-y-4">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-xs">
      <div class="space-y-1">
        <span class="text-slate-500 font-bold uppercase tracking-wider text-[10px]">Penyelesaian Proyek</span>
        <div class="text-2xl font-black text-[#14433B]">{{ $project->progress }}%</div>
      </div>
      <div class="flex items-center space-x-6 text-xs text-slate-600">
        <div>
          <span class="text-slate-400 text-[11px] block">Budget Proyek</span>
          <span class="font-extrabold text-slate-800">Rp {{ number_format($project->budget, 0, ',', '.') }}</span>
        </div>
        <div>
          <span class="text-slate-400 text-[11px] block">Deadline</span>
          <span class="font-bold text-amber-600">{{ $project->deadline ? $project->deadline->format('d M Y') : '-' }}</span>
        </div>
        <div>
          <span class="text-slate-400 text-[11px] block">Penanggung Jawab</span>
          <span class="font-bold text-slate-800">{{ $project->assignedStaff?->name ?? 'Belum Ada' }}</span>
        </div>
      </div>
    </div>

    <div class="w-full h-3 rounded-full bg-slate-100 overflow-hidden">
      <div class="h-full bg-gradient-to-r from-[#14433B] to-[#21C9A4] rounded-full transition-all duration-500" style="width: {{ $project->progress }}%"></div>
    </div>
  </div>

  <!-- Tabs / Grid Sections: Tasks, Documents & Timeline -->
  <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    <!-- Left Column: Tasks & Documents -->
    <div class="lg:col-span-2 space-y-6">

      <!-- Tasks Management Card -->
      <div class="bg-white rounded-2xl p-6 shadow-card border border-slate-100 space-y-4" x-data="{ showAddTask: false }">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
          <h3 class="text-sm font-bold text-slate-800 flex items-center space-x-2">
            <i class="fa-solid fa-[#14433B] fa-list-check text-[#14433B]"></i>
            <span>Project Tasks ({{ $project->tasks->count() }})</span>
          </h3>
          @if(Auth::guard('admin')->user()?->hasPermission('projects.tasks'))
            <button @click="showAddTask = !showAddTask" class="px-3 py-1.5 rounded-xl bg-[#14433B] hover:bg-[#0B443C] text-white text-xs font-semibold transition-all">
              <i class="fa-solid fa-plus mr-1"></i> Tambah Task
            </button>
          @endif
        </div>

        <!-- Add Task Form -->
        <div x-show="showAddTask" x-transition class="p-4 rounded-xl bg-slate-50 border border-slate-200 space-y-3">
          <form method="POST" action="{{ route('admin.projects.tasks.store', $project->id) }}" class="space-y-3">
            @csrf
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
              <div>
                <label class="block text-[11px] font-bold text-slate-700 mb-1">Judul Task *</label>
                <input type="text" name="title" required placeholder="Judul tugas..." class="w-full text-xs rounded-xl border-slate-200 py-1.5">
              </div>
              <div>
                <label class="block text-[11px] font-bold text-slate-700 mb-1">Status *</label>
                <select name="status" required class="w-full text-xs rounded-xl border-slate-200 py-1.5">
                  <option value="todo">Todo</option>
                  <option value="in_progress">In Progress</option>
                  <option value="review">Review</option>
                  <option value="done">Done</option>
                </select>
              </div>
              <div>
                <label class="block text-[11px] font-bold text-slate-700 mb-1">Prioritas *</label>
                <select name="priority" required class="w-full text-xs rounded-xl border-slate-200 py-1.5">
                  <option value="low">Low</option>
                  <option value="normal" selected>Normal</option>
                  <option value="high">High</option>
                  <option value="urgent">Urgent</option>
                </select>
              </div>
              <div>
                <label class="block text-[11px] font-bold text-slate-700 mb-1">Assigned Staff</label>
                <select name="assigned_to" class="w-full text-xs rounded-xl border-slate-200 py-1.5">
                  <option value="">-- Staff --</option>
                  @foreach($admins as $adm)
                    <option value="{{ $adm->id }}">{{ $adm->name }}</option>
                  @endforeach
                </select>
              </div>
            </div>
            <div>
              <input type="text" name="description" placeholder="Deskripsi tugas singkat..." class="w-full text-xs rounded-xl border-slate-200 py-1.5">
            </div>
            <div class="flex justify-end space-x-2">
              <button type="button" @click="showAddTask = false" class="px-3 py-1.5 rounded-lg border text-xs font-semibold">Batal</button>
              <button type="submit" class="px-3 py-1.5 rounded-lg bg-[#14433B] text-white text-xs font-semibold">Simpan Task</button>
            </div>
          </form>
        </div>

        <!-- Task List -->
        <div class="space-y-2">
          @forelse($project->tasks as $task)
            <div class="p-3 rounded-xl border border-slate-100 hover:border-slate-200 bg-slate-50/40 flex items-center justify-between text-xs">
              <div class="flex items-center space-x-3">
                <form method="POST" action="{{ route('admin.projects.tasks.update', $task->id) }}">
                  @csrf
                  @method('PUT')
                  <input type="hidden" name="title" value="{{ $task->title }}">
                  <input type="hidden" name="priority" value="{{ $task->priority }}">
                  <input type="hidden" name="status" value="{{ $task->status === 'done' ? 'todo' : 'done' }}">
                  <button type="submit" class="w-5 h-5 rounded border flex items-center justify-center transition-colors {{ $task->status === 'done' ? 'bg-emerald-500 border-emerald-500 text-white' : 'border-slate-300 hover:border-[#14433B]' }}">
                    @if($task->status === 'done') <i class="fa-solid fa-check text-[10px]"></i> @endif
                  </button>
                </form>

                <div>
                  <span class="font-semibold text-slate-800 {{ $task->status === 'done' ? 'line-through text-slate-400' : '' }}">{{ $task->title }}</span>
                  <div class="text-[10px] text-slate-400">Assigned: {{ $task->assignee?->name ?? 'Unassigned' }} • {!! $task->priority_badge !!}</div>
                </div>
              </div>

              <div class="flex items-center space-x-2">
                <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase {{ $task->status === 'done' ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-700' }}">
                  {{ $task->status }}
                </span>
                @if(Auth::guard('admin')->user()?->hasPermission('projects.tasks'))
                  <form method="POST" action="{{ route('admin.projects.tasks.destroy', $task->id) }}">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="text-rose-500 hover:text-rose-700 p-1"><i class="fa-solid fa-trash-can"></i></button>
                  </form>
                @endif
              </div>
            </div>
          @empty
            <p class="text-xs text-slate-400 py-4 text-center">Belum ada tugas pada proyek ini.</p>
          @endforelse
        </div>
      </div>

      <!-- Documents Management Card -->
      <div class="bg-white rounded-2xl p-6 shadow-card border border-slate-100 space-y-4" x-data="{ showUpload: false }">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
          <h3 class="text-sm font-bold text-slate-800 flex items-center space-x-2">
            <i class="fa-solid fa-folder-open text-[#14433B]"></i>
            <span>Dokumen & Deliverables Proyek ({{ $project->documents->count() }})</span>
          </h3>
          @if(Auth::guard('admin')->user()?->hasPermission('projects.documents'))
            <button @click="showUpload = !showUpload" class="px-3 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold transition-colors">
              <i class="fa-solid fa-upload mr-1"></i> Unggah Dokumen
            </button>
          @endif
        </div>

        <!-- Upload Document Form -->
        <div x-show="showUpload" x-transition class="p-4 rounded-xl bg-slate-50 border border-slate-200 space-y-3">
          <form method="POST" action="{{ route('admin.projects.documents.store', $project->id) }}" enctype="multipart/form-data" class="space-y-3">
            @csrf
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
              <div>
                <label class="block text-[11px] font-bold text-slate-700 mb-1">Judul Dokumen *</label>
                <input type="text" name="title" required placeholder="Kontrak / Desain UI / Dokumentasi..." class="w-full text-xs rounded-xl border-slate-200 py-1.5">
              </div>
              <div>
                <label class="block text-[11px] font-bold text-slate-700 mb-1">Kategori *</label>
                <select name="category" required class="w-full text-xs rounded-xl border-slate-200 py-1.5">
                  <option value="contract">Contract</option>
                  <option value="quotation">Quotation</option>
                  <option value="invoice">Invoice</option>
                  <option value="design">Design</option>
                  <option value="documentation">Documentation</option>
                  <option value="deliverables">Deliverables</option>
                  <option value="other">Other</option>
                </select>
              </div>
            </div>
            <div>
              <label class="block text-[11px] font-bold text-slate-700 mb-1">Pilih File Dokumen * (Max 20MB, No Executables)</label>
              <input type="file" name="document" required class="w-full text-xs rounded-xl border-slate-200 py-1.5 bg-white">
            </div>
            <div class="flex justify-end space-x-2">
              <button type="button" @click="showUpload = false" class="px-3 py-1.5 rounded-lg border text-xs font-semibold">Batal</button>
              <button type="submit" class="px-3 py-1.5 rounded-lg bg-[#14433B] text-white text-xs font-semibold">Unggah File</button>
            </div>
          </form>
        </div>

        <!-- Documents Table -->
        <div class="overflow-x-auto">
          <table class="w-full text-left text-xs">
            <thead>
              <tr class="text-[11px] font-bold text-slate-400 uppercase border-b border-slate-100">
                <th class="pb-2">Judul Dokumen</th>
                <th class="pb-2">Kategori</th>
                <th class="pb-2">File</th>
                <th class="pb-2 text-right">Aksi</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 text-slate-700">
              @forelse($project->documents as $doc)
                <tr class="hover:bg-slate-50">
                  <td class="py-2.5 font-bold text-slate-900">{{ $doc->title }}</td>
                  <td class="py-2.5">
                    <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-slate-100 uppercase text-slate-700">{{ $doc->category }}</span>
                  </td>
                  <td class="py-2.5 text-slate-500">{{ $doc->file_name }} ({{ round($doc->file_size / 1024, 1) }} KB)</td>
                  <td class="py-2.5 text-right space-x-1">
                    <a href="{{ route('admin.projects.documents.download', $doc->id) }}" class="p-1 text-sky-600 hover:text-sky-800" title="Download"><i class="fa-solid fa-download"></i></a>
                    @if(Auth::guard('admin')->user()?->hasPermission('projects.documents'))
                      <form method="POST" action="{{ route('admin.projects.documents.destroy', $doc->id) }}" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="p-1 text-rose-500 hover:text-rose-700"><i class="fa-solid fa-trash-can"></i></button>
                      </form>
                    @endif
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="4" class="py-4 text-center text-slate-400">Belum ada dokumen yang diunggah.</td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>

    </div>

    <!-- Right Column: Project Details & Timeline -->
    <div class="space-y-6">

      <!-- Details Card -->
      <div class="bg-white rounded-2xl p-6 shadow-card border border-slate-100 space-y-4">
        <h3 class="text-sm font-bold text-slate-800 border-b border-slate-100 pb-3 flex items-center space-x-2">
          <i class="fa-solid fa-circle-info text-[#14433B]"></i>
          <span>Detail Proyek</span>
        </h3>

        <div class="space-y-3 text-xs">
          <div>
            <span class="text-slate-400 block text-[11px]">Tanggal Mulai</span>
            <span class="font-semibold text-slate-800">{{ $project->start_date ? $project->start_date->format('d M Y') : '-' }}</span>
          </div>
          <div>
            <span class="text-slate-400 block text-[11px]">Tanggal Selesai / Deadline</span>
            <span class="font-semibold text-slate-800">{{ $project->deadline ? $project->deadline->format('d M Y') : ($project->end_date ? $project->end_date->format('d M Y') : '-') }}</span>
          </div>
          <div>
            <span class="text-slate-400 block text-[11px]">Tim Pengembang</span>
            <div class="flex flex-wrap gap-1 mt-1">
              @forelse($project->team as $member)
                <span class="px-2 py-0.5 rounded-full bg-slate-100 text-slate-700 font-semibold text-[10px]">
                  {{ $member->name }} ({{ $member->pivot->role ?? 'Member' }})
                </span>
              @empty
                <span class="text-slate-400">Belum ada tim.</span>
              @endforelse
            </div>
          </div>
          <div>
            <span class="text-slate-400 block text-[11px]">Deskripsi Proyek</span>
            <div class="p-3 rounded-xl bg-slate-50 text-slate-700 whitespace-pre-line mt-1">{{ $project->description ?? 'Tidak ada deskripsi.' }}</div>
          </div>
        </div>
      </div>

      <!-- Activity Logs -->
      <div class="bg-white rounded-2xl p-6 shadow-card border border-slate-100 space-y-4">
        <h3 class="text-sm font-bold text-slate-800 border-b border-slate-100 pb-3 flex items-center space-x-2">
          <i class="fa-solid fa-clock-rotate-left text-[#14433B]"></i>
          <span>Timeline Aktivitas</span>
        </h3>

        <div class="space-y-3 text-xs">
          @forelse($activityLogs as $log)
            <div class="border-l-2 border-[#14433B] pl-3 py-1">
              <div class="font-semibold text-slate-800">{{ $log->description }}</div>
              <div class="text-[10px] text-slate-400">{{ $log->created_at->format('d M Y, H:i') }} • {{ $log->user_name ?? 'System' }}</div>
            </div>
          @empty
            <p class="text-xs text-slate-400">Belum ada catatan aktivitas proyek.</p>
          @endforelse
        </div>
      </div>

    </div>

  </div>

</div>
@endsection