# Pengujian Lokal dan CI

## Lingkungan database

| Lingkungan | Database | Koneksi |
| --- | --- | --- |
| Development lokal | orderly_dev | Belum disiapkan |
| Testing lokal | orderly_test | PostgreSQL container, 127.0.0.1:55433 |
| CI | orderly_ci_test | Service sementara, 127.0.0.1:5432 |
| Deployment | orderly_prod | Belum disiapkan |

Semua lingkungan menggunakan PostgreSQL.
Container testing lokal terpisah dari database development.

## Persiapan testing lokal

Konfigurasi PostgreSQL container berada di
backend/.env.postgres-testing, berdasarkan file .example.

Konfigurasi Laravel testing berada di backend/.env.testing.
Password pada kedua file aktual harus cocok dan tidak di-commit.

Container testing bernama orderly-test-db.
Setelah container pernah dibuat, gunakan docker start orderly-test-db
untuk menyalakannya kembali.

## Menjalankan tes

Dari backend:

```bash
php artisan config:clear
php artisan migrate --env=testing
php artisan test
vendor/bin/pint --test
```

PostgreSqlConnectionTest memeriksa environment testing, driver PostgreSQL,
identitas database/user aktual, dan tabel users hasil migrasi.

Tes infrastruktur dan tes bawaan belum memenuhi persyaratan
minimal dua feature test endpoint API UTS.

## Frontend

Dari frontend:

```bash
npm run lint
npm ci
npm run build
```

Build belum membuktikan integrasi login atau CRUD dengan backend.

## GitHub Actions

Workflow: .github/workflows/ci.yml.

- Backend tests: PHP 8.2, PostgreSQL 16.15, migrasi, dan php artisan test.
- Frontend build: Node.js 24, npm ci, dan npm run build.
- Workflow dipicu push branch serta PR ke main/dev.
- Password PostgreSQL CI diambil dari repository secret CI_DB_PASSWORD.
- Database CI dibuat sementara dan tidak mengakses database lokal.
- APP_KEY CI dibuat saat job berjalan dan tidak dipakai untuk deployment.

Jika branch mempunyai PR terbuka, push dapat menghasilkan dua run:
event push dan pembaruan pull_request.

## Bukti

Catat tautan run, branch, commit, hasil job, serta screenshot.
Nilai secret tidak boleh muncul dalam bukti atau log tambahan.