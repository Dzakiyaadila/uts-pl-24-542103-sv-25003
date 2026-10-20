# Orderly API — Backend

Skeleton Laravel 12 dengan konfigurasi PostgreSQL.

## Status

Autentikasi dan CRUD belum diimplementasikan.
Database PostgreSQL belum dijalankan pada milestone skeleton ini.
Tes bawaan memeriksa boot aplikasi, bukan koneksi database atau API bisnis.

## Persiapan lokal

Dari folder backend:

```bash
cp .env.example .env
cp .env.testing.example .env.testing
composer install
php artisan key:generate
php artisan key:generate --env=testing
```

Ekstensi PHP pdo_pgsql wajib tersedia.

## Menjalankan

```bash
php artisan serve --host=127.0.0.1 --port=8000
```

Halaman awal tersedia di http://127.0.0.1:8000.

## Pemeriksaan

```bash
composer validate --strict
composer check-platform-reqs
php artisan config:clear
php artisan test
```

## Environment

- .env: development; PostgreSQL orderly_dev pada port host 55432.
- .env.testing: testing; PostgreSQL orderly_test pada port host 55433.
- Kedua file aktual diabaikan Git.
- File berakhiran .example tidak memuat rahasia aktual.
- Kredensial dan koneksi database diverifikasi saat milestone PostgreSQL.

Database testing harus terisolasi dari development sebelum tes yang
menjalankan migrasi atau mengubah data ditambahkan.

Lihat [rencana proyek](../docs/project-plan.md) untuk arsitektur dan alur Git.