<?php
use function Livewire\Volt\{state};
use Illuminate\Support\Facades\Artisan;
use function Laravel\Folio\name;
name('admin.profile.setting');


$createStorageLink = function(){
    Artisan::call('storage:link');
};
$clearcaches = function(){
    Artisan::call('optimize:clear');
};
$deleteAccount = function(){
    $user = \App\Models\User::findOrFail(Auth::user()->id);
    $user->delete();
    $route = route('home');
    return $this->redirect($route, navigate: true);
};
$logout = function(){
    auth()->logout();
    $route = route('home');
    return $this->redirect($route, navigate: true);
};
?>
<x-app>
    <x-toolbar-mobile 
        :breadcrumbs="[
            ['icon' => 'arrow-left', 'url' => route('admin.profile')],
            ['text' => 'Pengaturan', 'active' => true]
        ]"
    />
    @volt
    <div id="kt_app_content" class="app-content flex-column-fluid py-10">
        <div class="card shadow-sm mb-5">
            <div class="card-header collapsible cursor-pointer rotate" data-bs-toggle="collapse" data-bs-target="#pengaturan_akun">
                <h3 class="card-title">Pengaturan Akun</h3>
                <div class="card-toolbar rotate-180">
                    <i class="ki-duotone ki-down fs-1"></i>
                </div>
            </div>
            <div id="pengaturan_akun" class="collapse show">
                <a href="{{ route('admin.profile.edit') }}" wire:navigate class="card card-dashed hover-elevate-up shadow-sm text-decoration-none">
                    <div class="card-body d-flex align-items-center">
                        <i class="ki-filled ki-user fs-3x"></i>
                        <div class="ms-3">
                            <div class="text-gray-700 parent-hover-primary fs-4 fw-bold">
                                Ubah Profil
                            </div>
                            <div class="text-gray-500 parent-hover-primary fs-5 mt-1">
                                Atur identitas dan foto profil kamu
                            </div>
                        </div>
                    </div>
                </a>
                <a href="{{ route('admin.profile.security') }}" wire:navigate class="card card-dashed hover-elevate-up shadow-sm parent-hover text-decoration-none">
                    <div class="card-body d-flex align-items-center">
                        <i class="ki-filled ki-lock fs-3x"></i>
                        <div class="ms-3">
                            <div class="text-gray-700 parent-hover-primary fs-4 fw-bold">
                                Keamanan Akun
                            </div>
                            <div class="text-gray-500 parent-hover-primary fs-5 mt-1">
                                Kata sandi, & verifikasi data diri
                            </div>
                        </div>
                    </div>
                </a>
                <a href="{{ route('admin.profile.notification') }}" wire:navigate class="card card-dashed hover-elevate-up shadow-sm parent-hover text-decoration-none">
                    <div class="card-body d-flex align-items-center">
                        <i class="ki-filled ki-notification fs-3x"></i>
                        <div class="ms-3">
                            <div class="text-gray-700 parent-hover-primary fs-4 fw-bold">
                                Notifikasi
                            </div>
                            <div class="text-gray-500 parent-hover-primary fs-5 mt-1">
                                Atur segala jenis pesan notifikasi
                            </div>
                        </div>
                    </div>
                </a>
                <a href="{{ route('admin.profile.privacy') }}" wire:navigate class="card card-dashed hover-elevate-up shadow-sm parent-hover text-decoration-none">
                    <div class="card-body d-flex align-items-center">
                        <i class="ki-filled ki-shield-tick fs-3x"></i>
                        <div class="ms-3">
                            <div class="text-gray-700 parent-hover-primary fs-4 fw-bold">
                                Privasi Akun
                            </div>
                            <div class="text-gray-500 parent-hover-primary fs-5 mt-1">
                                Atur penggunaan data
                            </div>
                        </div>
                    </div>
                </a>
            </div>
        </div>
        <div class="card shadow-sm mb-5">
            <div class="card-header collapsible cursor-pointer rotate collapsed" data-bs-toggle="collapse" data-bs-target="#pengaturan_app">
                <h3 class="card-title">Pengaturan Aplikasi</h3>
                <div class="card-toolbar rotate-180">
                    <i class="ki-duotone ki-down fs-1"></i>
                </div>
            </div>
            <div id="pengaturan_app" class="collapse">
                <div class="card card-dashed hover-elevate-up parent-hover">
                    <div class="card-body d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center">
                            <i class="ki-filled ki-sun theme-light-show text-warning fs-3x"></i>
                            <i class="ki-filled ki-moon theme-dark-show fs-3x"></i>
                            <div class="ms-3">
                                <div class="text-gray-700 fs-4 fw-bold">
                                    Tampilan
                                </div>
                                <div class="text-gray-500 fs-5 mt-1">
                                    Atur tampilan warna di {{ config('app.name') }}
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-check form-switch form-check-custom form-check-warning form-check-solid">
                            <input class="form-check-input h-30px w-50px" type="checkbox" id="theme_mode_switch"  
                            />
                        </div>
                    </div>
                </div>
                <a onclick="createStorageLink();" wire:navigate class="card card-dashed hover-elevate-up parent-hover text-decoration-none">
                    <div class="card-body d-flex align-items-center">
                        <i class="ki-filled ki-paper-clip fs-3x"></i>
                        <div class="ms-3">
                            <div class="text-gray-700 parent-hover-primary fs-4 fw-bold">
                                Storage Link
                            </div>
                        </div>
                    </div>
                </a>
                <a onclick="clearCache();" wire:navigate class="card card-dashed hover-elevate-up parent-hover text-decoration-none">
                    <div class="card-body d-flex align-items-center">
                        <i class="ki-filled ki-eraser fs-3x"></i>
                        <div class="ms-3">
                            <div class="text-gray-700 parent-hover-primary fs-4 fw-bold">
                                Bersihkan Cache
                            </div>
                            <div class="text-gray-500 parent-hover-primary fs-5 mt-1">
                                Solusi cepat untuk atasi masalah aplikasi
                            </div>
                        </div>
                    </div>
                </a>
            </div>
        </div>
        <div class="card shadow-sm mb-5">
            <div class="card-header collapsible cursor-pointer rotate collapsed" data-bs-toggle="collapse" data-bs-target="#about_us">
                <h3 class="card-title">Seputar {{ config('app.name') }}</h3>
                <div class="card-toolbar rotate-180">
                    <i class="ki-duotone ki-down fs-1"></i>
                </div>
            </div>
            <div id="about_us" class="collapse">
                <a href="{{ route('about') }}" wire:navigate class="card card-dashed hover-elevate-up shadow-sm parent-hover">
                    <div class="card-body d-flex align-items">
                        <i class="ki-filled ki-information fs-3x"></i>
                        <span class="ms-3 parent-hover-primary fs-4 fw-bold">
                            Kenali {{ config('app.name') }}
                        </span>
                    </div>
                </a>
                <a href="{{ route('career') }}" wire:navigate class="card card-dashed hover-elevate-up shadow-sm parent-hover">
                    <div class="card-body d-flex align-items">
                        <i class="ki-filled ki-briefcase fs-3x"></i>
                        <span class="ms-3 parent-hover-primary fs-4 fw-bold">
                            Karir di {{ config('app.name') }}
                        </span>
                    </div>
                </a>
                <a href="{{ route('term-condition') }}" wire:navigate class="card hover-elevate-up shadow-sm parent-hover">
                    <div class="card-body d-flex align-items">
                        <i class="ki-filled ki-tablet-text-down fs-3x"></i>
                        <span class="ms-3 parent-hover-primary fs-4 fw-bold">
                            Syarat dan Ketentuan
                        </span>
                    </div>
                </a>
                <a href="{{ route('privacy-policy') }}" wire:navigate class="card hover-elevate-up shadow-sm parent-hover">
                    <div class="card-body d-flex align-items">
                        <i class="ki-filled ki-profile-circle fs-3x"></i>
                        <span class="ms-3 parent-hover-primary fs-4 fw-bold">
                            Hak Kekayaan Intelektual
                        </span>
                    </div>
                </a>
            </div>
        </div>
        <a onclick="logout();" class="card hover-elevate-up shadow-sm parent-hover mb-5">
            <div class="card-body d-flex align-items">
                <i class="ki-filled ki-exit-right fs-3x"></i>
                <span class="ms-3 parent-hover-primary fs-4 fw-bold">
                    Keluar Akun
                </span>
            </div>
        </a>
        <a onclick="deleteAccount();" class="card text-inverse-danger bg-danger hover-elevate-up shadow-sm parent-hover mb-3">
            <div class="card-body d-flex align-items">
                <i class="ki-filled ki-trash fs-3x"></i>
                <span class="ms-3 parent-hover-primary fs-4 fw-bold">
                    Hapus Akun
                </span>
            </div>
        </a>
        @section('custom_js')
            <script data-navigate-once>
                // Inisialisasi status toggle berdasarkan tema yang aktif
                document.addEventListener('DOMContentLoaded', function() {
                    const themeSwitch = document.getElementById('theme_mode_switch');
                    
                    // Set initial state based on current theme
                    themeSwitch.checked = (KTThemeMode.getMode() === 'light');
                    
                    // Tambahkan event listener untuk perubahan tema
                    themeSwitch.addEventListener('change', function() {
                        KTThemeMode.setMode(this.checked ? 'light' : 'dark');
                    });
                });
                document.addEventListener('livewire:navigated', function() {
                    const themeSwitch = document.getElementById('theme_mode_switch');
                    
                    // Set initial state based on current theme
                    themeSwitch.checked = (KTThemeMode.getMode() === 'light');
                    
                    // Tambahkan event listener untuk perubahan tema
                    themeSwitch.addEventListener('change', function() {
                        KTThemeMode.setMode(this.checked ? 'light' : 'dark');
                    });
                });
                function createStorageLink() {
                    Swal.fire({
                        title: 'Buat Storage Link?',
                        text: 'Ini akan membuat link antara storage dan folder public',
                        icon: 'info',
                        showCancelButton: true,
                        confirmButtonColor: '#28a745',
                        cancelButtonColor: '#6c757d',
                        confirmButtonText: 'Ya, Buat Link',
                        cancelButtonText: 'Batalkan',
                        reverseButtons: true,
                        backdrop: true,
                        allowOutsideClick: false,
                    }).then((result) => {
                        if (result.isConfirmed) {
                            Swal.fire({
                                title: 'Membuat Link...',
                                html: 'Sedang memproses permintaan Anda',
                                allowOutsideClick: false,
                                didOpen: () => {
                                    Swal.showLoading();
                                    @this.clearcaches().then(() => {
                                        Swal.fire({
                                            title: 'Berhasil!',
                                            text: 'Storage Link berhasil dibuat',
                                            icon: 'success',
                                            timer: 2000,
                                            timerProgressBar: true
                                        });
                                    }).catch(error => {
                                        Swal.fire({
                                            title: 'Gagal!',
                                            html: `Gagal membuat storage link: <br><span class="text-red-500">${error.message}</span>`,
                                            icon: 'error'
                                        });
                                    });
                                }
                            });
                        }
                    });
                }
                function clearCache() {
                    Swal.fire({
                        title: 'Bersihkan Cache Aplikasi?',
                        text: 'Ini akan menghapus data sementara dan mempercepat aplikasi',
                        icon: 'info',
                        showCancelButton: true,
                        confirmButtonColor: '#28a745',
                        cancelButtonColor: '#6c757d',
                        confirmButtonText: 'Ya, Bersihkan Cache',
                        cancelButtonText: 'Batalkan',
                        reverseButtons: true,
                        backdrop: true,
                        allowOutsideClick: false,
                    }).then((result) => {
                        if (result.isConfirmed) {
                            Swal.fire({
                                title: 'Membersihkan Cache...',
                                html: 'Sedang memproses permintaan Anda',
                                allowOutsideClick: false,
                                didOpen: () => {
                                    Swal.showLoading();
                                    @this.clearcaches().then(() => {
                                        Swal.fire({
                                            title: 'Berhasil!',
                                            text: 'Cache aplikasi telah dibersihkan',
                                            icon: 'success',
                                            timer: 2000,
                                            timerProgressBar: true
                                        });
                                    }).catch(error => {
                                        Swal.fire({
                                            title: 'Gagal!',
                                            html: `Gagal membersihkan cache: <br><span class="text-red-500">${error.message}</span>`,
                                            icon: 'error'
                                        });
                                    });
                                }
                            });
                        }
                    });
                }
                function logout() {
                    Swal.fire({
                        title: 'Konfirmasi Logout',
                        text: 'Anda akan keluar dari sistem dan harus login kembali untuk mengakses akun Anda.',
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Ya, Logout Sekarang',
                        cancelButtonText: 'Batalkan',
                        reverseButtons: true,
                        backdrop: true,
                        allowOutsideClick: false,
                        // preConfirm: () => {
                        //     const inputValue = document.getElementById('logoutConfirmation').value;
                        //     if (inputValue.toLowerCase() !== 'logout') {
                        //         Swal.showValidationMessage('Harap ketik "logout" untuk konfirmasi');
                        //         return false;
                        //     }
                        //     return true;
                        // }
                    }).then((result) => {
                        if (result.isConfirmed) {
                            Swal.fire({
                                title: 'Sedang Memproses...',
                                html: 'Anda akan keluar dari sistem',
                                allowOutsideClick: false,
                                didOpen: () => {
                                    Swal.showLoading();
                                    @this.logout().then(() => {
                                        Swal.fire({
                                            title: 'Logout Berhasil!',
                                            text: 'Anda telah keluar dari sistem',
                                            icon: 'success'
                                        });
                                    }).catch(error => {
                                        Swal.fire({
                                            title: 'Gagal Logout',
                                            html: `Terjadi kesalahan: <br><span class="text-red-500">${error.message}</span>`,
                                            icon: 'error'
                                        });
                                    });
                                }
                            });
                        }
                    });
                }
                function deleteAccount() {
                    Swal.fire({
                        title: 'Hapus Akun Permanen?',
                        html: `
                            <div class="text-left">
                                <p>Anda yakin ingin menghapus akun Anda? Tindakan ini akan:</p>
                                <ul class="list-disc pl-5">
                                    <li>Menghapus semua data Anda secara permanen</li>
                                    <li>Tidak dapat dikembalikan (irreversible)</li>
                                    <li>Menghentikan semua langganan aktif</li>
                                </ul>
                                <p class="mt-3 font-bold">Ketikan "<span class="text-red-500">konfirmasi</span>" untuk verifikasi:</p>
                                <input type="text" id="confirmationInput" class="swal2-input" placeholder="ketik konfirmasi...">
                            </div>
                        `,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#3085d6',
                        confirmButtonText: 'Ya, Hapus Akun',
                        cancelButtonText: 'Batalkan',
                        reverseButtons: true,
                        backdrop: true,
                        allowOutsideClick: false,
                        preConfirm: () => {
                            const inputValue = document.getElementById('confirmationInput').value;
                            if (inputValue.toLowerCase() !== 'konfirmasi') {
                                Swal.showValidationMessage('Harap ketik "konfirmasi" dengan benar');
                            }
                            return inputValue;
                        }
                    }).then((result) => {
                        if (result.isConfirmed) {
                            Swal.fire({
                                title: 'Menghapus Akun...',
                                html: 'Mohon tunggu, akun Anda sedang dihapus',
                                allowOutsideClick: false,
                                didOpen: () => {
                                    Swal.showLoading();
                                    @this.deleteAccount().then(() => {
                                        Swal.fire({
                                            title: 'Berhasil!',
                                            text: 'Akun Anda telah berhasil dihapus',
                                            icon: 'success'
                                        });
                                    }).catch(error => {
                                        Swal.fire({
                                            title: 'Gagal!',
                                            html: `Gagal menghapus akun: <br><span class="text-red-500">${error.message}</span>`,
                                            icon: 'error'
                                        });
                                    });
                                }
                            });
                        }
                    });
                }
            </script>
        @endsection
    </div>
    @endvolt
</x-app>