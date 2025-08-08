# Setup Lingkungan Pengembangan

## Persyaratan Sistem
- PHP 8.1+
- Composer
- MySQL
- Node.js (jika ada frontend build)
- Git

## Langkah Installasi
```bash
git clone https://github.com/your-repo.git
cd your-repo
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan serve
```

## Struktur Folder
- app/ (logika backend)
- resources/views/livewire (komponen Livewire)
- database/migrations
- routes/web.php

## Manajemen Branch Git
- main untuk produksi
- feature/* untuk pengembangan fitur
- pull request wajib untuk merge
