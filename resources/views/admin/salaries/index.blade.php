<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width,initial-scale=1"/>
  <title>Pengelolaan Gaji - ARTDEVATA Admin</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
</head>
<body class="bg-gray-50 text-gray-800 min-h-screen font-sans p-6">
  <div class="max-w-6xl mx-auto">
    <div class="flex items-center justify-between mb-6">
      <h1 class="text-2xl font-bold">Pengelolaan Gaji</h1>
      <div class="flex items-center gap-3">
        <div class="px-4 py-2 bg-white rounded shadow text-sm">Saldo Perusahaan: <strong>Rp {{ number_format($companyBalance ?? 0,0,',','.') }}</strong></div>
        <a href="{{ route('admin.panel') }}" class="px-4 py-2 border rounded-lg hover:bg-gray-100">
          <i class="fas fa-arrow-left mr-2"></i> Kembali ke Dashboard
        </a>
      </div>
    </div>

    <section class="mb-8">
      <h2 class="font-semibold mb-3">Ringkasan Per Admin</h2>
      <div class="bg-white p-4 rounded shadow">
        @if(count($perAdminTotals) === 0)
          <p class="text-sm text-gray-500">Belum ada data gaji.</p>
        @else
          <table class="w-full text-sm">
            <thead>
              <tr class="text-left text-xs text-gray-500">
                <th class="py-2">Admin</th>
                <th class="py-2">Total Gaji</th>
                <th class="py-2">Terbayar</th>
                <th class="py-2">Sisa</th>
                <th class="py-2">Proyek Selesai</th>
              </tr>
            </thead>
            <tbody>
              @foreach($perAdminTotals as $id => $amt)
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

                <tr class="border-t align-top">
                  <td class="py-3">
                    {{ $admins[$id]->name ?? 'User #'.$id }}
                    @if(count($projectsForAdmin) > 0)
                      <div class="text-xs text-gray-500 mt-1">
                        <strong>Proyek:</strong>
                        @foreach($projectsForAdmin as $pid => $pname)
                          <span class="inline-block @if(($projectsStatus[$pid] ?? 'unpaid')==='paid') bg-green-100 text-green-800 @else bg-yellow-100 text-yellow-800 @endif px-2 py-0.5 rounded text-xs mr-1">
                            {{ $pname }}
                          </span>
                        @endforeach
                      </div>
                    @else
                      <div class="text-xs text-gray-400 mt-1">Belum menyelesaikan proyek</div>
                    @endif
                  </td>

                  <td class="py-3 font-semibold">Rp {{ number_format($totalDue,0,',','.') }}</td>
                  <td class="py-3 text-green-700 font-semibold">Rp {{ number_format($totalPaidForAdmin,0,',','.') }}</td>
                  <td class="py-3 text-red-600 font-semibold">Rp {{ number_format($remaining,0,',','.') }}</td>

                  <td class="py-3 text-sm text-gray-600">
                    @if(count($projectsForAdmin) > 0)
                      <ul class="list-inside">
                        @foreach($projectsForAdmin as $pid => $pname)
                          <li class="flex items-center justify-between py-1">
                            <span>{{ $pname }}</span>
                            @if(($projectsStatus[$pid] ?? 'unpaid') === 'paid')
                              <span class="px-2 py-0.5 text-xs bg-green-100 text-green-800 rounded">Terbayar</span>
                            @else
                              <span class="px-2 py-0.5 text-xs bg-yellow-100 text-yellow-800 rounded">Belum</span>
                            @endif
                          </li>
                        @endforeach
                      </ul>
                    @else
                      -
                    @endif
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>
        @endif
      </div>
    </section>

    <section>
      <h2 class="font-semibold mb-3">Detail Per Proyek (Selesai)</h2>
      <div class="space-y-4">
        @forelse($perProject as $p)
          <div class="bg-white p-4 rounded shadow">
            <div class="flex justify-between items-center mb-3">
              <div>
                <div class="text-sm text-gray-500">Proyek</div>
                <div class="font-bold">{{ $p['name'] }}</div>
                <div class="text-xs text-gray-500">Budget: Rp {{ number_format($p['budget'],0,',','.') }}</div>
              </div>
              <div class="text-right">
                <div class="text-xs text-gray-500">QA (5%):</div>
                <div class="font-semibold">Rp {{ number_format($p['qa_share'],0,',','.') }}</div>
                <div class="text-xs text-gray-500 mt-2">Dev (25%):</div>
                <div class="font-semibold">Rp {{ number_format($p['dev_share'],0,',','.') }}</div>
              </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <div class="text-sm font-medium mb-2">QA</div>
                @if(count($p['qa_details'])>0)
                  <ul class="space-y-2">
                    @foreach($p['qa_details'] as $d)
                      @php $key = $p['id'].'_'.$d['id']; $paid = ($payments[$key]->status ?? null) === 'paid'; @endphp
                      <li class="flex justify-between items-center">
                        <span>{{ $d['name'] }} <small class="text-xs text-gray-500">({{ $d['role'] }})</small></span>
                        <div class="flex items-center gap-2">
                          <span class="font-medium">Rp {{ number_format($d['amount'],0,',','.') }}</span>
                          @if($paid)
                            <span class="px-2 py-1 text-xs bg-green-100 text-green-700 rounded">Terbayar</span>
                          @else
                            <form method="POST" action="{{ route('admin.salaries.pay') }}">
                              @csrf
                              <input type="hidden" name="project_id" value="{{ $p['id'] }}">
                              <input type="hidden" name="admin_id" value="{{ $d['id'] }}">
                              <input type="hidden" name="amount" value="{{ $d['amount'] }}">
                              <button type="submit" class="px-3 py-1 bg-blue-600 text-white rounded text-sm">Bayar</button>
                            </form>
                          @endif
                        </div>
                      </li>
                    @endforeach
                  </ul>
                @else
                  <div class="text-sm text-gray-500">Belum ada QA.</div>
                @endif
              </div>

              <div>
                <div class="text-sm font-medium mb-2">Developer</div>
                @if(count($p['dev_details'])>0)
                  <ul class="space-y-2">
                    @foreach($p['dev_details'] as $d)
                      @php $key = $p['id'].'_'.$d['id']; $paid = ($payments[$key]->status ?? null) === 'paid'; @endphp
                      <li class="flex justify-between items-center">
                        <span>{{ $d['name'] }} <small class="text-xs text-gray-500">({{ $d['role'] }})</small></span>
                        <div class="flex items-center gap-2">
                          <span class="font-medium">Rp {{ number_format($d['amount'],0,',','.') }}</span>
                          @if($paid)
                            <span class="px-2 py-1 text-xs bg-green-100 text-green-700 rounded">Terbayar</span>
                          @else
                            <form method="POST" action="{{ route('admin.salaries.pay') }}">
                              @csrf
                              <input type="hidden" name="project_id" value="{{ $p['id'] }}">
                              <input type="hidden" name="admin_id" value="{{ $d['id'] }}">
                              <input type="hidden" name="amount" value="{{ $d['amount'] }}">
                              <button type="submit" class="px-3 py-1 bg-blue-600 text-white rounded text-sm">Bayar</button>
                            </form>
                          @endif
                        </div>
                      </li>
                    @endforeach
                  </ul>
                @else
                  <div class="text-sm text-gray-500">Belum ada Developer.</div>
                @endif
              </div>
            </div>
          </div>
        @empty
          <div class="text-sm text-gray-500">Belum ada proyek selesai.</div>
        @endforelse
      </div>
    </section>
  </div>
</body>
</html>