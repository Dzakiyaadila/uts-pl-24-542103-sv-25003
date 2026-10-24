# Pengujian Lokal dan CI

## Lingkungan database

| Lingkungan | Database | Koneksi |
| --- | --- | --- |
| Development lokal | orderly_dev | Container orderly-dev-db, 127.0.0.1:55432, volume orderly-dev-data |
| Testing lokal | orderly_test | Container orderly-test-db, 127.0.0.1:55433 |
| CI | orderly_ci_test | Service sementara, 127.0.0.1:5432 |
| Deployment | orderly_prod | Belum disiapkan |

Semua lingkungan menggunakan PostgreSQL. Container testing terpisah
dari database development, dan hanya development memakai volume data
persisten pada tahap ini.

## Persiapan database lokal

Konfigurasi PostgreSQL container testing ada di
backend/.env.postgres-testing, berdasarkan file .example.
Konfigurasi Laravel testing ada di backend/.env.testing.
Gunakan POSTGRES_DB dan POSTGRES_USER bernilai orderly_test serta
DB_DATABASE dan DB_USERNAME yang sama; port host 55433.
Password pada kedua file aktual harus cocok.

Untuk development, gunakan backend/.env.postgres-development yang
berisi POSTGRES_DB=orderly_dev, POSTGRES_USER=orderly_app, dan
POSTGRES_PASSWORD tidak kosong. File backend/.env memakai
DB_CONNECTION=pgsql, DB_HOST=127.0.0.1, DB_PORT=55432,
DB_DATABASE=orderly_dev, DB_USERNAME=orderly_app, dan password yang sama.
Pilih password development berbeda dari password testing.

Kedua pasangan file aktual diabaikan Git. Nilai POSTGRES_* hanya
dipakai saat inisialisasi volume baru; mengedit env-file setelah
database terbentuk tidak mengubah role atau password di database.
Jangan menghapus volume orderly-dev-data jika sudah berisi data
development yang perlu disimpan.

Untuk menyalakan kembali container yang pernah dibuat:

```bash
docker start orderly-dev-db orderly-test-db
docker ps --filter name=orderly
```

## Menjalankan tes

Dari backend, setelah orderly-test-db siap:

```bash
php artisan config:clear
php artisan migrate --env=testing
php artisan test
./vendor/bin/pint --test
```

PostgreSqlConnectionTest memeriksa environment testing, driver PostgreSQL,
identitas database/user aktual, dan tabel users hasil migrasi.
AuthApiTest menggunakan RefreshDatabase pada orderly_test dan menguji
login berhasil/gagal, validasi, akses terlindungi, serta logout yang
mencabut token. Tes ini tidak memakai database development.

## Verifikasi API manual

Setelah orderly-dev-db siap, dari backend jalankan php artisan migrate
dan php artisan orderly:create-dev-user, lalu php artisan serve.
[Postman Collection](postman/orderly-api.postman_collection.json) berisi
request dengan variabel lokal, tanpa password atau token sebenarnya.

Hasil yang diperiksa: POST /api/login mengembalikan 200 dan token;
GET /api/user dengan Bearer token mengembalikan 200; POST /api/logout
mengembalikan 200; GET /api/user dengan token yang telah dicabut
mengembalikan 401. Feature test juga memeriksa kredensial salah 401
dan input login tidak lengkap 422.

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
PR autentikasi: https://github.com/Dzakiyaadila/uts-pl-24-542103-sv-25003/pull/13.
Nilai secret, password, dan token tidak boleh muncul dalam bukti
atau log tambahan.
