# 🏥 PPI Check — Aplikasi Audit & Surveilans PPI

**PPI Check** adalah aplikasi web untuk mendukung program **Pencegahan dan Pengendalian Infeksi (PPI)** di rumah sakit / fasilitas pelayanan kesehatan. Aplikasi ini membantu tim PPI dalam melakukan **audit kepatuhan** (cuci tangan, APD, pemilahan sampah), **surveilans temuan**, **tindak lanjut**, hingga **pelaporan** secara terintegrasi dan paperless.

---

## ✨ Fitur Utama

| Modul | Deskripsi |
| --- | --- |
| 📊 **Dashboard** | Ringkasan skor kepatuhan per unit & per kategori audit, tren capaian, dan grafik visual (Chart.js), termasuk detail per unit |
| 🧼 **Audit PPI** | Pelaksanaan audit: **Cuci Tangan (5 Momen WHO)**, **APD** (pilih 1 dari 19 tindakan, nilai 6 jenis APD: Sarung Tangan, Masker, Goggle, Apron, Tutup Kepala, Sepatu Boot), dan **Pemilahan Sampah** — lengkap dengan perhitungan skor otomatis & ekspor PDF per audit |
| 💉 **Monitoring Limbah Benda Tajam** | Lembar monitoring penanganan limbah benda tajam (8 pernyataan, Ya/Tidak) dengan persentase otomatis, riwayat, cetak & PDF |
| 🔍 **Surveilans Temuan** | Monitoring seluruh temuan (finding) hasil audit beserta statusnya |
| 📝 **Tindak Lanjut** | Unit/petugas mengirimkan tindak lanjut atas temuan, auditor/admin memverifikasi |
| 🔔 **Notifikasi** | Ikon lonceng di header: temuan menunggu tindak lanjut, lewat jatuh tempo, dan tindak lanjut menunggu verifikasi |
| 📄 **Laporan** | Rekapitulasi laporan audit dengan ekspor **Excel** dan **PDF** |
| 🗂️ **Master Data** | Kelola Unit/Ruangan (44 ruangan), Profesi, Jenis APD, Tindakan APD (19 item), Jenis Limbah, dan Instrumen Audit (kategori & butir pertanyaan) |
| 👥 **Manajemen User** | Pengelolaan pengguna & penetapan peran (khusus Super Admin) |
| ⚙️ **Pengaturan** | Tab **Umum** (identitas RS & ambang batas predikat), **SMTP Email** (konfigurasi + email percobaan), **Logo Aplikasi** (ganti logo), dan **Ganti Password** |
| 👤 **Profil** | Pembaruan data profil & kata sandi pengguna |
| 🔐 **Keamanan** | Login dengan **captcha SVG**, rate-limiting percobaan login, middleware peran & status aktif user, activity log |
| 📱 **PWA Ready** | Service worker, manifest & halaman offline — dapat dipasang seperti aplikasi mobile |

## 👥 Peran Pengguna (Role)

| Role | Hak Akses |
| --- | --- |
| **Super Admin** | Akses penuh: semua modul, kelola user, master data & pengaturan |
| **Admin PPI** | Kelola audit, verifikasi, laporan, master data & pengaturan |
| **Auditor** | Melaksanakan audit, melihat temuan, verifikasi tindak lanjut, laporan |
| **Unit / Petugas** | Melihat temuan unit, mengirim tindak lanjut |

> ℹ️ Kredensial akun tidak dipublikasikan. Silakan hubungi **Super Admin / Admin PPI** masing-masing fasilitas untuk pembuatan akun.

---

## 🛠️ Teknologi

- **Backend:** PHP 8.2+, Laravel 12
- **Frontend:** Tailwind CSS 4, Bootstrap 5, Bootstrap Icons, Alpine.js, Chart.js
- **Build Tool:** Vite 7
- **Database:** SQLite (default, mudah diganti ke MySQL/PostgreSQL)
- **Package tambahan:**
  - `barryvdh/laravel-dompdf` — ekspor PDF
  - `maatwebsite/excel` — ekspor Excel

---

## 🚀 Instalasi

### Persyaratan
- PHP ≥ 8.2 (dengan ekstensi: `pdo_sqlite`, `gd`, `mbstring`, `intl`, dll.)
- Composer
- Node.js & NPM

### Langkah Instalasi

```bash
# 1. Clone / salin proyek, lalu masuk ke folder proyek
cd ppi-check

# 2. Install dependensi PHP
composer install

# 3. Install dependensi JS
npm install

# 4. Siapkan file environment
cp .env.example .env
php artisan key:generate

# 5. Siapkan database (SQLite)
touch database/database.sqlite

# 6. Jalankan migrasi & seeder (master data + contoh data demo)
php artisan migrate --seed

# 7. Build aset frontend
npm run build

# 8. Jalankan server development
php artisan serve
```

Aplikasi dapat diakses di **http://localhost:8000**.

> 💡 Alternatif cepat: `composer run setup` untuk menjalankan langkah instalasi sekaligus, dan `composer run dev` untuk menjalankan server + queue + logs + Vite bersamaan.

### Mode Development

```bash
npm run dev          # Vite dev server (hot reload)
php artisan serve    # Server Laravel
```

---

## ⚙️ Konfigurasi

Salin `.env.example` menjadi `.env` lalu sesuaikan:

```env
APP_NAME="PPI Check"
APP_URL=http://localhost

# Default menggunakan SQLite
DB_CONNECTION=sqlite

# Jika menggunakan MySQL, sesuaikan:
# DB_CONNECTION=mysql
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=ppi_check
# DB_USERNAME=root
# DB_PASSWORD=
```

---

## 📁 Struktur Direktori (Ringkas)

```
ppi-check/
├── app/
│   ├── Exports/              # Export Excel (audits)
│   ├── Http/
│   │   ├── Controllers/      # Auth, Dashboard, Audit, Finding, FollowUp,
│   │   │   │                 # Verification, Report, Profile, Setting
│   │   │   └── Master/       # Unit, Profesi, APD, Limbah, Instrumen, User
│   │   └── Middleware/       # CheckRole, EnsureUserIsActive
│   ├── Models/               # Eloquent models
│   └── Support/              # Helper PPI (perhitungan skor, dll.)
├── database/
│   ├── migrations/
│   └── seeders/              # MasterSeeder (data master) & DemoDataSeeder
├── resources/views/          # Blade templates (layouts, audits, findings, dst.)
├── routes/web.php            # Definisi seluruh route aplikasi
└── public/                   # Termasuk manifest.webmanifest & sw.js (PWA)
```

---

## 📖 Alur Penggunaan Singkat

1. **Auditor** login → memilih kategori audit (Cuci Tangan / APD / Pemilahan Sampah) → mengisi instrumen audit per unit.
2. Sistem menghitung **skor kepatuhan** secara otomatis; butir yang tidak patuh otomatis menjadi **temuan (finding)**.
3. **Unit/Petugas** menerima temuan → mengirimkan **tindak lanjut**.
4. **Auditor/Admin** melakukan **verifikasi** tindak lanjut (disetujui / revisi).
5. **Admin PPI/Super Admin** memantau **dashboard** dan mengunduh **laporan** Excel/PDF.

---

## 🧪 Testing

```bash
php artisan test
# atau
composer run test
```

---

## 🔒 Catatan Keamanan

- Akun bawaan/seeder bersifat **demo** — pada lingkungan produksi, segera nonaktifkan/ganti akun demo dan gunakan kata sandi yang kuat.
- Jangan pernah meng-commit file `.env` ke repositori.
- Pastikan `APP_DEBUG=false` pada production.

## 📄 Lisensi

Aplikasi ini dikembangkan untuk kebutuhan internal program PPI. Seluruh hak cipta dimiliki oleh pengembang / instansi terkait.
# ppi-check
