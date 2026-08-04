# RSUD Landak — Website Profil

Website profil rumah sakit dinamis berbasis Laravel 10 + MySQL + Admin Panel.

## Persyaratan

- PHP 8.1+
- MySQL 8.0 (via Laragon)
- Composer

## Setup & Instalasi

### 1. Jalankan Laragon
Buka Laragon dan klik **Start All** agar Apache dan MySQL aktif.

### 2. Buat Database
Buka HeidiSQL di Laragon, lalu jalankan:
```sql
CREATE DATABASE rsudlandak_web;
```

### 3. Konfigurasi .env
File `.env` sudah dikonfigurasi. Pastikan nilai DB sesuai:
```env
DB_DATABASE=rsudlandak_web
DB_USERNAME=root
DB_PASSWORD=
```

### 4. Jalankan Migration & Seeder
Buka terminal di folder `rsudlandak/`:
```bash
php artisan migrate
php artisan db:seed
php artisan storage:link
```

### 5. Akses Website

Jika project ada di `C:\laragon\www\rsudlandak\`:
- **Website publik**: http://rsudlandak.test
- **Panel admin**: http://rsudlandak.test/admin

Atau jika via subfolder:
- http://localhost/rsudlandak/public

### Login Admin
- Email: `admin@rsudlandak.com`
- Password: `password123`

## Struktur Fitur

- Halaman publik dengan semua section (Hero, Layanan, Dokter, Kamar, Berita, dll)
- Panel admin CRUD: Hero, Layanan, Dokter, Berita, Kamar, Pengaturan Situs
- Upload gambar ke `storage/app/public/rssite/images/`
- Toggle aktif/nonaktif semua konten
- Jadwal dokter dinamis (Alpine.js)
- Fasilitas kamar dinamis (Alpine.js)
- Counter animasi statistik
- Responsive mobile-friendly
- Tailwind CSS CDN + Alpine.js CDN (tidak perlu npm build)
