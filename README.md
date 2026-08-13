# HR-DWMS — Daily Work Monitoring System

> Sistem monitoring tugas harian berbasis web untuk seluruh divisi perusahaan.  
> Dibangun dengan Laravel 12, Tailwind CSS, dan Alpine.js.

---

## Daftar Isi

- [Overview](#overview)
- [Role Pengguna](#role-pengguna)
- [Teknologi & Arsitektur](#teknologi--arsitektur)
- [Instalasi & Konfigurasi](#instalasi--konfigurasi)
- [Database](#database)
- [Menjalankan Aplikasi](#menjalankan-aplikasi)
- [Jadwal Otomatis](#jadwal-otomatis)
- [Kontak Developer](#kontak-developer)

---

## Overview

HR-DWMS adalah aplikasi manajemen tugas harian internal perusahaan yang memungkinkan setiap divisi — mulai dari HR, Customer Service, hingga Office Boy — memantau, menerima, dan menyelesaikan tugas secara real-time melalui antarmuka web.

> 📸 **[Screenshot Dashboard Admin]**

> 📸 **[Screenshot Dashboard HR Staff]**

> 📸 **[Screenshot Halaman Tugas Hari Ini]**

**Fitur utama:**
- Distribusi tugas dari Admin ke semua divisi
- Tugas harian otomatis (dijadwalkan via cron)
- Tugas mandiri yang bisa dibuat sendiri oleh setiap pengguna
- Monitoring progres per divisi secara real-time
- Notifikasi in-app (tugas masuk & pengumuman custom)
- Laporan produktivitas, riwayat tugas, dan ranking
- Manajemen pengguna per role
- Pengaturan nama aplikasi, logo, dan link grup WhatsApp

---

## Role Pengguna

| Role | Keterangan | Hak Akses Utama |
|---|---|---|
| **Admin** | Administrator sistem | Manajemen semua pengguna, buat & pantau semua tugas, laporan, notifikasi, pengaturan aplikasi |
| **HR Staff** | Staf HR | Tugas harian sendiri, kelola akun HR Assistant, buat tugas untuk HR Assistant, pantau progres Assistant |
| **HR Assistant** | Asisten HR | Menerima & menyelesaikan tugas (rutin + dari atasan + mandiri), lihat riwayat dan skor |
| **CS** | Customer Service | Tugas harian sendiri (rutin + dari Admin + mandiri), lihat riwayat — tidak memiliki bawahan |
| **OB** | Office Boy | Tugas harian sendiri (rutin + dari Admin + mandiri), lihat riwayat — tidak memiliki bawahan |

---

## Teknologi & Arsitektur

| Layer | Teknologi | Versi |
|---|---|---|
| Backend Framework | Laravel | 12.x |
| PHP | PHP | ^8.2 |
| Frontend CSS | Tailwind CSS | ^3.4 |
| Frontend JS | Alpine.js | ^3.15 |
| Build Tool | Vite + laravel-vite-plugin | ^6.0 / ^1.2 |
| Database | MySQL | 5.7+ / 8.0+ |
| Notifikasi | Laravel Notifications (database channel) | — |
| Penjadwalan | Laravel Task Scheduler (cron) | — |
| Font | Inter (@fontsource) | ^5.2 |
| UI Components | Flowbite | ^4.0 |
| HTTP Client | Axios | ^1.7 |
| Dev Server | Concurrently | ^9.0 |

**Pola Arsitektur:** Controller → Service → Repository → Model (layered architecture)

```
app/
├── Http/Controllers/   # Admin/, Staff/, Assistant/, Cs/, Ob/
├── Services/           # Business logic
├── Repositories/       # Database query layer
├── Models/             # Eloquent models
├── Notifications/      # Laravel notification classes
└── Console/Commands/   # Scheduled commands
```

---

## Instalasi & Konfigurasi

### Prasyarat

Pastikan sistem kamu sudah memiliki:

- **PHP** >= 8.2 (dengan ekstensi: `pdo_mysql`, `mbstring`, `openssl`, `tokenizer`, `xml`, `ctype`, `json`, `bcmath`, `fileinfo`)
- **Composer** >= 2.x
- **Node.js** >= 18.x + **npm**
- **MySQL** >= 5.7
- **Git**

---

### Langkah 1 — Clone Repository

```bash
git clone https://github.com/username/hr-dwms.git
cd hr-dwms
```

---

### Langkah 2 — Install Dependensi PHP

```bash
composer install
```

---

### Langkah 3 — Install Dependensi Node

```bash
npm install
```

---

### Langkah 4 — Konfigurasi Environment

Salin file `.env.example` menjadi `.env`:

```bash
cp .env.example .env
```

Buka `.env` dan sesuaikan konfigurasi berikut:

```env
APP_NAME="HR-DWMS"
APP_ENV=local
APP_KEY=                        # akan diisi di langkah berikutnya
APP_DEBUG=true
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=nama_database_kamu
DB_USERNAME=root
DB_PASSWORD=password_kamu
```

---

### Langkah 5 — Generate Application Key

```bash
php artisan key:generate
```

---

### Langkah 6 — Setup Database

> ⚠️ Database tidak disertakan dalam repository ini.  
> Lihat bagian [Database](#database) di bawah untuk mendapatkan dump database.

Setelah mendapatkan file dump database, import ke MySQL:

```bash
mysql -u root -p nama_database_kamu < hr_dwms.sql
```

Atau import melalui phpMyAdmin / HeidiSQL / tools database lainnya.

---

### Langkah 7 — Buat Storage Link

```bash
php artisan storage:link
```

Ini diperlukan agar foto profil dan logo aplikasi yang diupload bisa diakses publik.

---

### Langkah 8 — Buat Akun Admin Pertama

Akses URL berikut di browser **setelah server dijalankan** (langkah 9):

```
http://localhost:8000/setup/seveninc-setup-2024
```

Jika berhasil, akan muncul response JSON berisi email dan password default:

```json
{
  "status": "success",
  "email": "admin@seveninc.com",
  "password": "admin123456"
}
```

> ⚠️ **Segera ganti password** setelah login pertama kali melalui menu Profil.  
> Setelah akun admin berhasil dibuat, **hapus atau nonaktifkan route setup** di `routes/web.php` untuk keamanan.

---

### Langkah 9 — Build Assets

Untuk **development** (dengan hot reload):

```bash
npm run dev
```

Untuk **production**:

```bash
npm run build
```

---

## Menjalankan Aplikasi

### Development

Jalankan dua terminal secara bersamaan:

**Terminal 1 — Laravel server:**
```bash
php artisan serve
```

**Terminal 2 — Vite dev server (hot reload):**
```bash
npm run dev
```

Akses aplikasi di: `http://localhost:8000`

---

### Menggunakan Laragon / XAMPP

Jika menggunakan Laragon atau XAMPP, cukup letakkan project di direktori `www/` atau `htdocs/`, lalu akses melalui virtual host yang telah dikonfigurasi. Pastikan Vite sudah di-build dengan:

```bash
npm run build
```

---

## Database

Database **tidak disertakan** dalam repository ini karena berisi data sensitif perusahaan.

Untuk mendapatkan dump database (struktur + data awal), silakan hubungi developer:

> 📧 **[Hubungi Developer — lihat bagian Kontak di bawah]**

File dump yang akan diberikan mencakup:
- Struktur semua tabel (`users`, `tasks`, `task_assignments`, `default_tasks`, `notifications`, `app_settings`, `wa_groups`)
- Data seeder awal (akun admin default, contoh default tasks)

---

## Jadwal Otomatis

Aplikasi ini menggunakan dua scheduled command yang harus dijalankan otomatis setiap hari:

| Command | Jadwal | Fungsi |
|---|---|---|
| `tasks:generate-daily` | Setiap hari pukul 08:00 | Generate tugas harian otomatis dari daftar Default Tasks |
| `tasks:mark-not-done` | Setiap hari pukul 23:59 | Tandai tugas default yang belum diselesaikan sebagai "Tidak Dikerjakan" |

### Setup Cron (Linux/Mac)

Tambahkan baris berikut ke crontab server:

```bash
* * * * * cd /path/to/project && php artisan schedule:run >> /dev/null 2>&1
```

### Menjalankan Manual (untuk testing)

```bash
# Generate tugas harian
php artisan tasks:generate-daily

# Tandai tugas yang tidak dikerjakan
php artisan tasks:mark-not-done
```

---

## Kontak Developer

Database tidak tersedia secara publik. Untuk mendapatkan dump database atau informasi lebih lanjut mengenai project ini, silakan hubungi:

| | |
|---|---|
| **Nama** | [Nama Developer] |
| **Email** | [email@developer.com] |
| **GitHub** | [github.com/username] |

---

*HR-DWMS — Built with Laravel 12 & Tailwind CSS*
