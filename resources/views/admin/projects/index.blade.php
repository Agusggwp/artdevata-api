<!DOCTYPE html>
<html lang="id" class="h-full scroll-smooth">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Daftar Proyek - ARTDEVATA Admin</title>

  <!-- Tailwind CSS CDN -->
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      theme: {
        extend: {
          colors: {
            primary: '#4361ee',
            secondary: '#3a0ca3'
          }
        }
      }
    }
  </script>

  <!-- Google Fonts: Inter -->
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  
  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
</head>
<body class="h-full bg-gray-50 text-gray-800 font-sans">

  <div class="flex h-screen overflow-hidden">
    <!-- Main Content -->
    <div class="flex-1 flex flex-col overflow-hidden">
      <main class="flex-1 overflow-y-auto p-4 lg:p-8">
        <div class="max-w-7xl mx-auto">

          <!-- Header -->
          <div class="flex justify-between items-center mb-8">
            <h1 class="text-3xl font-bold text-gray-800">Daftar Proyek</h1>
            <div class="flex items-center gap-3">
              <a href="{{ route('admin.panel') }}" class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-100 text-sm flex items-center">
                <i class="fas fa-tachometer-alt mr-2"></i> Dashboard
              </a>
              <a href="{{ route('admin.projects.create') }}" class="px-6 py-3 bg-primary hover:bg-secondary text-white rounded-lg flex items-center">
                <i class="fas fa-plus mr-2"></i> Tambah Proyek
              </a>
            </div>
          </div>

          <!-- Success Message -->
          @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-6">
              {{ session('success') }}
            </div>
          @endif

          <!-- Table -->
          <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
            <div class="overflow-x-auto">
              <table class="w-full">
                <thead class="bg-primary text-white">
                  <tr>
                    <th class="px-6 py-4 text-left text-sm font-semibold">Nama Proyek</th>
                    <th class="px-6 py-4 text-left text-sm font-semibold">Klien</th>
                    <th class="px-6 py-4 text-left text-sm font-semibold">Status</th>
                    <th class="px-6 py-4 text-left text-sm font-semibold">Progress</th>
                    <th class="px-6 py-4 text-left text-sm font-semibold">Tim</th>
                    <th class="px-6 py-4 text-left text-sm font-semibold">Tanggal Mulai</th>
                    <th class="px-6 py-4 text-left text-sm font-semibold">Budget</th>
                    <th class="px-6 py-4 text-center text-sm font-semibold">Aksi</th>
                  </tr>
                </thead>
                <tbody>
                  @forelse($projects as $project)
                    <tr class="border-b border-gray-200 hover:bg-gray-50">
                      <td class="px-6 py-4">
                        <p class="font-semibold text-gray-800">{{ $project->name }}</p>
                      </td>
                      <td class="px-6 py-4 text-sm text-gray-600">{{ $project->client ?? '-' }}</td>
                      <td class="px-6 py-4">
                        {!! $project->status_badge !!}
                      </td>
                      <td class="px-6 py-4">
                        <div class="w-full bg-gray-200 rounded-full h-2">
                          <div class="bg-primary h-2 rounded-full" style="width: {{ $project->progress }}%"></div>
                        </div>
                        <p class="text-xs text-gray-600 mt-1">{{ $project->progress }}%</p>
                      </td>
                      <td class="px-6 py-4">
                        <div class="flex flex-wrap gap-2">
                          @forelse($project->team as $member)
                            <div class="flex flex-col">
                              <span class="px-3 py-1 bg-blue-100 text-blue-800 text-xs rounded-full font-semibold">
                                {{ $member->name }}
                              </span>
                              @if($member->pivot->role)
                                <span class="text-xs text-gray-600 mt-1">{{ $member->pivot->role }}</span>
                              @endif
                            </div>
                          @empty
                            <span class="text-gray-500 text-sm">-</span>
                          @endforelse
                        </div>
                      </td>
                      <td class="px-6 py-4 text-sm text-gray-600">
                        {{ $project->start_date ? $project->start_date->format('d M Y') : '-' }}
                      </td>
                      <td class="px-6 py-4 text-sm font-semibold text-gray-800">
                        Rp {{ number_format($project->budget, 0, ',', '.') }}
                      </td>
                      <td class="px-6 py-4 text-center space-x-2">
                        <a href="{{ route('admin.projects.show', $project) }}" class="text-blue-600 hover:underline">
                          <i class="fas fa-eye"></i>
                        </a>
                        <a href="{{ route('admin.projects.edit', $project) }}" class="text-yellow-600 hover:underline">
                          <i class="fas fa-edit"></i>
                        </a>
                        <form method="POST" action="{{ route('admin.projects.destroy', $project) }}" style="display:inline;" onsubmit="return confirm('Hapus proyek ini?');">
                          @csrf
                          @method('DELETE')
                          <button type="submit" class="text-red-600 hover:underline">
                            <i class="fas fa-trash"></i>
                          </button>
                        </form>
                      </td>
                    </tr>
                  @empty
                    <tr>
                      <td colspan="8" class="px-6 py-8 text-center text-gray-500">
                        <i class="fas fa-folder-open text-4xl mb-4"></i>
                        <p>Belum ada proyek</p>
                      </td>
                    </tr>
                  @endforelse
                </tbody>
              </table>
            </div>

            <!-- Pagination -->
            <div class="px-6 py-4 border-t border-gray-200">
              {{ $projects->links() }}
            </div>
          </div>

        </div>
      </main>
    </div>
  </div>

</body>
</html>