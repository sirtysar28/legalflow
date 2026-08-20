# LegalFlow

**Platform Pengajuan, Review & Manajemen Dokumen Legal**

Aplikasi dibangun dengan **Laravel 11** (PHP >= 8.2) sesuai workflow pengajuan izin, pembuatan agreement, review legal, penyimpanan dokumen otomatis ke folder divisi, dan manajemen akses dokumen.

---

## 1. Fitur Utama

### Modul 1 — Submission Management
- **Pengajuan Izin** (Dashboard Perizinan) — pilih jenis izin, tiap jenis punya persyaratan dokumen berbeda.
- **Agreement Baru** (Dashboard Purchasing) — lengkap dengan **Check Supplier Assessment** (supplier harus terdaftar, assessment tersedia, data & dokumen lengkap).
- Form dinamis per jenis pengajuan (disimpan di `application_fields`).
- Upload dokumen dengan **versioning** (maks 10MB; pdf/doc/docx/xls/xlsx/jpg/png).
- Simpan Draft / Ajukan ke Legal, validasi kelengkapan otomatis saat pengajuan.
- Revisi: user memperbaiki data, mengganti/menambah dokumen, lalu mengajukan ulang.

### Modul 2 — Document Management
- Review Legal/Admin: **Setujui / Minta Revisi / Tolak** dengan catatan.
- Saat **APPROVED**: dokumen otomatis berstatus **ISSUED** dan tersimpan ke folder divisi:
  `Document Management/{Divisi}/{Perizinan|Agreement}/{Nomor Pengajuan}`.
- Timeline/audit trail lengkap di `application_histories` (semua aksi tercatat).
- Unduh dokumen terotorisasi via controller (bukan URL publik).

### Modul 3 — Document Access Management
- User lain dapat **Minta Akses** dokumen pengajuan yang sudah disetujui (wajib mengisi alasan).
- Admin/Legal menyetujui dengan **jenis akses (View Only / View+Download)** dan **masa berlaku**, atau menolak dengan alasan.
- Akses kedaluwarsa otomatis berubah `ACCESS_EXPIRED`.
- Guard duplikat permintaan (satu permintaan aktif per pengajuan per user).

### Lainnya
- 3 role: **User/Requester, Legal, Admin** (middleware `role:`).
- Notifikasi in-app (ikon lonceng) untuk semua kejadian penting.
- Admin: kelola user, divisi, jenis izin, persyaratan dokumen, supplier, dan **Audit Trail**.
- Dashboard statistik per role.
- Logo di header & halaman login (`public/images/logo_legalflow.jpg`), favicon (`public/favicon.png`).

---

## 2. Workflow Status

```
DRAFT ──submit──► SUBMITTED ──start_review──► UNDER_REVIEW ─┬─approve──► APPROVED (dokumen terbit)
                                    ▲                        ├─revision─► REVISION_REQUESTED
                                    │                        └─reject───► REJECTED
              REVISION_REQUESTED ─resubmit─► RESUBMITTED ────┘
```

Transisi status dikontrol terpusat di **`app/Services/ApplicationWorkflowService.php`**
(peta `TRANSITIONS`), bukan if/else di controller. Setiap transisi otomatis:
1. Update status + timestamp (`submitted_at`/`approved_at`/`rejected_at`)
2. Simpan history/audit trail (aktor, aksi, status lama → baru, catatan)
3. Simpan record review (untuk keputusan legal)
4. Kirim notifikasi database ke pihak terkait
5. Saat approve: terbitkan dokumen + set folder divisi otomatis

Status akses dokumen: `ACCESS_REQUESTED → ACCESS_APPROVED / ACCESS_REJECTED → ACCESS_EXPIRED`.

---

## 3. Cara Menjalankan

```bash
cd legalflow
composer install
cp .env.example .env          # lalu sesuaikan bila perlu
php artisan key:generate

# Database (default: SQLite — sudah langsung jalan)
touch database/database.sqlite
php artisan migrate --seed

php artisan serve
# buka http://127.0.0.1:8000
```

> Requirement: **PHP 8.2+**, Composer 2. Database default **SQLite** (zero-config).
> Untuk MySQL: ubah `DB_CONNECTION=mysql` + kredensial di `.env`.

### Akun Demo (password semuanya: `password`)

| Email | Role | Keterangan |
|---|---|---|
| `admin@legalflow.test` | Admin | Semua fitur + master data + audit trail |
| `legal@legalflow.test` | Legal | Review, keputusan, proses permintaan akses |
| `user@legalflow.test` | User | Pengajuan izin (Divisi A) |
| `budi@legalflow.test` | User | Purchasing — agreement |
| `citra@legalflow.test` | User | Divisi B — untuk demo minta akses dokumen |

---

## 4. Struktur Penting

```
app/
├── Enums/                        # ApplicationType, ApplicationStatus, AccessStatus
├── Models/                       # 12 model (workflow.txt section 15)
├── Services/
│   ├── ApplicationWorkflowService.php   # INTI: transisi status + history + notifikasi
│   └── AccessService.php                # Otorisasi akses/unduh dokumen
├── Notifications/ApplicationNotification.php
├── Http/
│   ├── Middleware/CheckRole.php         # alias middleware 'role:'
│   └── Controllers/
│       ├── Auth/LoginController.php
│       ├── DashboardController.php      # dashboard user vs legal/admin
│       ├── ApplicationController.php    # CRUD + submit/resubmit pengajuan
│       ├── DocumentController.php       # upload/hapus/unduh + browse dokumen terbit
│       ├── ReviewController.php         # antrean + keputusan legal
│       ├── AccessRequestController.php  # minta/proses akses dokumen
│       └── Admin/                       # users, departments, permit-types,
│                                        # requirements, suppliers, histories
config/legalflow.php              # definisi field form dinamis per jenis pengajuan
resources/views/                  # Blade + Bootstrap 5 (CDN)
```

### Tabel Database
`roles`, `departments`, `users`, `permit_types`, `suppliers`, `document_requirements`,
`applications`, `application_fields`, `documents`, `application_reviews`,
`application_histories`, `document_access_requests` + `notifications`.

---

## 5. Ringkasan Route

| Route | Role | Fungsi |
|---|---|---|
| `/dashboard` | semua | Dashboard statistik per role |
| `/applications?type=PERMIT` | user | Dashboard Perizinan |
| `/applications?type=AGREEMENT` | user | Dashboard Purchasing |
| `/applications/create?type=...` | user | Buat pengajuan izin / agreement |
| `/applications/{id}` | semua (sesuai hak) | Detail + timeline + aksi |
| `/applications/{id}/submit` | owner | Ajukan / ajukan ulang |
| `/review-queue` | legal, admin | Antrean review |
| `/applications/{id}/review/start` | legal, admin | Mulai review |
| `/applications/{id}/review/decide` | legal, admin | Approve / revisi / tolak |
| `/documents/browse` | semua | Dokumen terbit semua divisi |
| `/applications/{id}/access-requests` | user lain | Minta akses dokumen |
| `/access-requests/incoming` | legal, admin | Proses permintaan akses |
| `/admin/*` | admin | User, divisi, jenis izin, persyaratan, supplier, audit trail |

---

## 6. Catatan Pengembangan

- Form dinamis per jenis pengajuan diatur di `config/legalflow.php` (bisa ditambah field baru tanpa migrasi).
- Dokumen disimpan di disk `local` (private `storage/app/documents/{id}`) — unduh selalu lewat controller dengan cek akses.
- Semua aksi penting tercatat di `application_histories` (audit trail Admin → menu Audit Trail).
