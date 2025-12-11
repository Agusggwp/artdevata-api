<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width,initial-scale=1"/>
  <title>Riwayat Saldo - ARTDEVATA Admin</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
</head>
<body class="bg-gray-50 text-gray-800 min-h-screen font-sans p-6">
  <div class="max-w-6xl mx-auto">
    <div class="flex items-center justify-between mb-6">
      <h1 class="text-2xl font-bold">Riwayat Saldo Perusahaan</h1>
      <div class="flex items-center gap-3">
        <div class="px-4 py-2 bg-white rounded shadow text-sm">Saldo Sekarang: <strong>Rp {{ number_format($companyBalance ?? 0,0,',','.') }}</strong></div>
        <a href="{{ route('admin.panel') }}" class="px-4 py-2 border rounded hover:bg-gray-100 text-sm">Kembali</a>
      </div>
    </div>

    <form method="GET" class="mb-4 grid grid-cols-1 md:grid-cols-5 gap-2">
      <select name="type" class="px-3 py-2 border rounded">
        <option value="">Semua Tipe</option>
        <option value="credit" {{ $qType==='credit' ? 'selected' : '' }}>Credit (Tambah)</option>
        <option value="debit" {{ $qType==='debit' ? 'selected' : '' }}>Debit (Kurangi)</option>
      </select>
      <input type="date" name="from" value="{{ $qFrom ?? '' }}" class="px-3 py-2 border rounded"/>
      <input type="date" name="to" value="{{ $qTo ?? '' }}" class="px-3 py-2 border rounded"/>
      <input type="text" name="q" placeholder="Cari keterangan..." value="{{ $qSearch ?? '' }}" class="px-3 py-2 border rounded"/>
      <div class="flex items-center">
        <button type="submit" class="px-4 py-2 bg-primary text-white rounded">Filter</button>
      </div>
    </form>

    <div class="bg-white p-4 rounded shadow">
      <table class="w-full text-sm">
        <thead class="text-xs text-gray-500">
          <tr>
            <th class="py-2 text-left">Waktu</th>
            <th class="py-2 text-left">Oleh</th>
            <th class="py-2 text-left">Tipe</th>
            <th class="py-2 text-left">Deskripsi</th>
            <th class="py-2 text-right">Jumlah</th>
            <th class="py-2 text-right">Saldo Setelah</th>
          </tr>
        </thead>
        <tbody>
          @forelse($transactions as $t)
            <tr class="border-t">
              <td class="py-3 text-xs text-gray-600">{{ $t->created_at->format('d M Y H:i') }}</td>
              <td class="py-3">{{ $t->admin->name ?? 'System' }}</td>
              <td class="py-3">
                <span class="px-2 py-1 text-xs rounded {{ $t->type === 'credit' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                  {{ ucfirst($t->type) }}
                </span>
              </td>
              <td class="py-3 text-sm text-gray-600">{{ $t->description }}</td>
              <td class="py-3 text-right font-semibold">{{ $t->type === 'credit' ? '+' : '-' }} Rp {{ number_format($t->amount,0,',','.') }}</td>
              <td class="py-3 text-right">{{ $t->balance_after ? 'Rp '.number_format($t->balance_after,0,',','.') : '-' }}</td>
            </tr>
          @empty
            <tr><td colspan="6" class="py-4 text-center text-gray-500">Belum ada transaksi.</td></tr>
          @endforelse
        </tbody>
      </table>

      <div class="mt-4">
        {{ $transactions->links() }}
      </div>
    </div>
  </div>
</body>
</html>