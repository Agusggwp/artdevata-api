# Dokumentasi REST API Backend - ArtDevata Admin

Selamat datang di Dokumentasi Resmi REST API **ArtDevata Backend**. API ini dibangun berbasis **Laravel 12** dan **Laravel Sanctum** untuk mendukung integrasi penuh dengan frontend web (React / Next.js / Vue), aplikasi mobile, maupun sistem pihak ketiga.

---

## 📌 Informasi Umum

- **Base URL API V1**: `http://localhost:8000/api/v1` (atau domain server backend Anda).
- **Format Payload & Respon**: `JSON` (`Content-Type: application/json`, `Accept: application/json`).
- **Autentikasi API**: Bearer Token via Header `Authorization: Bearer <TOKEN>`.

---

## 📦 Standardized JSON Response Format

Setiap respon API mengembalikan format JSON yang konsisten:

### 1. Respon Sukses (`200 OK`, `201 Created`)
```json
{
  "success": true,
  "message": "Pesan deskripsi tindakan sukses.",
  "data": { ... }
}
```

### 2. Respon Paginated Data (`200 OK`)
```json
{
  "success": true,
  "message": "Data berhasil diambil.",
  "data": [ ... ],
  "meta": {
    "current_page": 1,
    "last_page": 5,
    "per_page": 15,
    "total": 75
  },
  "links": {
    "first": "http://localhost:8000/api/v1/admin/leads?page=1",
    "last": "http://localhost:8000/api/v1/admin/leads?page=5",
    "next": "http://localhost:8000/api/v1/admin/leads?page=2",
    "prev": null
  }
}
```

### 3. Respon Gagal / Validasi (`400`, `401`, `403`, `404`, `422`, `500`)
```json
{
  "success": false,
  "message": "Validasi gagal.",
  "errors": {
    "email": ["Alamat email wajib diisi."]
  }
}
```

---

## 🔐 1. Authentication Endpoints (`/api/v1/auth/*`)

### A. Login Admin / User
- **Method & Endpoint**: `POST /api/v1/auth/login`
- **Headers**: `Accept: application/json`
- **Request Body**:
```json
{
  "email": "admin@artdevata.com",
  "password": "password123"
}
```
- **Response `200 OK`**:
```json
{
  "success": true,
  "message": "Login berhasil.",
  "data": {
    "token": "1|sanctum_token_string_here...",
    "admin": {
      "id": 1,
      "name": "Super Admin",
      "email": "admin@artdevata.com",
      "role": "Super Admin",
      "role_slug": "super-admin",
      "is_super": true
    }
  }
}
```

### B. Get Profile & Permissions
- **Method & Endpoint**: `GET /api/v1/auth/me`
- **Headers**: `Authorization: Bearer <TOKEN>`
- **Response `200 OK`**:
```json
{
  "success": true,
  "message": "Data profil berhasil diambil.",
  "data": {
    "id": 1,
    "name": "Super Admin",
    "email": "admin@artdevata.com",
    "status": "active",
    "role": "Super Admin",
    "role_slug": "super-admin",
    "is_super": true,
    "permissions": ["*"]
  }
}
```

### C. Change Password
- **Method & Endpoint**: `POST /api/v1/auth/change-password`
- **Headers**: `Authorization: Bearer <TOKEN>`
- **Request Body**:
```json
{
  "current_password": "oldpassword",
  "new_password": "newpassword123",
  "new_password_confirmation": "newpassword123"
}
```

### D. Logout (Revoke Token)
- **Method & Endpoint**: `POST /api/v1/auth/logout`
- **Headers**: `Authorization: Bearer <TOKEN>`

---

## 🌐 2. Public API Endpoints (`/api/v1/public/*`)

> Endpoint publik tidak memerlukan token autentikasi.

| Method | Endpoint | Deskripsi |
| :--- | :--- | :--- |
| `GET` | `/api/v1/public/services` | Mengambil daftar semua layanan jasa. |
| `GET` | `/api/v1/public/services/{id}` | Detail spesifik layanan jasa. |
| `GET` | `/api/v1/public/portfolios` | Mengambil karya portofolio. Query param: `?category=Web` |
| `GET` | `/api/v1/public/portfolios/{id}` | Detail spesifik portofolio. |
| `GET` | `/api/v1/public/blogs` | Mengambil artikel blog publik. Query param: `?category=SEO&page=1` |
| `GET` | `/api/v1/public/blogs/{idOrSlug}` | Detail artikel berdasarkan ID atau Slug URL. |
| `GET` | `/api/v1/public/clients` | Mengambil daftar klien aktif. |
| `GET` | `/api/v1/public/documentations` | Mengambil dokumentasi publik perusahaan. |
| `POST` | `/api/v1/public/contact` | Mengirim formulir kontak dari website (Otomatis menjadi Lead prospek baru). |

**Contoh Payload Send Lead / Contact Form (`POST /api/v1/public/contact`)**:
```json
{
  "name": "Budi Santoso",
  "company_name": "PT Jaya Sentosa",
  "email": "budi@jayasentosa.com",
  "phone": "081234567890",
  "service_interest": "Pengembangan E-Commerce Custom",
  "estimated_budget": 15000000,
  "notes": "Halo, saya ingin konsultasi mengenai pembuatan sistem web e-commerce B2B."
}
```

---

## 🏢 3. Protected Admin APIs (`/api/v1/admin/*`)

> Memerlukan Header: `Authorization: Bearer <TOKEN>`

### 3.1 Dashboard & Metrics Overview
- **Endpoint**: `GET /api/v1/admin/dashboard`
- **Deskripsi**: Mengembalikan ringkasan lengkap metrik keuangan, statistik proyek, status pipeline leads, dan transaksi terbaru.

### 3.2 Global Search (Ctrl + K)
- **Endpoint**: `GET /api/v1/admin/global-search?q={keyword}`
- **Deskripsi**: Mencari data lintas modul (Leads, Clients, Quotations, Projects, Invoices).

### 3.3 Notifikasi Bisnis
- **`GET /api/v1/admin/notifications`**: Mengambil daftar notifikasi mendesak (Lead follow up, Quotation expired, Project deadline, Invoice overdue).
- **`POST /api/v1/admin/notifications/mark-read`**: Tandai semua notifikasi telah dibaca.

---

### 3.4 CRM & Leads Management (`/api/v1/admin/leads`)

- **Daftar Leads**: `GET /api/v1/admin/leads?search={text}&status={new|contacted|qualified|negotiation|won|lost}`
- **Tambah Lead Baru**: `POST /api/v1/admin/leads`
  ```json
  {
    "name": "Dewi Lestari",
    "company_name": "Studio Creative",
    "email": "dewi@creative.com",
    "phone": "08987654321",
    "address": "Denpasar, Bali",
    "source": "instagram",
    "service_interest": "UI/UX Design & Branding",
    "estimated_budget": 8000000,
    "status": "new",
    "assigned_to": 2,
    "next_follow_up_at": "2026-09-15"
  }
  ```
- **Detail Lead**: `GET /api/v1/admin/leads/{id}`
- **Update Lead**: `PUT /api/v1/admin/leads/{id}`
- **Hapus Lead**: `DELETE /api/v1/admin/leads/{id}`
- **Konversi Lead ke Client**: `POST /api/v1/admin/leads/{id}/convert` (Otomatis mengubah status lead menjadi `won` dan membuat entri Client baru).

---

### 3.5 Quotations / Penawaran Harga (`/api/v1/admin/quotations`)

- **Daftar Quotation**: `GET /api/v1/admin/quotations?status={draft|sent|viewed|accepted|rejected}`
- **Buat Quotation**: `POST /api/v1/admin/quotations`
  ```json
  {
    "client_id": 1,
    "issue_date": "2026-09-11",
    "valid_until": "2026-09-25",
    "status": "sent",
    "discount": 500000,
    "tax": 0,
    "notes": "Penawaran harga sistem kasir pos.",
    "items": [
      {
        "service_id": 1,
        "description": "Pengembangan Fitur POS & Backend",
        "quantity": 1,
        "unit": "paket",
        "unit_price": 10000000,
        "discount": 0
      }
    ]
  }
  ```
- **Detail Quotation**: `GET /api/v1/admin/quotations/{id}`
- **Update Quotation**: `PUT /api/v1/admin/quotations/{id}`
- **Hapus Quotation**: `DELETE /api/v1/admin/quotations/{id}`
- **Buat Proyek dari Quotation**: `POST /api/v1/admin/quotations/{id}/create-project`

---

### 3.6 Projects, Kanban, Tasks, & Documents (`/api/v1/admin/projects`)

- **Daftar Proyek**: `GET /api/v1/admin/projects?status={planning|in_progress|review|completed|on_hold}`
- **Kanban Board Data**: `GET /api/v1/admin/projects/kanban` (Membagi proyek berdasarkan kolom kanban).
- **Buat Proyek**: `POST /api/v1/admin/projects`
- **Detail Proyek**: `GET /api/v1/admin/projects/{id}` (Lengkap dengan tim, tasks, dokumen, & log aktivitas).
- **Update Proyek**: `PUT /api/v1/admin/projects/{id}`
- **Hapus Proyek**: `DELETE /api/v1/admin/projects/{id}`
- **Update Status Proyek Quick**: `POST /api/v1/admin/projects/{id}/update-status` (`{"status": "completed"}`)

#### Sub-resource Tasks & Documents:
- **Tambah Task Proyek**: `POST /api/v1/admin/projects/{projectId}/tasks`
  ```json
  {
    "title": "Selesaikan Sliced UI Navbar & Sidebar",
    "assigned_to": 3,
    "priority": "high",
    "status": "in_progress",
    "due_date": "2026-09-18"
  }
  ```
- **Update Task**: `PUT /api/v1/admin/tasks/{id}`
- **Hapus Task**: `DELETE /api/v1/admin/tasks/{id}`
- **Unggah Dokumen Proyek**: `POST /api/v1/admin/projects/{projectId}/documents` (`multipart/form-data`)
  - Fields: `title`, `category` (`contract|quotation|invoice|design|deliverables|other`), `document` (File max 20MB).
- **Hapus Dokumen Proyek**: `DELETE /api/v1/admin/documents/{id}`

---

### 3.7 Invoices (`/api/v1/admin/invoices`)

- **Daftar Invoices**: `GET /api/v1/admin/invoices?status={draft|sent|paid|overdue|cancelled}`
- **Buat Invoice**: `POST /api/v1/admin/invoices`
  ```json
  {
    "client_name": "PT Jaya Sentosa",
    "client_email": "budi@jayasentosa.com",
    "client_id": 1,
    "project_id": 2,
    "invoice_date": "2026-09-11",
    "due_date": "2026-09-25",
    "status": "sent",
    "items": [
      {
        "description": "Pembayaran DP 50% Proyek Web E-Commerce",
        "quantity": 1,
        "price": 7500000
      }
    ]
  }
  ```
- **Update Status Invoice**: `PATCH /api/v1/admin/invoices/{id}/status` (`{"status": "paid"}`)
- **Hapus Invoice**: `DELETE /api/v1/admin/invoices/{id}`

---

### 3.8 Management Client / Klien (`/api/v1/admin/clients`)

- **Daftar Klien**: `GET /api/v1/admin/clients?status={active|inactive|prospect}`
- **Buat Klien**: `POST /api/v1/admin/clients`
- **Detail Klien (360 Financial View)**: `GET /api/v1/admin/clients/{id}`
- **Update Klien**: `PUT /api/v1/admin/clients/{id}`
- **Hapus Klien**: `DELETE /api/v1/admin/clients/{id}`

---

### 3.9 Services, Portfolios, & Blogs Management

| Modul | Method | Endpoint |
| :--- | :--- | :--- |
| **Services** | `GET/POST` | `/api/v1/admin/services` |
| | `GET/PUT/DELETE` | `/api/v1/admin/services/{id}` |
| **Portfolios** | `GET/POST` | `/api/v1/admin/portfolios` |
| | `GET/PUT/DELETE` | `/api/v1/admin/portfolios/{id}` |
| **Blogs** | `GET/POST` | `/api/v1/admin/blogs` |
| | `GET/PUT/DELETE` | `/api/v1/admin/blogs/{id}` |

---

### 3.10 Payroll, Kompensasi & Keuangan

- **Perhitungan Gaji & Kompensasi Tim Proyek**: `GET /api/v1/admin/salaries`
  - Menghitung secara otomatis pembagian insentif Developer (25% dari budget) & QA (5% dari budget) dari proyek yang `completed`.
- **Catat Pembayaran Gaji**: `POST /api/v1/admin/salaries/pay` (`{"project_id": 1, "admin_id": 3, "amount": 2500000}`)
- **Daftar Transaksi Keuangan Perusahaan**: `GET /api/v1/admin/finance/transactions?type={credit|debit}`
- **Entri Transaksi Keuangan**: `POST /api/v1/admin/finance/transaction` (`{"type": "credit", "amount": 5000000, "description": "Pembayaran Invoice Direct Client"}`)

---

### 3.11 User Admin & Security Management

- **Daftar Pengelola Admin**: `GET /api/v1/admin/users`
- **Tambah Admin User Baru**: `POST /api/v1/admin/users`
- **Force Logout Sesi Admin**: `POST /api/v1/admin/users/{id}/force-logout`
- **Daftar Roles & Permissions**: `GET /api/v1/admin/roles`, `GET /api/v1/admin/roles/permissions`
- **Audit Logs / Log Aktivitas**: `GET /api/v1/admin/activity-logs?module=Projects&action=update`
- **Login Histories**: `GET /api/v1/admin/login-histories?status=failed`

---

## 🛠️ Status HTTP Code Standard

| Status Code | Arti |
| :--- | :--- |
| `200 OK` | Permintaan berhasil diproses. |
| `201 Created` | Resource baru berhasil dibuat. |
| `400 Bad Request` | Permintaan salah atau parameter tidak lengkap. |
| `401 Unauthenticated` | Bearer Token tidak valid, kadaluarsa, atau tidak dikirim. |
| `403 Forbidden` | Akun dinonaktifkan atau Anda tidak memiliki izin (*permission*). |
| `404 Not Found` | Data atau endpoint tidak ditemukan. |
| `422 Unprocessable Entity` | Validasi input gagal. |
| `500 Server Error` | Kesalahan pada server backend. |
