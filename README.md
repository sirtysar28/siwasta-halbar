# 🛡️ SIWASTA-HALBAR

**Sistem Informasi Pengawasan dan Tindak Lanjut Kabupaten Halmahera Barat**

Sistem informasi berbasis web untuk mengelola, memonitor, mengevaluasi, dan mengendalikan proses tindak lanjut atas hasil pemeriksaan, audit, pengawasan, maupun rekomendasi dari berbagai lembaga (BPK, BPKP, Inspektorat, SPI, auditor internal).

Dibangun dengan **Laravel 11 + PHP 8.2** dengan **REST API** (Sanctum) dan antarmuka **Blade + Bootstrap 5 + Chart.js**.

---

## ✨ Fitur Utama

| Modul | Deskripsi |
|-------|-----------|
| 🔐 **Multi-Role Auth** | 9 role: Super Admin, Administrator, Auditor, Inspektorat, Kepala OPD, Operator OPD, Verifikator, Pimpinan, Guest |
| 📊 **Dashboard Pimpinan** | KPI (temuan, rekomendasi, selesai, lewat deadline, progress), grafik pie/bar/line, Top 10 OPD tercepat & terlambat, aktivitas terbaru |
| 🏢 **Master Data** | OPD, Auditor, Jenis Audit, Tahun Audit, Kategori Temuan |
| 📋 **Manajemen Audit** | Input audit lengkap dengan upload dokumen (surat, BA, pendukung) |
| 🔍 **Manajemen Temuan** | Temuan dengan OPD, nilai, kategori, tingkat risiko, status |
| ✅ **Manajemen Rekomendasi** | Rekomendasi dengan deadline, PIC, progress otomatis |
| 🔄 **Tindak Lanjut** | Operator OPD input tindak lanjut + upload bukti (PDF, foto, video) |
| 👁️ **Verifikasi** | Verifikator approve/revisi/reject dengan notifikasi otomatis ke OPD |
| 📁 **Manajemen Dokumen** | Repository dengan kategori, versioning, QR Code validasi |
| ⏰ **Early Warning System** | Reminder otomatis H-30/14/7/3/1, lewat deadline. Multi-media (Email/WA/Telegram) |
| 📈 **Monitoring** | Filter (tahun, OPD, auditor, status, deadline, risiko) + export PDF/Excel/CSV |
| 📑 **Laporan** | Rekap audit/temuan/rekomendasi/OPD/auditor/deadline + grafik tahunan/bulanan |
| 👥 **Manajemen User** | Role management, permission, reset password, aktivitas login, device |
| 🔔 **Notifikasi** | Inbox, broadcast, reminder, dengan dropdown real-time |
| 📝 **Audit Trail** | Log seluruh aktivitas (login, CRUD, upload, approval) + IP, browser, waktu |

---

## 🚀 Instalasi

### Persyaratan
- PHP >= 8.2
- Composer
- MySQL 8 / MariaDB
- Node.js & NPM (opsional untuk asset)

### Langkah Instalasi

```bash
# 1. Masuk folder project
cd siwasta-halbar

# 2. Install dependency
composer install

# 3. Konfigurasi environment
cp .env.example .env
php artisan key:generate

# 4. Edit .env - sesuaikan database
DB_DATABASE=siwasta_halbar
DB_USERNAME=root
DB_PASSWORD=

# 5. Buat database
mysql -u root -e "CREATE DATABASE siwasta_halbar CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

# 6. Jalankan migration + seeder
php artisan migrate:fresh --seed

# 7. Buat storage symlink (untuk file upload)
php artisan storage:link

# 8. Jalankan server
php artisan serve
```

Akses: **http://localhost:8000**

---

## 🔑 Akun Demo

| Role | Email | Password |
|------|-------|----------|
| Super Administrator | `admin@siwasta.go.id` | `password` |
| Inspektorat | `inspektorat@siwasta.go.id` | `password` |
| Verifikator | `verifikator@siwasta.go.id` | `password` |
| Operator OPD (Pendidikan) | `operator1@siwasta.go.id` | `password` |
| Operator OPD (Kesehatan) | `operator2@siwasta.go.id` | `password` |
| Pimpinan | `pimpinan@siwasta.go.id` | `password` |

---

## 🌐 REST API

Base URL: `http://localhost:8000/api/v1`

### Authentication
```bash
# Login
curl -X POST http://localhost:8000/api/v1/login \
  -H "Content-Type: application/json" \
  -d '{"email":"admin@siwasta.go.id","password":"password","device_name":"mobile"}'

# Response: { "data": { "token": "1|xxx...", "user": {...} } }
```

### Endpoint Utama
| Method | Endpoint | Deskripsi |
|--------|----------|-----------|
| POST | `/login` | Login & dapatkan token |
| POST | `/register` | Registrasi user baru |
| GET | `/profile` | Profil user (butuh token) |
| POST | `/logout` | Logout |
| GET/POST | `/opd` | List / buat OPD |
| GET/POST | `/auditor` | List / buat Auditor |
| GET/POST | `/jenis-audit` | List / buat Jenis Audit |
| GET/POST | `/tahun-audit` | List / buat Tahun Audit |
| GET/POST | `/kategori-temuan` | List / buat Kategori Temuan |
| GET/POST | `/audit` | CRUD Audit |
| POST | `/audit/{id}/dokumen` | Upload dokumen audit |
| GET/POST | `/temuan` | CRUD Temuan |
| GET/POST | `/rekomendasi` | CRUD Rekomendasi |
| GET/POST | `/tindak-lanjut` | CRUD Tindak Lanjut |
| POST | `/tindak-lanjut/{id}/bukti` | Upload bukti |
| POST | `/tindak-lanjut/{id}/verifikasi` | Verifikasi (approve/revisi/ditolak) |
| GET | `/dokumen` | List dokumen |
| GET | `/dokumen/{id}/download` | Download dokumen |
| GET | `/notifikasi` | List notifikasi |
| POST | `/notifikasi/{id}/read` | Tandai dibaca |
| GET | `/monitoring` | Monitoring tindak lanjut |
| GET | `/dashboard/statistik` | KPI dashboard |
| GET | `/dashboard/grafik` | Data grafik |
| GET | `/dashboard/progress` | Progress per OPD |
| GET | `/laporan/{type}` | Laporan (audit/temuan/rekomendasi/opd/auditor/deadline) |

Semua endpoint (kecuali login/register) memerlukan header: `Authorization: Bearer {token}`

---

## ⏰ Early Warning System (Scheduler)

Reminder otomatis berjalan setiap hari jam 08:00. Setup cron:

```bash
# Tambahkan ke crontab
* * * * * cd /path/to/siwasta-halbar && php artisan schedule:run >> /dev/null 2>&1
```

Atau jalankan manual:
```bash
php artisan siwasta:send-reminders
```

### Konfigurasi Media Notifikasi (`.env`)
```env
# WhatsApp Gateway (Fonnte)
WA_GATEWAY_URL=https://api.fonnte.com
WA_GATEWAY_TOKEN=your_token

# Telegram Bot
TELEGRAM_BOT_TOKEN=your_bot_token

# Email SMTP
MAIL_HOST=smtp.gmail.com
MAIL_USERNAME=your_email@gmail.com
MAIL_PASSWORD=your_app_password
```

---

## 🗄️ Struktur Database

```
users ─── roles (Spatie Permission)
  └── opd
       ├── audits ─── jenis_audit, tahun_audit, auditor
       │     ├── audit_opd (pivot)
       │     └── findings ─── kategori_temuan
       │           └── recommendations
       │                 ├── follow_ups
       │                 │     ├── documents (polymorphic)
       │                 │     ├── approvals (verifikasi)
       │                 │     └── comments
       │                 └── reminders (early warning)
       └── documents (polymorphic)

notifications_custom, activity_log, login_activities
```

---

## 🛠️ Teknologi

| Komponen | Teknologi |
|----------|-----------|
| Backend | Laravel 11, PHP 8.2 |
| Frontend | Blade, Bootstrap 5.3, Bootstrap Icons, Chart.js, DataTables, Select2 |
| Database | MySQL 8 / MariaDB |
| Auth & API | Laravel Sanctum |
| Role & Permission | Spatie Laravel Permission |
| Activity Log | Spatie Activity Log |
| PDF | DomPDF (barryvdh/laravel-dompdf) |
| Excel | Laravel Excel (maatwebsite) |
| QR Code | Simple QR Code |
| Scheduler | Laravel Task Scheduler |

---

## 📂 Struktur Folder Penting

```
app/
├── Http/Controllers/
│   ├── Api/           # REST API controllers
│   └── Master/        # Master data controllers
├── Models/            # Eloquent models + Traits
├── Services/          # DocumentService, NotificationService, etc.
└── Console/Commands/  # SendRemindersCommand
database/
├── migrations/        # Schema database
└── seeders/           # Data awal & demo
resources/views/       # Blade templates
routes/
├── web.php           # Web routes
└── api.php           # API routes
```

---

## 🎯 Alur Kerja (Workflow)

1. **Admin/Inspektorat** input Audit → upload surat audit
2. **Admin/Inspektorat** input Temuan per OPD
3. **Admin/Inspektorat** input Rekomendasi (otomatis generate reminder H-30/14/7/3/1)
4. **Operator OPD** menerima notifikasi → input Tindak Lanjut + upload bukti
5. **Verifikator** verifikasi (Disetujui/Revisi/Ditolak)
6. Jika Revisi → **Operator OPD** dapat notifikasi & wajib revisi
7. **Pimpinan** monitoring via dashboard & laporan
8. **Early Warning** kirim reminder otomatis mendekati deadline

---

## 📞 Dukungan

Untuk pengembangan lebih lanjut, kontak: **CV CIPTA KARYA DIGITAL INDONESIA**

© 2026 Pemerintah Kabupaten Halmahera Barat - Inspektorat
# siwasta-halbar
