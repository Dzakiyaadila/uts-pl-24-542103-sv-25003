# API Pesanan

Base URL saat aplikasi dijalankan melalui Compose: `http://localhost:8080/api`.
Untuk Laravel yang dijalankan langsung di Mac: `http://127.0.0.1:8000/api`.
Login melalui `POST /api/login` dengan email dan password akun development.
Kirim token yang diterima sebagai header `Authorization: Bearer <token>`.
Registrasi publik tidak tersedia.

| Method | Endpoint | Respons sukses |
| --- | --- | --- |
| GET | /api/orders | 200; array pesanan milik pengguna |
| GET | /api/orders/{id} | 200; detail satu pesanan |
| POST | /api/orders | 201; pesanan baru |
| PUT | /api/orders/{id} | 200; pesanan yang diperbarui |
| DELETE | /api/orders/{id} | 204; tanpa body |

Contoh body POST:

```json
{
  "customer_name": "Bu Sari",
  "product_name": "Plakat Akrilik",
  "quantity": 3,
  "unit_price": 25000,
  "due_date": "2030-01-15",
  "notes": "Untuk wisuda"
}
```

`status` boleh dihilangkan saat POST dan akan menjadi `pending`.
Saat PUT, kirim seluruh field wajib termasuk `status`.
Pilihan status: `pending`, `processing`, `completed`, `cancelled`.

- `customer_name` dan `product_name` wajib, string maksimal 255 karakter.
- `quantity` wajib, bilangan bulat 1–100000.
- `unit_price` wajib, bilangan bulat rupiah 0–1000000000.
- `due_date` wajib dalam format `YYYY-MM-DD`.
- `notes` opsional, maksimal 5000 karakter.

Backend menentukan `user_id` dari token dan menghitung
`total_price = quantity × unit_price` pada POST maupun PUT.
Nilai kedua field itu dari request klien diabaikan.

Tanpa token atau dengan token tidak valid, API mengembalikan JSON 401.
Pesanan milik pengguna lain diperlakukan seperti tidak ditemukan (404).
Input yang melanggar validasi menghasilkan JSON 422 dengan objek `errors`.
Feature test PostgreSQL mencakup CRUD, validasi, autentikasi, dan isolasi
data antar pengguna. Contoh request ada di
[Postman Collection](postman/orderly-api.postman_collection.json);
isi email, password, token, dan ID pesanan hanya di Postman lokal.
