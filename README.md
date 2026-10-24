# Orderly API

Aplikasi pengelolaan pesanan souvenir untuk tugas UTS Konstruksi dan
Evolusi Perangkat Lunak. Laravel 12 menyediakan API, Vue 3 menampilkan
login dan CRUD, dan PostgreSQL menyimpan pesanan.

## Fitur

- Login dan logout dengan token Laravel Sanctum.
- Daftar, detail, tambah, ubah, dan hapus pesanan milik pengguna login.
- Validasi JSON dan perhitungan total harga di backend.
- Feature test PostgreSQL, pemeriksaan CI, dan build container.
- Docker Compose menjalankan PostgreSQL, backend Apache, dan frontend Nginx
  pada satu alamat browser. Data PostgreSQL disimpan pada volume.

## Menjalankan lewat Docker Compose

Dari root repository, salin `.env.example` ke `.env` jika belum ada.
Isi `DB_PASSWORD` dengan nilai nonkosong dan `APP_KEY` dengan key Laravel
yang valid, misalnya nilai dari `backend/.env` lokal. Jangan commit
`.env` atau membagikan kedua rahasia tersebut.

```bash
docker compose config --quiet
docker compose up --build -d
docker compose ps
docker compose exec backend php artisan orderly:create-dev-user
```

Buka http://localhost:8080, lalu login dengan akun yang dibuat melalui
prompt. Backend menjalankan migrasi ketika service mulai.
Untuk menguji persistensi, buat satu pesanan, jalankan `docker compose down`
lalu `docker compose up -d`, dan periksa pesanan yang sama di browser.
Perintah `down -v` menghapus volume database.

Untuk pengembangan tanpa Compose, ikuti panduan backend dan frontend di bawah.

## CI dan image

Pull Request ke `dev` menjalankan tes backend, build frontend, serta build
image backend dan frontend tanpa publikasi. Setelah perubahan masuk `main`,
workflow membangun image multiarsitektur dan memublikasikannya ke GHCR
sebagai `ghcr.io/dzakiyaadila/orderly-backend` dan
`ghcr.io/dzakiyaadila/orderly-frontend` dengan tag `main` dan SHA commit.
Publikasi image bukan hosting aplikasi/database.

## Dokumentasi

- [Kontrak API pesanan](docs/order-api.md)
- [Postman Collection](docs/postman/orderly-api.postman_collection.json)
- [Panduan backend](backend/README.md)
- [Panduan frontend](frontend/README.md)
- [Pengujian lokal dan CI](docs/testing.md)
- [Rencana proyek](docs/project-plan.md)
- [Indeks bukti](docs/evidence/README.md)
- [Catatan dan checklist di Notion](https://app.notion.com/p/3ffcadac5c7f81a6b950c7a3067e1ade)

Branch pekerjaan berawal dari `dev` dan digabungkan melalui PR.
Rilis stabil masuk dari `dev` ke `main` melalui PR.
