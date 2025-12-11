<!DOCTYPE html>
<html lang="id" class="h-full scroll-smooth">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>{{ $project->name }} - ARTDEVATA Admin</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
</head>
<body class="h-full bg-gray-50 text-gray-800 font-sans">

  <div class="flex h-screen overflow-hidden">
    <div class="flex-1 flex flex-col overflow-hidden">
      <main class="flex-1 overflow-y-auto p-4 lg:p-8">
        <div class="max-w-4xl mx-auto">

          <!-- Header -->
          <div class="flex justify-between items-center mb-8">
            <h1 class="text-3xl font-bold text-gray-800">{{ $project->name }}</h1>
            <div class="space-x-3">
              <a href="{{ route('admin.projects.edit', $project) }}" class="px-6 py-3 bg-yellow-500 hover:bg-yellow-600 text-white rounded-lg">
                <i class="fas fa-edit mr-2"></i> Edit
              </a>
              <a href="{{ route('admin.projects.index') }}" class="px-6 py-3 border border-gray-300 rounded-lg hover:bg-gray-100">
                <i class="fas fa-arrow-left mr-2"></i> Kembali
              </a>
            </div>
          </div>

          <!-- Main Info Card -->
          <div class="bg-white p-8 rounded-2xl shadow-lg mb-8">
            <div class="grid grid-cols-2 gap-8 mb-8">
              <!-- Status -->
              <div>
                <p class="text-sm text-gray-500 mb-2">Status</p>
                {!! $project->status_badge !!}
              </div>

              <!-- Klien -->
              <div>
                <p class="text-sm text-gray-500 mb-2">Klien</p>
                <p class="text-lg font-semibold text-gray-800">{{ $project->client ?? '-' }}</p>
              </div>

              <!-- Tanggal Mulai -->
              <div>
                <p class="text-sm text-gray-500 mb-2">Tanggal Mulai</p>
                <p class="text-lg font-semibold text-gray-800">{{ $project->start_date ? $project->start_date->format('d M Y') : '-' }}</p>
              </div>

              <!-- Tanggal Selesai -->
              <div>
                <p class="text-sm text-gray-500 mb-2">Tanggal Selesai</p>
                <p class="text-lg font-semibold text-gray-800">{{ $project->end_date ? $project->end_date->format('d M Y') : '-' }}</p>
              </div>

              <!-- Budget -->
              <div>
                <p class="text-sm text-gray-500 mb-2">Budget</p>
                <p class="text-lg font-semibold text-gray-800">Rp {{ number_format($project->budget, 0, ',', '.') }}</p>
              </div>

              <!-- Progress -->
              <div>
                <p class="text-sm text-gray-500 mb-2">Progress</p>
                <div class="flex items-center space-x-3">
                  <div class="flex-1 bg-gray-200 rounded-full h-3">
                    <div class="bg-blue-600 h-3 rounded-full" style="width: {{ $project->progress }}%"></div>
                  </div>
                  <span class="text-lg font-bold text-gray-800">{{ $project->progress }}%</span>
                </div>
              </div>
            </div>

            <!-- Deskripsi -->
            <div class="border-t pt-6">
              <p class="text-sm text-gray-500 mb-2">Deskripsi</p>
              <p class="text-gray-700 leading-relaxed">{{ $project->description ?? '-' }}</p>
            </div>
          </div>

          <!-- Tim Proyek -->
          <div class="bg-white p-8 rounded-2xl shadow-lg mb-8">
            <h2 class="text-2xl font-bold text-gray-800 mb-6">Tim Proyek</h2>
            @if($project->team->count() > 0)
              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @foreach($project->team as $member)
                  <div class="flex items-center p-4 bg-gray-50 rounded-lg border border-gray-200">
                    <div class="w-12 h-12 bg-blue-200 rounded-full flex items-center justify-center mr-4">
                      <i class="fas fa-user text-blue-600 text-lg"></i>
                    </div>
                    <div>
                      <p class="font-semibold text-gray-800">{{ $member->name }}</p>
                      <p class="text-sm text-gray-500">{{ $member->email }}</p>
                    </div>
                  </div>
                @endforeach
              </div>
            @else
              <p class="text-gray-500 text-center py-8">Belum ada anggota tim</p>
            @endif
          </div>

          <!-- Timeline Info -->
          <div class="bg-white p-8 rounded-2xl shadow-lg">
            <h2 class="text-2xl font-bold text-gray-800 mb-6">Informasi Tambahan</h2>
            <div class="space-y-4">
              <div class="flex justify-between items-center pb-4 border-b">
                <span class="text-gray-600">Dibuat Oleh</span>
                <span class="font-semibold text-gray-800">{{ $project->creator->name ?? 'N/A' }}</span>
              </div>
              <div class="flex justify-between items-center pb-4 border-b">
                <span class="text-gray-600">Tanggal Dibuat</span>
                <span class="font-semibold text-gray-800">{{ $project->created_at->format('d M Y H:i') }}</span>
              </div>
              <div class="flex justify-between items-center">
                <span class="text-gray-600">Terakhir Diperbarui</span>
                <span class="font-semibold text-gray-800">{{ $project->updated_at->format('d M Y H:i') }}</span>
              </div>
            </div>
          </div>

        </div>
      </main>
    </div>
  </div>

</body>
</html>