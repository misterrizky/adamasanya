# Panduan Teknis

## API Endpoint Utama
| Endpoint                      | Method | Deskripsi                         |
|-------------------------------|--------|----------------------------------|
| /api/auth/register             | POST   | Registrasi user baru              |
| /api/auth/login                | POST   | Login user                       |
| /api/product/{id}/unavailable-dates | GET    | Mendapatkan tanggal tidak tersedia|

## Livewire Components Utama
- AuthLogin: Form login user
- KycStep1 - KycStep4: Proses pengisian KYC
- ProductRental: Pilih dan transaksi produk rental

## Integrasi Midtrans
- Webhook dipasang di route `/api/midtrans/webhook`
- Payment status otomatis update status transaksi

## PWA Setup
- Service Worker di `public/service-worker.js`
- Push notification menggunakan Firebase Cloud Messaging (FCM)
