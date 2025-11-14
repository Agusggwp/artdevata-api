<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Admin Baru</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #e0f7fa; min-height: 100vh; display: flex; align-items: center; }
        .card { max-width: 420px; margin: auto; border-radius: 15px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="card shadow">
            <div class="card-header bg-primary text-white text-center py-3">
                <h4>➕ Daftar Admin Baru</h4>
            </div>
            <div class="card-body p-4">
                <form method="POST" action="{{ route('admin.register') }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Nama</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Konfirmasi</label>
                        <input type="password" name="password_confirmation" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Buat Admin</button>
                </form>
                <div class="text-center mt-3">
                    <a href="{{ route('admin.login') }}" class="text-decoration-none">Sudah punya akun? Login</a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>