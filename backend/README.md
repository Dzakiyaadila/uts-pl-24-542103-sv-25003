# Orderly API — Backend

Laravel 12 REST API dengan autentikasi token Sanctum dan PostgreSQL.

## Status

Login, logout, dan endpoint profil terautentikasi tersedia.
CRUD pesanan dan integrasi frontend belum diimplementasikan.
Database development dan testing lokal menggunakan container PostgreSQL terpisah.

## Persiapan lokal

Dari folder backend:

```bash
cp .env.example .env
cp .env.testing.example .env.testing
composer install
php artisan key:generate
php artisan key:generate --env=testing
```

Ekstensi PHP pdo_pgsql wajib tersedia. Isi password database pada file
.env aktual; nilai tersebut tidak disimpan di Git. Petunjuk container ada
di [pengujian lokal dan CI](../docs/testing.md).

## Menjalankan API

Pastikan container orderly-dev-db aktif dan konfigurasi .env mengarah
ke orderly_dev pada 127.0.0.1:55432. Dari folder backend:

```bash
php artisan migrate
php artisan orderly:create-dev-user
php artisan serve --host=127.0.0.1 --port=8000
```

Perintah pembuatan akun hanya berjalan saat APP_ENV=local dan meminta
password melalui prompt tersembunyi. Registrasi publik tidak tersedia.

| Method | Endpoint | Akses |
| --- | --- | --- |
| POST | /api/login | Publik; JSON email dan password |
| GET | /api/user | Bearer token |
| POST | /api/logout | Bearer token; mencabut token yang dipakai |

Login berhasil mengembalikan token dan data pengguna dalam JSON.
Kirim token sebagai header Authorization: Bearer <token> untuk endpoint
terlindungi. Respons validasi gagal 422, kredensial salah 401,
dan akses tanpa token 401. Contoh request tanpa kredensial aktual ada
di [Postman Collection](../docs/postman/orderly-api.postman_collection.json).

## Pemeriksaan

Pastikan container orderly-test-db aktif dan .env.testing mengarah
ke orderly_test pada 127.0.0.1:55433. Dari folder backend:

```bash
composer validate --strict
composer check-platform-reqs
php artisan config:clear
php artisan test
./vendor/bin/pint --test
```

Feature test mencakup login berhasil/gagal, validasi, akses terlindungi,
dan pencabutan token. Database testing terpisah dari orderly_dev.

## Environment

- .env: development; PostgreSQL orderly_dev pada port host 55432.
- .env.testing: testing; PostgreSQL orderly_test pada port host 55433.
- .env.postgres-development dan .env.postgres-testing: variabel POSTGRES_*
  untuk masing-masing container.
- Seluruh file aktual diabaikan Git; file .example tidak memuat rahasia.

Lihat [rencana proyek](../docs/project-plan.md) untuk arsitektur dan alur Git.
