@extends('layouts.admin')

@section('title', 'Pengelolaan Gaji & Payroll')

@section('content')

<!-- Header Bar -->
<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
  <div>
    <h1 class="text-xl font-bold text-slate-900">Pengelolaan Gaji & Fee Tim</h1>
    <p class="text-xs text-slate-700 font-medium">Hitung & bayar komisi pengembang & QA berdasarkan proyek yang telah selesai</p>
  </div>
  
  <div class="flex items-center space-x-3">
    <div class="px-4 py-2 rounded-xl bg-amber-100/80 border border-amber-300 text-amber-950 text-xs font-bold">
      <span>Saldo Perusahaan: </span>
      <span class="font-black text-amber-900">Rp {{ number_format($companyBalance ?? 0, 0, ',', '.') }}</span>
    </div>
  </div>
</div>

<!-- Section 1: Summary Per Admin Card -->
<div class="bg-white rounded-2xl p-6 shadow-card border border-slate-200 mb-8">
  <h2 class="text-base font-bold text-slate-900 mb-4">Ringkasan Hak Gaji per Admin / Developer</h2>
  
  <div class="overflow-x-auto">
    <table class="w-full text-left border-collapse text-xs">
      <thead>
        <tr class="border-b border-slate-200 bg-slate-100/70 text-[11px] uppercase tracking-wider text-slate-700 font-bold">
          <th class="py-3.5 px-4">Nama Administrator / Tim</th>
          <th class="py-3.5 px-4">Total Hak Komisi</th>
          <th class="py-3.5 px-4">Terbayar</th>
          <th class="py-3.5 px-4">Sisa Hak Gaji</th>
          <th class="py-3.5 px-4">Status Proyek Selesai</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-slate-100">
        @forelse($perAdminTotals as $id => $amt)
          @php
            $projectsForAdmin = [];
            $projectsStatus = [];
            $totalPaidForAdmin = 0;
            foreach($perProject as $pp) {
              foreach($pp['qa_details'] as $d) {
                if($d['id'] == $id) {
                  $projectsForAdmin[$pp['id']] = $pp['name'];
                  $key = $pp['id'].'_'.$id;
                  $paid = ($payments[$key]->status ?? null) === 'paid';
                  $projectsStatus[$pp['id']] = $paid ? 'paid' : 'unpaid';
                  if($paid) $totalPaidForAdmin += (float) ($payments[$key]->amount ?? $d['amount']);
                }
              }
              foreach($pp['dev_details'] as $d) {
                if($d['id'] == $id) {
                  $projectsForAdmin[$pp['id']] = $pp['name'];
                  $key = $pp['id'].'_'.$id;
                  $paid = ($payments[$key]->status ?? null) === 'paid';
                  $projectsStatus[$pp['id']] = $paid ? 'paid' : 'unpaid';
                  if($paid) $totalPaidForAdmin += (float) ($payments[$key]->amount ?? $d['amount']);
                }
              }
            }
            $totalDue = $amt;
            $totalPaidForAdmin = round($totalPaidForAdmin, 2);
            $remaining = round($totalDue - $totalPaidForAdmin, 2);
          @endphp

          <tr class="hover:bg-slate-50 transition-colors">
            <td class="py-4 px-4">
              <div class="font-bold text-slate-900 text-sm">{{ $admins[$id]->name ?? 'User #'.$id }}</div>
              <div class="text-[11px] text-slate-600 font-medium">{{ $admins[$id]->email ?? '' }}</div>
            </td>

            <td class="py-4 px-4 font-black text-slate-900">
              Rp {{ number_format($totalDue, 0, ',', '.') }}
            </td>

            <td class="py-4 px-4 font-black text-emerald-700">
              Rp {{ number_format($totalPaidForAdmin, 0, ',', '.') }}
            </td>

            <td class="py-4 px-4 font-black {{ $remaining > 0 ? 'text-rose-700' : 'text-slate-600' }}">
              Rp {{ number_format($remaining, 0, ',', '.') }}
            </td>

            <td class="py-4 px-4">
              @if(count($projectsForAdmin) > 0)
                <div class="flex flex-wrap gap-1">
                  @foreach($projectsForAdmin as $pid => $pname)
                    <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-bold {{ ($projectsStatus[$pid] ?? 'unpaid') === 'paid' ? 'bg-emerald-100 text-emerald-900 border border-emerald-300' : 'bg-amber-100 text-amber-900 border border-amber-300' }}">
                      {{ $pname }} ({{ ($projectsStatus[$pid] ?? 'unpaid') === 'paid' ? 'Lunas' : 'Belum' }})
                    </span>
                  @endforeach
                </div>
              @else
                <span class="text-slate-500 italic text-[11px] font-medium">Belum ada komisi proyek</span>
              @endif
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="5" class="py-8 text-center text-slate-500 font-medium">
              Belum ada data akumulasi gaji admin.
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>

<!-- Section 2: Project Payout Breakdown Cards -->
<div class="space-y-6">
  <h2 class="text-base font-bold text-slate-900">Rincian Komisi Per Proyek Selesai</h2>

  @forelse($perProject as $p)
    <div class="bg-white rounded-2xl p-6 shadow-card border border-slate-200 space-y-6">
      
      <!-- Project Summary Row -->
      <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-4 border-b border-slate-200 gap-4">
        <div>
          <span class="text-[10px] font-bold text-slate-600 uppercase tracking-wider">Proyek Selesai</span>
          <h3 class="text-base font-bold text-slate-900">{{ $p['name'] }}</h3>
          <p class="text-xs text-slate-700 font-medium mt-0.5">Total Budget: <strong class="text-slate-900 font-black">Rp {{ number_format($p['budget'], 0, ',', '.') }}</strong></p>
        </div>

        <div class="flex items-center space-x-6 text-xs">
          <div class="p-2.5 rounded-xl bg-purple-100/70 border border-purple-200">
            <span class="text-[10px] text-purple-900 block font-bold">Alokasi QA (5%)</span>
            <span class="font-extrabold text-purple-950 text-sm">Rp {{ number_format($p['qa_share'], 0, ',', '.') }}</span>
          </div>
          <div class="p-2.5 rounded-xl bg-blue-100/70 border border-blue-200">
            <span class="text-[10px] text-blue-900 block font-bold">Alokasi Dev (25%)</span>
            <span class="font-extrabold text-blue-950 text-sm">Rp {{ number_format($p['dev_share'], 0, ',', '.') }}</span>
          </div>
        </div>
      </div>

      <!-- QA & Dev Members Payout Tables -->
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-xs">
        
        <!-- QA Team Payout -->
        <div class="p-4 rounded-xl bg-slate-50 border border-slate-200">
          <h4 class="font-bold text-slate-900 mb-3 flex items-center space-x-2">
            <i class="fa-solid fa-bug text-purple-700"></i>
            <span>Tim Quality Assurance (QA)</span>
          </h4>

          @if(count($p['qa_details']) > 0)
            <ul class="space-y-2.5">
              @foreach($p['qa_details'] as $d)
                @php $key = $p['id'].'_'.$d['id']; $paid = ($payments[$key]->status ?? null) === 'paid'; @endphp
                <li class="flex items-center justify-between p-2.5 rounded-lg bg-white border border-slate-200">
                  <div>
                    <span class="font-bold text-slate-900 block">{{ $d['name'] }}</span>
                    <span class="text-[10px] text-slate-600 font-semibold">{{ $d['role'] }}</span>
                  </div>
                  <div class="flex items-center space-x-3">
                    <span class="font-extrabold text-slate-900">Rp {{ number_format($d['amount'], 0, ',', '.') }}</span>
                    @if($paid)
                      <span class="px-2.5 py-1 text-[10px] font-bold bg-emerald-100 text-emerald-900 border border-emerald-300 rounded-md">Terbayar</span>
                    @else
                      <form method="POST" action="{{ route('admin.salaries.pay') }}" class="inline">
                        @csrf
                        <input type="hidden" name="project_id" value="{{ $p['id'] }}">
                        <input type="hidden" name="admin_id" value="{{ $d['id'] }}">
                        <input type="hidden" name="amount" value="{{ $d['amount'] }}">
                        <button type="submit" class="px-3 py-1 bg-[#14433B] hover:bg-[#0B443C] text-white text-[11px] font-bold rounded-lg shadow-xs transition-colors">Bayar</button>
                      </form>
                    @endif
                  </div>
                </li>
              @endforeach
            </ul>
          @else
            <p class="text-slate-500 italic text-[11px] font-medium py-2">Tidak ada anggota QA pada proyek ini.</p>
          @endif
        </div>

        <!-- Developer Team Payout -->
        <div class="p-4 rounded-xl bg-slate-50 border border-slate-200">
          <h4 class="font-bold text-slate-900 mb-3 flex items-center space-x-2">
            <i class="fa-solid fa-code text-blue-700"></i>
            <span>Tim Developer</span>
          </h4>

          @if(count($p['dev_details']) > 0)
            <ul class="space-y-2.5">
              @foreach($p['dev_details'] as $d)
                @php $key = $p['id'].'_'.$d['id']; $paid = ($payments[$key]->status ?? null) === 'paid'; @endphp
                <li class="flex items-center justify-between p-2.5 rounded-lg bg-white border border-slate-200">
                  <div>
                    <span class="font-bold text-slate-900 block">{{ $d['name'] }}</span>
                    <span class="text-[10px] text-slate-600 font-semibold">{{ $d['role'] }}</span>
                  </div>
                  <div class="flex items-center space-x-3">
                    <span class="font-extrabold text-slate-900">Rp {{ number_format($d['amount'], 0, ',', '.') }}</span>
                    @if($paid)
                      <span class="px-2.5 py-1 text-[10px] font-bold bg-emerald-100 text-emerald-900 border border-emerald-300 rounded-md">Terbayar</span>
                    @else
                      <form method="POST" action="{{ route('admin.salaries.pay') }}" class="inline">
                        @csrf
                        <input type="hidden" name="project_id" value="{{ $p['id'] }}">
                        <input type="hidden" name="admin_id" value="{{ $d['id'] }}">
                        <input type="hidden" name="amount" value="{{ $d['amount'] }}">
                        <button type="submit" class="px-3 py-1 bg-[#14433B] hover:bg-[#0B443C] text-white text-[11px] font-bold rounded-lg shadow-xs transition-colors">Bayar</button>
                      </form>
                    @endif
                  </div>
                </li>
              @endforeach
            </ul>
          @else
            <p class="text-slate-500 italic text-[11px] font-medium py-2">Tidak ada developer pada proyek ini.</p>
          @endif
        </div>

      </div>

    </div>
  @empty
    <div class="bg-white rounded-2xl p-12 text-center text-slate-500 shadow-card border border-slate-200">
      <i class="fa-solid fa-money-check-dollar text-4xl mb-3 text-slate-400"></i>
      <p class="text-sm font-bold text-slate-800">Belum Ada Proyek Selesai</p>
      <p class="text-xs text-slate-600 font-medium mt-0.5">Komisi dan gaji otomatis dihitung ketika proyek diubah statusnya menjadi Selesai (Completed).</p>
    </div>
  @endforelse
</div>

@endsection