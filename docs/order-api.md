# API Pesanan

Base URL: `/api`. Semua endpoint pesanan memerlukan
`Authorization: Bearer <token>` dari `POST /api/login`.

| Method | Endpoint | Hasil |
| --- | --- | --- |
| GET | /orders | Daftar pesanan milik pengguna |
| GET | /orders/{id} | Detail pesanan milik pengguna |
| POST | /orders | Membuat pesanan; 201 |
| PUT | /orders/{id} | Memperbarui pesanan |
| DELETE | /orders/{id} | Menghapus pesanan; 204 |

Contoh body POST/PUT:

```json
{
  "customer_name": "Bu Sari",
  "product_name": "Plakat Akrilik",
  "quantity": 3,
  "unit_price": 25000,
  "status": "pending",
  "due_date": "2030-01-15",
  "notes": "Untuk wisuda"
}