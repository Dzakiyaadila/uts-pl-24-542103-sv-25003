# Orderly API

Aplikasi pengelolaan pesanan souvenir dengan Laravel REST API,
Vue, dan PostgreSQL. Dikembangkan untuk tugas UTS
Konstruksi dan Evolusi Perangkat Lunak sekaligus portofolio.

## Status proyek

Tahap saat ini: perencanaan dan fondasi repository.

Backend, frontend, database, CI, dan Docker belum diimplementasikan.
Fitur di bawah merupakan scope yang akan dikerjakan.

## Fitur yang direncanakan

- Login dan logout menggunakan token Laravel Sanctum.
- CRUD pesanan milik pengguna terautentikasi.
- Perhitungan total harga oleh backend.
- Validasi dan respons JSON.
- Frontend Vue menggunakan Axios.
- Pengujian API, CI, Docker Compose, dan publikasi image ke GHCR.

## Teknologi yang ditetapkan

| Komponen | Pilihan |
| --- | --- |
| Backend | Laravel 12 dan PHP 8.2 |
| Frontend | Vue 3, JavaScript, Vite, Vue Router, Axios |
| Tooling frontend | Node.js 24 dan npm |
| Database | PostgreSQL 16 |
| Container | Docker dan Docker Compose |
| CI / registry | GitHub Actions dan GHCR |

Versi dependency persis dicatat melalui lockfile setelah instalasi.
Versi image dasar ditetapkan saat implementasi Docker.

## Dokumentasi

- [Rencana proyek](docs/project-plan.md)
- [Indeks bukti](docs/evidence/README.md)
- [Catatan pendampingan di Notion](https://app.notion.com/p/3ffcadac5c7f81a6b950c7a3067e1ade)

Petunjuk instalasi dan menjalankan aplikasi ditambahkan setelah
skeleton dan konfigurasi terkait benar-benar tersedia serta diuji.

## Alur pengembangan

Branch pekerjaan dibuat dari dev dan digabungkan kembali melalui PR.
Versi yang siap dirilis masuk dari dev ke main melalui PR.

Gunakan Conventional Commits dan lakukan pemeriksaan yang relevan
sebelum commit. Required CI checks dipasang setelah workflow tersedia.

## Konfigurasi

File .env aktual diabaikan Git. File .env.example di root merupakan
rencana konfigurasi Compose dan tidak memuat rahasia aktual.

Konfigurasi backend dan frontend akan disediakan saat scaffold.
Perintah PHP yang dijalankan di Mac nantinya memakai alamat database
yang dapat diakses host; nama service Docker dipakai antarkontainer.

Development, testing lokal, CI, dan deployment menggunakan PostgreSQL
dengan data serta kredensial pengujian yang terpisah.
