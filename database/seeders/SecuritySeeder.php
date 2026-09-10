<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class SecuritySeeder extends Seeder
{
    public function run(): void
    {
        // 1. Define Permissions by Module
        $permissions = [
            // Dashboard & Overview
            ['name' => 'View Dashboard', 'slug' => 'dashboard.view', 'module' => 'Dashboard', 'description' => 'Akses melihat halaman utama dashboard'],

            // Admin Management
            ['name' => 'View Admins', 'slug' => 'admins.view', 'module' => 'Admins', 'description' => 'Melihat daftar administrator'],
            ['name' => 'Create Admins', 'slug' => 'admins.create', 'module' => 'Admins', 'description' => 'Menambahkan administrator baru'],
            ['name' => 'Edit Admins', 'slug' => 'admins.edit', 'module' => 'Admins', 'description' => 'Mengubah data & status administrator'],
            ['name' => 'Delete Admins', 'slug' => 'admins.delete', 'module' => 'Admins', 'description' => 'Menghapus akun administrator'],

            // Roles & Permissions
            ['name' => 'Manage Roles', 'slug' => 'roles.manage', 'module' => 'Roles & Permissions', 'description' => 'Mengatur peran / role pengguna'],
            ['name' => 'Manage Permissions', 'slug' => 'permissions.manage', 'module' => 'Roles & Permissions', 'description' => 'Mengatur matriks izin akses'],

            // CRM / Leads
            ['name' => 'View Leads', 'slug' => 'leads.view', 'module' => 'CRM & Leads', 'description' => 'Melihat daftar lead prospek'],
            ['name' => 'Create Leads', 'slug' => 'leads.create', 'module' => 'CRM & Leads', 'description' => 'Menambahkan lead prospek baru'],
            ['name' => 'Edit Leads', 'slug' => 'leads.edit', 'module' => 'CRM & Leads', 'description' => 'Mengubah data lead'],
            ['name' => 'Delete Leads', 'slug' => 'leads.delete', 'module' => 'CRM & Leads', 'description' => 'Menghapus data lead'],
            ['name' => 'Convert Leads', 'slug' => 'leads.convert', 'module' => 'CRM & Leads', 'description' => 'Mengkonversi lead menjadi client'],

            // Quotations
            ['name' => 'View Quotations', 'slug' => 'quotations.view', 'module' => 'Quotations', 'description' => 'Melihat daftar penawaran harga'],
            ['name' => 'Create Quotations', 'slug' => 'quotations.create', 'module' => 'Quotations', 'description' => 'Membuat quotation penawaran baru'],
            ['name' => 'Edit Quotations', 'slug' => 'quotations.edit', 'module' => 'Quotations', 'description' => 'Mengubah rincian penawaran'],
            ['name' => 'Delete Quotations', 'slug' => 'quotations.delete', 'module' => 'Quotations', 'description' => 'Menghapus penawaran'],
            ['name' => 'Send Quotations', 'slug' => 'quotations.send', 'module' => 'Quotations', 'description' => 'Mengirim quotation ke klien'],
            ['name' => 'Approve Quotations', 'slug' => 'quotations.approve', 'module' => 'Quotations', 'description' => 'Menyetujui & konversi quotation ke project'],

            // Projects
            ['name' => 'View Projects', 'slug' => 'projects.view', 'module' => 'Projects', 'description' => 'Melihat daftar proyek'],
            ['name' => 'Create Projects', 'slug' => 'projects.create', 'module' => 'Projects', 'description' => 'Membuat proyek baru'],
            ['name' => 'Edit Projects', 'slug' => 'projects.edit', 'module' => 'Projects', 'description' => 'Mengedit data & status proyek'],
            ['name' => 'Delete Projects', 'slug' => 'projects.delete', 'module' => 'Projects', 'description' => 'Menghapus data proyek'],
            ['name' => 'Kanban Projects', 'slug' => 'projects.kanban', 'module' => 'Projects', 'description' => 'Melihat dan mengatur papan Kanban proyek'],
            ['name' => 'Manage Project Tasks', 'slug' => 'projects.tasks', 'module' => 'Projects', 'description' => 'Mengelola task & tugas proyek'],
            ['name' => 'Manage Project Documents', 'slug' => 'projects.documents', 'module' => 'Projects', 'description' => 'Mengunggah & mengelola dokumen proyek'],

            // Services
            ['name' => 'View Services', 'slug' => 'services.view', 'module' => 'Services', 'description' => 'Melihat daftar layanan'],
            ['name' => 'Create Services', 'slug' => 'services.create', 'module' => 'Services', 'description' => 'Menambahkan layanan baru'],
            ['name' => 'Edit Services', 'slug' => 'services.edit', 'module' => 'Services', 'description' => 'Mengubah detail layanan'],
            ['name' => 'Delete Services', 'slug' => 'services.delete', 'module' => 'Services', 'description' => 'Menghapus layanan'],

            // Portfolios
            ['name' => 'View Portfolios', 'slug' => 'portfolios.view', 'module' => 'Portfolios', 'description' => 'Melihat daftar portofolio'],
            ['name' => 'Create Portfolios', 'slug' => 'portfolios.create', 'module' => 'Portfolios', 'description' => 'Menambahkan portofolio baru'],
            ['name' => 'Edit Portfolios', 'slug' => 'portfolios.edit', 'module' => 'Portfolios', 'description' => 'Mengubah karya portofolio'],
            ['name' => 'Delete Portfolios', 'slug' => 'portfolios.delete', 'module' => 'Portfolios', 'description' => 'Menghapus portofolio'],

            // Blogs
            ['name' => 'View Blogs', 'slug' => 'blogs.view', 'module' => 'Blogs', 'description' => 'Melihat daftar artikel blog'],
            ['name' => 'Create Blogs', 'slug' => 'blogs.create', 'module' => 'Blogs', 'description' => 'Menulis berita / artikel baru'],
            ['name' => 'Edit Blogs', 'slug' => 'blogs.edit', 'module' => 'Blogs', 'description' => 'Mengubah konten artikel'],
            ['name' => 'Delete Blogs', 'slug' => 'blogs.delete', 'module' => 'Blogs', 'description' => 'Menghapus artikel blog'],

            // Clients
            ['name' => 'View Clients', 'slug' => 'clients.view', 'module' => 'Clients', 'description' => 'Melihat daftar & Client 360'],
            ['name' => 'Create Clients', 'slug' => 'clients.create', 'module' => 'Clients', 'description' => 'Menambahkan data klien baru'],
            ['name' => 'Edit Clients', 'slug' => 'clients.edit', 'module' => 'Clients', 'description' => 'Mengubah profil klien'],
            ['name' => 'Delete Clients', 'slug' => 'clients.delete', 'module' => 'Clients', 'description' => 'Menghapus klien'],

            // Documentations
            ['name' => 'View Documentations', 'slug' => 'documentations.view', 'module' => 'Documentations', 'description' => 'Melihat galeri dokumentasi'],
            ['name' => 'Create Documentations', 'slug' => 'documentations.create', 'module' => 'Documentations', 'description' => 'Menambahkan foto dokumentasi'],
            ['name' => 'Edit Documentations', 'slug' => 'documentations.edit', 'module' => 'Documentations', 'description' => 'Mengubah dokumentasi'],
            ['name' => 'Delete Documentations', 'slug' => 'documentations.delete', 'module' => 'Documentations', 'description' => 'Menghapus foto dokumentasi'],

            // Invoices
            ['name' => 'View Invoices', 'slug' => 'invoices.view', 'module' => 'Invoices', 'description' => 'Melihat daftar invoice tagihan'],
            ['name' => 'Create Invoices', 'slug' => 'invoices.create', 'module' => 'Invoices', 'description' => 'Membuat invoice baru'],
            ['name' => 'Edit Invoices', 'slug' => 'invoices.edit', 'module' => 'Invoices', 'description' => 'Mengubah status / rincian invoice'],
            ['name' => 'Delete Invoices', 'slug' => 'invoices.delete', 'module' => 'Invoices', 'description' => 'Menghapus invoice'],

            // Finance & Transactions
            ['name' => 'View Finance', 'slug' => 'finance.view', 'module' => 'Finance', 'description' => 'Melihat catatan kas transaksi'],
            ['name' => 'Create Finance', 'slug' => 'finance.create', 'module' => 'Finance', 'description' => 'Mencatat transaksi pemasukan/pengeluaran'],
            ['name' => 'Edit Finance', 'slug' => 'finance.edit', 'module' => 'Finance', 'description' => 'Mengubah data transaksi'],
            ['name' => 'Delete Finance', 'slug' => 'finance.delete', 'module' => 'Finance', 'description' => 'Menghapus catatan transaksi'],

            // Salaries
            ['name' => 'View Salaries', 'slug' => 'salaries.view', 'module' => 'Salaries', 'description' => 'Melihat riwayat penggajian'],
            ['name' => 'Create Salaries', 'slug' => 'salaries.create', 'module' => 'Salaries', 'description' => 'Memproses pencairan gaji'],
            ['name' => 'Edit Salaries', 'slug' => 'salaries.edit', 'module' => 'Salaries', 'description' => 'Mengubah data gaji'],
            ['name' => 'Delete Salaries', 'slug' => 'salaries.delete', 'module' => 'Salaries', 'description' => 'Menghapus catatan gaji'],

            // Reports, Activity Logs & Security
            ['name' => 'View Reports', 'slug' => 'reports.view', 'module' => 'Security & Logs', 'description' => 'Melihat laporan bisnis'],
            ['name' => 'View Activity Logs', 'slug' => 'activity_logs.view', 'module' => 'Security & Logs', 'description' => 'Melihat log aktivitas administrator'],
            ['name' => 'View Login History', 'slug' => 'login_history.view', 'module' => 'Security & Logs', 'description' => 'Melihat riwayat login'],
            ['name' => 'View Security Dashboard', 'slug' => 'security.view', 'module' => 'Security & Logs', 'description' => 'Melihat status keamanan sistem'],
            ['name' => 'View Settings', 'slug' => 'settings.view', 'module' => 'Settings', 'description' => 'Melihat pengaturan aplikasi'],
            ['name' => 'Edit Settings', 'slug' => 'settings.edit', 'module' => 'Settings', 'description' => 'Mengubah konfigurasi sistem'],
        ];

        $permissionModels = [];
        foreach ($permissions as $perm) {
            $permissionModels[$perm['slug']] = Permission::firstOrCreate(
                ['slug' => $perm['slug']],
                $perm
            );
        }

        // 2. Define Roles
        $roles = [
            'super-admin' => [
                'name' => 'Super Admin',
                'description' => 'Akses penuh tanpa batas ke seluruh sistem dan keamanan',
                'permissions' => array_keys($permissionModels), // All permissions
            ],
            'manager' => [
                'name' => 'Manager',
                'description' => 'Akses manajemen operasional bisnis, CRM, penawaran, keuangan, proyek, dan konten',
                'permissions' => [
                    'dashboard.view',
                    'leads.view', 'leads.create', 'leads.edit', 'leads.delete', 'leads.convert',
                    'quotations.view', 'quotations.create', 'quotations.edit', 'quotations.delete', 'quotations.send', 'quotations.approve',
                    'projects.view', 'projects.create', 'projects.edit', 'projects.delete', 'projects.kanban', 'projects.tasks', 'projects.documents',
                    'clients.view', 'clients.create', 'clients.edit', 'clients.delete',
                    'services.view', 'services.create', 'services.edit', 'services.delete',
                    'portfolios.view', 'portfolios.create', 'portfolios.edit', 'portfolios.delete',
                    'blogs.view', 'blogs.create', 'blogs.edit', 'blogs.delete',
                    'documentations.view', 'documentations.create', 'documentations.edit', 'documentations.delete',
                    'invoices.view', 'invoices.create', 'invoices.edit', 'invoices.delete',
                    'finance.view', 'finance.create', 'finance.edit', 'finance.delete',
                    'reports.view',
                ],
            ],
            'staff' => [
                'name' => 'Staff',
                'description' => 'Akses operasional lead, proyek, klien, dan dokumentasi',
                'permissions' => [
                    'dashboard.view',
                    'leads.view', 'leads.create', 'leads.edit',
                    'projects.view', 'projects.edit', 'projects.kanban', 'projects.tasks', 'projects.documents',
                    'clients.view',
                    'documentations.view', 'documentations.create', 'documentations.edit',
                ],
            ],
            'finance' => [
                'name' => 'Finance',
                'description' => 'Akses khusus quotation, invoice, pencatatan transaksi kas, dan penggajian',
                'permissions' => [
                    'dashboard.view',
                    'clients.view',
                    'quotations.view', 'quotations.create', 'quotations.edit',
                    'invoices.view', 'invoices.create', 'invoices.edit', 'invoices.delete',
                    'finance.view', 'finance.create', 'finance.edit', 'finance.delete',
                    'salaries.view', 'salaries.create', 'salaries.edit', 'salaries.delete',
                    'reports.view',
                ],
            ],
            'technician' => [
                'name' => 'Technician',
                'description' => 'Akses teknisi proyek, task, dan pengunggahan dokumentasi kerja',
                'permissions' => [
                    'dashboard.view',
                    'projects.view', 'projects.edit', 'projects.kanban', 'projects.tasks', 'projects.documents',
                    'documentations.view', 'documentations.create', 'documentations.edit',
                ],
            ],
        ];

        $superAdminRole = null;
        foreach ($roles as $slug => $roleData) {
            $role = Role::firstOrCreate(
                ['slug' => $slug],
                ['name' => $roleData['name'], 'description' => $roleData['description']]
            );

            if ($slug === 'super-admin') {
                $superAdminRole = $role;
            }

            // Sync permissions
            $permissionIds = [];
            foreach ($roleData['permissions'] as $pSlug) {
                if (isset($permissionModels[$pSlug])) {
                    $permissionIds[] = $permissionModels[$pSlug]->id;
                }
            }
            $role->permissions()->sync($permissionIds);
        }

        // 3. Assign Super Admin Role to Default Admin Accounts
        if ($superAdminRole) {
            Admin::whereNull('role_id')->update(['role_id' => $superAdminRole->id, 'status' => 'active']);
        }
    }
}
