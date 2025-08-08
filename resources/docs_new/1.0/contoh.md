# Dokumentasi Penggunaan Template Metronic dengan Laravel

---

- [1. Struktur Komponen](#struktur-komponen)
- [2. Komponen Utama](#komponen-utama)
    - [- MetronicApp](#metronic-layout)
    - [- MetronicToolbar](#metronic-toolbar)
- [3. Implementasi Layout](#implementasi-layout)
- [4. Customization](#customization)
- [5. Troubleshooting](#troubleshooting)
- [6. Contoh Penggunaan](#contoh-penggunaan)

<a name="struktur-komponen"></a>
## Struktur Komponen

```bash
resources/views/components/metronic/
├── app.blade.php         # Layout utama
├── toolbar.blade.php     # Komponen toolbar
├── sidebar/             # Komponen sidebar
│   ├── default.blade.php
│   └── compact.blade.php
├── header.blade.php      # Komponen header
└── shared/              # Assets shared
    ├── scripts.blade.php
    └── styles.blade.php
```
<a name="komponen-utama"></a>
## Komponen Utama

<a name="metronic-layout"></a>
## MetronicLayout
Layout Utama Aplikasi

Props:

| Prop | Type | Default | Deskripsi |
| :- |   :-   |  :-  | :- |
| theme | string | 'light' | 'light' atau 'dark' |
| layout | string | 'default' | Tipe Layout |
| demo | string | '1' | Versi Layout Metronic |

Contoh:
```php
<x-metronic-layout theme="dark" demo="1">
    <!-- Konten aplikasi -->
</x-metronic-layout>
```

<a name="metronic-toolbar"></a>
## MetronicToolbar
Komponen toolbar dinamis.

Props:

| Prop | Type | Default | Deskripsi |
| :- |   :-   |  :-  | :- |
| title | string | required | Judul Halaman |
| breadcrumbs | array | [] | Array Breadcrumbs |
| buttons | array | [] | Tombol Aksi |

Contoh Breadcrumb:
```php
:breadcrumbs="[
    ['text' => 'Home', 'url' => '/'],
    ['text' => 'Dashboard', 'active' => true]
]"
```
Contoh Button:
```php
:buttons="[
    ['text' => 'Tambah', 'type' => 'primary', 'url' => '#']
]
```

<a name="implementasi-layout"></a>
## Implementasi Layout
Layout Dasar
```php
<x-metronic-app>
    <x-metronic-header />
    
    <x-metronic-sidebar type="default" />
    
    <div class="app-wrapper">
        <x-metronic-toolbar title="Dashboard" />
        
        <div class="app-content">
            {{ $slot }}
        </div>
    </div>
</x-metronic-app>
```

Custom Sidebar
```php
<x-metronic-sidebar type="compact" />
```

<a name="customization"></a>
## Customization 
Menambahkan Style
```php
@push('styles')
    <style>
        .custom-class {
            color: var(--kt-primary);
        }
    </style>
@endpush
```
Menambahkan Script
```php
@push('scripts')
    <script>
        KTUtil.onDOMContentLoaded(function() {
            // Kode custom
        });
    </script>
@endpush
```

<a name="troubleshooting"></a>
## Troubleshooting
Problem: Komponen tidak dikenali

Pastikan komponen terdaftar di AppServiceProvider.php

<a name="contoh-penggunaan"></a>
## Contoh Penggunaan

```php
<x-metronic-app demo="demo7" theme="light">
    <x-metronic-header />
    
    <x-metronic-sidebar type="compact" />
    
    <div class="app-wrapper">
        <x-metronic-toolbar 
            title="Laporan Penjualan"
            :breadcrumbs="[
                ['text' => 'Home', 'url' => '/'],
                ['text' => 'Laporan', 'active' => true]
            ]"
            :buttons="[
                ['text' => 'Export', 'type' => 'secondary'],
                ['text' => 'Cetak', 'type' => 'primary']
            ]"
        />
        
        <div class="app-content">
            <div class="container-xxl">
                <!-- Konten halaman -->
            </div>
        </div>
    </div>
</x-metronic-app>
```