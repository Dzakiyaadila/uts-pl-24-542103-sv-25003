# Rencana Proyek Orderly API

Status: rencana implementasi, belum merupakan fitur yang selesai.
Issue: https://github.com/Dzakiyaadila/uts-pl-24-542103-sv-25003/issues/1

## 1. Tujuan

Orderly API membantu usaha souvenir mencatat dan mengelola pesanan.
Pengguna dapat login, melihat pesanan, menambah, mengubah, dan menghapus
pesanan melalui Vue yang mengonsumsi Laravel REST API.

Prioritas pertama adalah memenuhi seluruh persyaratan wajib UTS KEPL.
Tema proyek harus berbeda dari tugas sebelumnya.

## 2. Batas MVP

Fitur yang direncanakan:

- Login dan logout menggunakan token Laravel Sanctum.
- CRUD lengkap resource order, termasuk endpoint detail satu order.
- Setiap pengguna hanya dapat mengakses order miliknya.
- Validasi input dan respons JSON dengan status HTTP yang sesuai.
- Halaman login dan CRUD Vue menggunakan Axios.
- Penyimpanan data PostgreSQL.
- Feature test API, koleksi Postman, CI, Docker Compose, dan publikasi image GHCR.

Untuk MVP, satu order mencatat satu jenis produk souvenir.
Total harga dihitung backend dari jumlah dan harga satuan.

Katalog produk terpisah, banyak item dalam satu order, stok, pembayaran,
notifikasi WhatsApp, dan analitik menjadi pengembangan setelah UTS.

## 3. Teknologi

| Komponen | Pilihan |
| --- | --- |
| Backend | Laravel 12 |
| PHP | Seri 8.2 |
| Dependency manager backend | Composer 2 |
| Autentikasi | Laravel Sanctum yang kompatibel dengan Laravel 12 |
| Frontend | Vue 3, JavaScript, Vue Router, Vite |
| HTTP client | Axios |
| Node.js | Seri 24 |
| Package manager frontend | npm |
| Database | PostgreSQL 16 |
| Container | Docker dan Docker Compose |
| CI dan publikasi image | GitHub Actions dan GHCR |
| Penyaji frontend container | Nginx |

Versi dependency persis akan disimpan dalam composer.lock dan package-lock.json.
Versi tag image dasar diperiksa dan ditetapkan saat pembuatan Dockerfile.
Image tidak memakai tag latest.

Lingkungan awal yang sudah diperiksa:

- PHP 8.2.29 dan Composer 2.8.11.
- Node.js 24.19.0 dan npm 11.17.0.
- Docker Engine 28.3.3.
- Docker Compose v2.39.2-desktop.1.
- Platform Engine linux/aarch64.

Pemeriksaan versi belum membuktikan seluruh ekstensi PHP, dependency,
koneksi database, atau aplikasi sudah berfungsi.

Dukungan keamanan seri PHP 8.2 berakhir pada 31 Desember 2026.
Gunakan patch keamanan yang tersedia; peningkatan versi PHP menjadi
pekerjaan lanjutan sebelum penggunaan publik jangka panjang.

## 4. Rancangan Resource Order

| Field | Fungsi |
| --- | --- |
| id | Identitas order |
| user_id | Pemilik order; ditentukan dari pengguna terautentikasi |
| customer_name | Nama pelanggan |
| product_name | Nama produk souvenir |
| quantity | Jumlah pesanan, bilangan bulat positif |
| unit_price | Harga satuan dalam rupiah bulat |
| total_price | quantity × unit_price, dihitung backend |
| status | pending, processing, completed, atau cancelled |
| due_date | Tanggal tenggat pesanan |
| notes | Catatan opsional |
| created_at, updated_at | Waktu pencatatan dan perubahan |

Nilai uang memakai bilangan bulat rupiah, bukan floating point.
Backend memvalidasi batas quantity dan unit_price sebelum menghitung total.
Frontend tidak boleh menentukan user_id atau total_price tepercaya.

## 5. Rencana Endpoint

| Method | Endpoint | Fungsi |
| --- | --- | --- |
| POST | /api/login | Login dan menerima token |
| POST | /api/logout | Mencabut token aktif |
| GET | /api/orders | Daftar order pengguna |
| GET | /api/orders/{order} | Detail order |
| POST | /api/orders | Membuat order |
| PUT | /api/orders/{order} | Mengubah order |
| DELETE | /api/orders/{order} | Menghapus order |

Semua endpoint aplikasi selain login memerlukan Bearer token.
Registrasi publik tidak termasuk MVP; akun awal disiapkan melalui seeder.

Request yang membawa data menggunakan JSON.
Seluruh respons API menggunakan JSON, termasuk respons error.
Pembuatan data menghasilkan 201, validasi gagal 422,
dan akses tanpa token yang valid menghasilkan 401.

## 6. Struktur Repository

| Lokasi | Isi yang direncanakan |
| --- | --- |
| backend/ | Laravel, feature test, Dockerfile, dan .dockerignore |
| frontend/ | Vue, konfigurasi Axios, Dockerfile, dan .dockerignore |
| .github/workflows/ci.yml | Pengujian, build, dan publikasi image |
| docs/ | Rencana, arsitektur, panduan, dan bukti |
| docs/postman/ | Ekspor Postman Collection dan environment contoh |
| docs/evidence/ | Screenshot dan indeks bukti |
| docker-compose.yml | Definisi layanan aplikasi |
| .env.example | Contoh konfigurasi tanpa rahasia aktual |
| README.md | Overview dan petunjuk penggunaan |

Tabel ini menunjukkan struktur target; folder dan file dibuat bertahap.

## 7. Alur Sistem

Pengguna membuka Vue di browser. Vue mengirim request menggunakan Axios
ke URL API dari konfigurasi environment.

Pada Docker Compose, Nginx frontend meneruskan request /api ke backend
menggunakan nama service backend. Laravel memvalidasi request, memeriksa
token dan kepemilikan order, lalu mengakses PostgreSQL melalui nama
service database.

Respons JSON dikembalikan ke Vue untuk ditampilkan kepada pengguna.

Nama service internal Docker dipakai untuk komunikasi antarkontainer.
Browser mengakses alamat aplikasi yang tersedia dari host.

## 8. Pemisahan Database

| Lingkungan | Database rencana | Ketentuan |
| --- | --- | --- |
| Development lokal | orderly_dev | Data percobaan aplikasi; disimpan persisten |
| Testing lokal | orderly_test | Terisolasi dari development; boleh di-reset oleh tes |
| CI | orderly_ci_test | PostgreSQL sementara pada runner |
| Deployment | orderly_prod | Terpisah dari lokal dan CI jika aplikasi dihosting |

Database testing menggunakan kredensial yang tidak berhak mengakses
database development atau production.

Semua lingkungan memakai PostgreSQL.
Jangan menjalankan reset database atau migration:fresh pada data
development atau production tanpa pemeriksaan dan persetujuan.

Job deploy yang diminta UTS membangun dan memublikasikan image ke GHCR.
Hosting aplikasi dan database production belum dilakukan pada tahap itu.

## 9. Alur Git

- main: versi stabil.
- dev: integrasi pekerjaan.
- docs/...: dokumentasi.
- feat/...: fitur aplikasi.
- build/...: dependency dan konfigurasi build/container.
- ci/...: workflow GitHub Actions.
- fix/...: perbaikan masalah.

Branch pekerjaan dibuat dari dev.
Perubahan masuk ke dev melalui Pull Request.
Versi yang sudah siap masuk dari dev ke main melalui Pull Request.

Urutan kerja:
Issue → branch → perubahan → pemeriksaan → commit → push →
PR → Actions/review → merge.

Contoh Conventional Commits:

- docs: define project plan and development workflow
- build(backend): scaffold Laravel application
- ci: add PostgreSQL backend tests and frontend build
- feat(orders): add authenticated order creation
- test(orders): cover validation and ownership
- fix(auth): revoke current token on logout

Commit dibuat setelah pemeriksaan yang relevan lulus.
Branch protection main dan dev mewajibkan PR dan status checks.
Required checks dipasang setelah workflow menghasilkan nama check dan lulus.
Publikasi awal dev hanya menunjuk commit awal, tanpa perubahan file.

## 10. Rencana Pengujian

- Pastikan skeleton dapat diinstal sebelum menambahkan fitur bisnis.
- Jalankan feature test dengan PostgreSQL khusus testing.
- Periksa login, logout, akses tanpa token, CRUD, validasi, dan kepemilikan.
- Uji seluruh endpoint melalui Postman dan ekspor koleksinya.
- Jalankan npm ci dan npm run build.
- Jalankan CI backend dan frontend sampai hijau.
- Uji login dan CRUD dari browser menggunakan Docker Compose.
- Uji persistensi setelah docker compose down dan docker compose up -d.

Pemeriksaan dan bukti dicatat berdasarkan hasil nyata.

## 11. Environment dan Secrets

- File .env aktual tidak di-commit.
- .env.example hanya memuat contoh aman atau placeholder.
- APP_KEY, DB_PASSWORD, dan rahasia lain yang diperlukan disimpan di Secrets.
- Rahasia tidak ditulis dalam Dockerfile atau dibawa ke image.
- Konfigurasi VITE_* tersedia untuk browser dan tidak boleh berisi rahasia.
- Token/password aktual dibersihkan sebelum mengekspor Postman.
- Screenshot dan log tidak menampilkan nilai rahasia.

## 12. Dokumentasi dan Bukti

Checklist rubrik, catatan keputusan, dan progres:
https://app.notion.com/p/3ffcadac5c7f81a6b950c7a3067e1ade

Dokumentasi teknis disimpan bersama kode di repository.
Setiap bukti dicatat dengan nomor soal, hasil pemeriksaan, nama file,
dan tautan PR/Actions/package yang relevan.

Laporan PDF memuat langkah pengerjaan, screenshot tiap soal, tautan repo,
Postman Collection, output docker compose ps, alur data, serta kendala
dan penyelesaiannya.

## 13. Referensi Resmi

- Laravel: https://laravel.com/docs/12.x/releases
- Database Laravel: https://laravel.com/docs/12.x/database
- Vue: https://vuejs.org/guide/quick-start.html
- PHP: https://www.php.net/supported-versions.php
- PostgreSQL: https://www.postgresql.org/support/versioning/