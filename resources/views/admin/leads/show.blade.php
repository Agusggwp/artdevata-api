@extends('layouts.admin')

@section('title', 'Detail Lead ' . $lead->name)

@section('content')
<div class="space-y-6">

  <!-- Header Section -->
  <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
    <div class="flex items-center space-x-3">
      <div class="w-12 h-12 rounded-2xl bg-[#14433B] text-white flex items-center justify-center font-bold text-lg">
        {{ strtoupper(substr($lead->name, 0, 1)) }}
      </div>
      <div>
        <div class="flex items-center space-x-2">
          <h1 class="text-xl font-bold text-slate-800">{{ $lead->name }}</h1>
          {!! $lead->status_badge !!}
        </div>
        <p class="text-xs text-slate-500">{{ $lead->company_name ?? 'Perorangan' }} • Sumber: <span class="uppercase font-semibold text-slate-700">{{ $lead->source }}</span></p>
      </div>
    </div>

    <div class="flex items-center space-x-2">
      @if($lead->client_id)
        <a href="{{ route('admin.clients.show', $lead->client_id) }}" class="px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold shadow-sm transition-all flex items-center space-x-1.5">
          <i class="fa-solid fa-user-check"></i>
          <span>Lihat Client 360°</span>
        </a>
      @elseif(Auth::guard('admin')->user()?->hasPermission('leads.convert'))
        <form method="POST" action="{{ route('admin.leads.convert', $lead->id) }}">
          @csrf
          <button type="submit" class="px-4 py-2.5 rounded-xl bg-sky-600 hover:bg-sky-700 text-white text-xs font-semibold shadow-sm transition-all flex items-center space-x-1.5">
            <i class="fa-solid fa-arrow-right-to-bracket"></i>
            <span>Convert to Client</span>
          </button>
        </form>
      @endif

      @if(Auth::guard('admin')->user()?->hasPermission('leads.edit'))
        <a href="{{ route('admin.leads.edit', $lead->id) }}" class="px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold transition-colors">
          <i class="fa-solid fa-pen-to-square mr-1"></i> Edit
        </a>
      @endif

      <a href="{{ route('admin.leads.index') }}" class="px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold transition-colors">
        Kembali
      </a>
    </div>
  </div>

  <!-- Detail Information Cards -->
  <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

    <!-- Contact & Lead Details -->
    <div class="md:col-span-2 bg-white rounded-2xl p-6 shadow-card border border-slate-100 space-y-4">
      <h3 class="text-sm font-bold text-slate-800 border-b border-slate-100 pb-3 flex items-center space-x-2">
        <i class="fa-solid fa-id-card text-[#14433B]"></i>
        <span>Informasi Prospek</span>
      </h3>

      <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
        <div>
          <span class="text-slate-400 block text-[11px]">Email</span>
          <span class="font-semibold text-slate-800">{{ $lead->email ?? '-' }}</span>
        </div>
        <div>
          <span class="text-slate-400 block text-[11px]">Nomor Telepon / WhatsApp</span>
          <span class="font-semibold text-slate-800">{{ $lead->phone ?? '-' }}</span>
        </div>
        <div>
          <span class="text-slate-400 block text-[11px]">Layanan Diminati</span>
          <span class="font-semibold text-slate-800">{{ $lead->service_interest ?? '-' }}</span>
        </div>
        <div>
          <span class="text-slate-400 block text-[11px]">Estimasi Budget</span>
          <span class="font-bold text-[#14433B]">Rp {{ number_format($lead->estimated_budget, 0, ',', '.') }}</span>
        </div>
        <div class="sm:col-span-2">
          <span class="text-slate-400 block text-[11px]">Alamat</span>
          <span class="text-slate-700">{{ $lead->address ?? '-' }}</span>
        </div>
        <div class="sm:col-span-2">
          <span class="text-slate-400 block text-[11px]">Catatan Kebutuhan</span>
          <div class="p-3 rounded-xl bg-slate-50 text-slate-700 mt-1 whitespace-pre-line">{{ $lead->notes ?? 'Tidak ada catatan.' }}</div>
        </div>
      </div>
    </div>

    <!-- Staff Assignment & Schedule Card -->
    <div class="bg-white rounded-2xl p-6 shadow-card border border-slate-100 space-y-4">
      <h3 class="text-sm font-bold text-slate-800 border-b border-slate-100 pb-3 flex items-center space-x-2">
        <i class="fa-solid fa-user-gear text-[#14433B]"></i>
        <span>Penugasan & Follow Up</span>
      </h3>

      <div class="space-y-3 text-xs">
        <div>
          <span class="text-slate-400 block text-[11px]">Assigned Staff</span>
          <div class="font-semibold text-slate-800 mt-0.5 flex items-center space-x-2">
            <div class="w-6 h-6 rounded-full bg-[#14433B] text-white flex items-center justify-center text-[10px] font-bold">
              {{ strtoupper(substr($lead->assignedStaff?->name ?? 'A', 0, 1)) }}
            </div>
            <span>{{ $lead->assignedStaff?->name ?? 'Belum Ditugaskan' }}</span>
          </div>
        </div>

        <div>
          <span class="text-slate-400 block text-[11px]">Jadwal Follow Up Berikutnya</span>
          <div class="font-bold text-amber-600 mt-0.5">
            @if($lead->next_follow_up_at)
              <i class="fa-regular fa-calendar-check mr-1"></i> {{ $lead->next_follow_up_at->format('d M Y - H:i') }}
            @else
              <span class="text-slate-400 font-normal">Belum dijadwalkan</span>
            @endif
          </div>
        </div>

        <div>
          <span class="text-slate-400 block text-[11px]">Tanggal Dibuat</span>
          <span class="text-slate-600">{{ $lead->created_at->format('d M Y, H:i') }}</span>
        </div>
      </div>
    </div>

  </div>

</div>
@endsection
