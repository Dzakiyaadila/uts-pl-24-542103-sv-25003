# Domain Pesanan

Issue: https://github.com/Dzakiyaadila/uts-pl-24-542103-sv-25003/issues/15

## Status

Migrasi tabel `orders`, model, relasi, factory, dan tes domain tersedia.
Endpoint CRUD serta integrasi Vue belum diimplementasikan pada tahap ini.

## Skema

| Kolom | Tipe | Aturan |
| --- | --- | --- |
| id | bigint | Kunci utama |
| user_id | bigint | Foreign key wajib ke users; order terhapus saat pemilik dihapus |
| customer_name | string | Nama pelanggan |
| product_name | string | Satu produk souvenir per order |
| quantity | integer | Jumlah unit; validasi nilai positif pada API berikutnya |
| unit_price | bigint | Harga satuan dalam rupiah bulat |
| total_price | bigint | Total rupiah; akan dihitung backend dari quantity × unit_price |
| status | string(20) | Awal `pending`; pilihan status divalidasi pada API berikutnya |
| due_date | date | Tanggal tenggat, wajib |
| notes | text nullable | Catatan opsional |
| created_at, updated_at | timestamp | Waktu pencatatan dan perubahan |

Relasi: satu `User` mempunyai banyak `Order`, dan setiap `Order`
milik satu `User`. `user_id` dan `total_price` tidak termasuk
atribut mass assignment model. API berikutnya mengambil pemilik dari
token terautentikasi dan menghitung total di backend; klien tidak
menentukan kedua nilai tersebut.

## Verifikasi

Migrasi development dijalankan di PostgreSQL `orderly_dev`.
Tes dengan `RefreshDatabase` memakai PostgreSQL `orderly_test`,
mencakup kepemilikan, penyimpanan bilangan bulat, dan penghapusan
order saat pengguna dihapus.

Dari `backend/`:

```bash
php artisan migrate
php artisan test
./vendor/bin/pint --test
```

Simpan screenshot migrasi dan hasil tes untuk laporan tanpa
menampilkan kredensial database.
