# HR-DWMS — Daily Work Monitoring System

> Sistem manajemen dan monitoring tugas harian berbasis web untuk kebutuhan internal perusahaan.

HR-DWMS memungkinkan setiap divisi menerima, membuat, menyelesaikan, dan memantau tugas melalui satu sistem terpusat.

## Features
 
- Distribusi tugas dari Admin ke berbagai divisi
- Tugas harian otomatis melalui scheduler
- Tugas mandiri untuk setiap pengguna
- Monitoring progres tugas
- Notifikasi in-app
- Laporan produktivitas dan riwayat tugas
- Manajemen pengguna berdasarkan role
- Pengaturan nama aplikasi, logo, dan link grup WhatsApp

## User Roles

| Role | Deskripsi | Akses Utama |
|---|---|---|
| **Admin** | Administrator sistem | Kelola pengguna, tugas, laporan, notifikasi, dan pengaturan aplikasi |
| **HR Staff** | Staf HR | Kelola HR Assistant, membuat tugas, dan memantau progres Assistant |
| **HR Assistant** | Asisten HR | Menerima dan menyelesaikan tugas serta melihat riwayat |
| **CS** | Customer Service | Menerima dan menyelesaikan tugas serta melihat riwayat |
| **OB** | Office Boy | Menerima dan menyelesaikan tugas serta melihat riwayat |

## Technology Stack

| Komponen | Teknologi |
|---|---|
| Backend | Laravel 12 |
| PHP | ^8.2 |
| Frontend | Tailwind CSS ^3.4 |
| JavaScript | Alpine.js ^3.15 |
| UI Components | Flowbite ^4.0 |
| Build Tool | Vite |
| Database | MySQL 8.0+ |
| Notifications | Laravel Notifications — Database Channel |
| Scheduler | Laravel Task Scheduler |

## Architecture

Menggunakan layered architecture dengan pemisahan antara controller, business logic, database access, dan model.

```text
app/
├── Http/Controllers/
│   ├── Admin/
│   ├── Staff/
│   ├── Assistant/
│   ├── Cs/
│   └── Ob/
├── Services/
├── Repositories/
├── Models/
└── Notifications/
```

## Requirements

Pastikan environment memiliki:

- PHP >= 8.2
- Composer >= 2.x
- Node.js >= 18.x
- npm
- MySQL >= 8.0
- Git

## Installation

### 1. Clone Repository

```bash
git clone https://github.com/JakwanAja/hr-monitor-project.git
cd hr-monitor-project
```

### 2. Install Dependencies

```bash
composer install
npm install
```

### 3. Configure Environment

Salin `.env.example` menjadi `.env`:

```bash
cp .env.example .env
```

Kemudian sesuaikan konfigurasi aplikasi dan database:

```env
APP_NAME="HR-DWMS"
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://localhost

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=hrmonitor_db
DB_USERNAME=root
DB_PASSWORD=

SESSION_DRIVER=file
SESSION_LIFETIME=120
SESSION_ENCRYPT=false
SESSION_PATH=/
SESSION_DOMAIN=null
```

Generate application key:

```bash
php artisan key:generate
```

### 4. Setup Database

> ⚠️ Database tidak disertakan dalam repository ini, Hubungi developer untuk mendapatkan database. 
Setelah mendapatkan file (`.sql`), ikuti langkah berikut:

**1. Buat database baru di MySQL:**

Melalui phpMyAdmin / HeidiSQL: klik **New** → isi nama database (contoh: hrmonitor_db) → pilih collation `utf8mb4_unicode_ci` → klik **Create**.

**2. Import dump database:**

Via terminal:
```bash
mysql -u root -p nama_database < hrmonitor_db.sql
```
Via phpMyAdmin: pilih database yang baru dibuat → tab **Import** → pilih file `hrmonitor_db.sql` → klik **Go**.

**3. Sesuaikan `.env` dengan nama database yang digunakan:**

```env
DB_DATABASE=hrmonitor_db
DB_USERNAME=root
DB_PASSWORD=
```

### 5. Create Storage Link

```bash
php artisan storage:link
```

### 6. Build Assets

```bash
npm run build
```

## Running the Application

Jalankan Laravel server:

```bash
php artisan serve
```

Pada terminal kedua, jalankan Vite:

```bash
npm run dev
```

Aplikasi tersedia di:

```text
http://localhost:8000
```

## Project Status

**Status: Completed**

**Completed** — Fitur utama manajemen tugas, monitoring progres, notifikasi, laporan produktivitas, manajemen pengguna, dan pengaturan aplikasi telah tersedia.

## Contributor

**Muhamad Dzakwan Alfaris**  
Program Studi Teknologi Rekayasa Perangkat Lunak  
Politeknik Negeri Madiun — 2026
