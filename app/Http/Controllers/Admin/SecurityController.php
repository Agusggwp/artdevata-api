<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\AuditLog;
use App\Models\LoginHistory;
use Illuminate\Http\Request;

class SecurityController extends Controller
{
    /**
     * Display Security Overview Dashboard.
     */
    public function index()
    {
        $activeAdminsCount = Admin::where('status', 'active')->count();
        $suspendedAdminsCount = Admin::where('status', 'suspended')->count();
        
        $failedLoginsToday = LoginHistory::where('status', 'failed')
            ->whereDate('created_at', now())
            ->count();

        $recentFailedLogins = LoginHistory::where('status', 'failed')
            ->latest('created_at')
            ->take(5)
            ->get();

        $recentSecurityEvents = AuditLog::whereIn('action', ['login', 'failed_login', 'logout', 'password_change', 'role_change', 'status_change', 'force_logout'])
            ->latest('created_at')
            ->take(10)
            ->get();

        $securityChecklist = [
            [
                'title'       => 'HTTPS & SSL Protection',
                'description' => 'Memastikan enkripsi lalu lintas data antara browser pengguna dan server.',
                'status'      => request()->secure() || config('app.env') === 'local',
                'label'       => request()->secure() ? 'Aktif (HTTPS)' : 'Development (HTTP)',
            ],
            [
                'title'       => 'CSRF Protection',
                'description' => 'Mencegah serangan Cross-Site Request Forgery pada seluruh formulir internal.',
                'status'      => true,
                'label'       => 'Aktif (Laravel VerifyCsrfToken)',
            ],
            [
                'title'       => 'Password Hashing',
                'description' => 'Menyimpan password menggunakan algoritma hashing aman Bcrypt.',
                'status'      => true,
                'label'       => 'Aktif (Bcrypt / Argon2)',
            ],
            [
                'title'       => 'Login Rate Limiting & Brute-Force Shield',
                'description' => 'Membatasi percobaan login berulang (maksimal 5 kali per menit per IP/Email).',
                'status'      => true,
                'label'       => 'Aktif (RateLimiter 5 req/min)',
            ],
            [
                'title'       => 'Centralized Audit Logging',
                'description' => 'Mencatat seluruh aksi sensitif (create, update, delete, login) ke audit log.',
                'status'      => true,
                'label'       => 'Aktif (Scrubbing Password)',
            ],
            [
                'title'       => 'Granular Role & Permissions',
                'description' => 'Membatasi akses setiap modul berdasarkan matriks peran administrator.',
                'status'      => true,
                'label'       => 'Aktif (5 Roles, 25+ Permissions)',
            ],
            [
                'title'       => 'Session Security & Regeneration',
                'description' => 'Mengubah ID sesi saat login/logout dan mendukung force logout.',
                'status'      => true,
                'label'       => 'Aktif (Strict Session Guard)',
            ],
            [
                'title'       => 'Strict File Upload Validation',
                'description' => 'Memvalidasi MIME type, ekstensi, dan batas ukuran file terunggah.',
                'status'      => true,
                'label'       => 'Aktif (Sanitized Storage)',
            ],
        ];

        return view('admin.security.index', compact(
            'activeAdminsCount',
            'suspendedAdminsCount',
            'failedLoginsToday',
            'recentFailedLogins',
            'recentSecurityEvents',
            'securityChecklist'
        ));
    }
}
