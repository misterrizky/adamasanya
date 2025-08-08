# Dokumentasi API

## Autentikasi
### POST /api/auth/register
Registrasi user baru dengan data lengkap.

### POST /api/auth/login
Login user, menghasilkan token autentikasi.

## Produk
### GET /api/product/{id}/unavailable-dates
Mendapatkan daftar tanggal dimana produk tidak tersedia (full booked).

## Transaksi
### POST /api/transaction/rent
Membuat transaksi sewa produk.

### GET /api/transaction/{id}
Melihat detail transaksi.

## Verifikasi Midtrans
### POST /api/midtrans/webhook
Endpoint webhook untuk update status pembayaran otomatis.
