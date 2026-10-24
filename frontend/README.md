# Orderly — Frontend

Vue 3 dengan JavaScript, Vue Router, Vite, dan Axios.

## Status

Halaman login, daftar/detail, tambah/edit/hapus pesanan, dan logout
terhubung ke Laravel API. Token Bearer disimpan selama sesi tab di
sessionStorage dan dibersihkan saat logout atau respons 401.

## Persiapan

Dari folder frontend, gunakan Node.js 24 dan npm:

```bash
cp .env.example .env
npm ci
npm run dev
```

Buka alamat yang ditampilkan Vite.

## Konfigurasi API

VITE_API_URL pada .env menentukan base URL instance Axios
di src/services/api.js.

Contoh pengembangan lokal:
http://127.0.0.1:8000/api

Pada Docker Compose, frontend dibangun dengan VITE_API_URL=/api.
Nginx melayani halaman Vue di http://localhost:8080, meneruskan /api
ke backend, dan mengembalikan index.html untuk route Vue langsung.
Environment frontend berbeda dari environment backend dan root Compose.

Variabel VITE_* tersedia bagi browser dan bukan tempat menyimpan rahasia.
Restart dev server setelah mengubah environment.
Konfigurasi produksi dimasukkan saat build.

## Pemeriksaan

```bash
npm run format
npm run lint
npm ci
npm run build
```

Hasil build berada di dist/ dan tidak di-commit.
package-lock.json di-commit; node_modules/ diabaikan Git.

Untuk menjalankan frontend, backend, dan database lewat Docker Compose,
lihat [README root](../README.md). Detail request dan respons ada di
[kontrak API pesanan](../docs/order-api.md).

Lihat [rencana proyek](../docs/project-plan.md) untuk arsitektur dan alur Git.