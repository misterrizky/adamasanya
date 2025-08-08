<?php
use function Laravel\Folio\name;
name('profile.security');
?>
<x-app>
    <x-toolbar-mobile 
        :breadcrumbs="[
            ['icon' => 'arrow-left', 'url' => route('profile.setting')],
            ['text' => 'Keamanan Akun', 'active' => true]
        ]"
    />
    @volt
    <div id="kt_app_content" class="app-content flex-column-fluid py-10">
        <div class="card shadow-sm mb-5">
            <div class="card-header cursor-pointer rotate">
                <h3 class="card-title">Keamanan</h3>
            </div>
            <div id="pengaturan_akun" class="collapse show">
                <a href="{{ route('profile.edit-password') }}" wire:navigate class="card card-dashed hover-elevate-up shadow-sm text-decoration-none">
                    <div class="card-body d-flex align-items-center">
                        <i class="ki-filled ki-password-check fs-3x"></i>
                        <div class="ms-3">
                            <div class="text-gray-700 parent-hover-primary fs-4 fw-bold">
                                Ubah Kata Sandi
                            </div>
                            <div class="text-gray-500 parent-hover-primary fs-5 mt-1">
                                Ganti password akun Anda untuk keamanan yang lebih baik
                            </div>
                        </div>
                    </div>
                </a>
                <a href="{{ route('profile.pin') }}" wire:navigate class="card card-dashed hover-elevate-up shadow-sm parent-hover text-decoration-none">
                    <div class="card-body d-flex align-items-center">
                        <i class="ki-filled ki-shield-tick fs-3x"></i>
                        <div class="ms-3">
                            <div class="text-gray-700 parent-hover-primary fs-4 fw-bold">
                                PIN Adamasanya
                            </div>
                            <div class="text-gray-500 parent-hover-primary fs-5 mt-1">
                                Kelola PIN untuk transaksi yang lebih aman
                            </div>
                        </div>
                    </div>
                </a>
                @role('Super Admin|Owner|Cabang|Pegawai|Konsumen')
                <a href="{{ route('profile.biometric') }}" wire:navigate class="card card-dashed hover-elevate-up shadow-sm parent-hover text-decoration-none">
                    <div class="card-body d-flex align-items-center">
                        <i class="ki-filled ki-fingerprint-scanning fs-3x"></i>
                        <div class="ms-3">
                            <div class="text-gray-700 parent-hover-primary fs-4 fw-bold">
                                Sidik Jari
                            </div>
                            <div class="text-gray-500 parent-hover-primary fs-5 mt-1">
                                Aktifkan login dengan sidik jari untuk akses lebih cepat
                            </div>
                        </div>
                    </div>
                </a>
                @elserole('Onboarding')
                <a href="{{ route('profile.verification') }}" wire:navigate class="card card-dashed hover-elevate-up shadow-sm parent-hover text-decoration-none">
                    <div class="card-body d-flex align-items-center">
                        <i class="ki-filled ki-face-id fs-3x"></i>
                        <div class="ms-3">
                            <div class="text-gray-700 parent-hover-primary fs-4 fw-bold">
                                Verifikasi Data Diri
                            </div>
                            <div class="text-gray-500 parent-hover-primary fs-5 mt-1">
                                Lengkapi verifikasi identitas untuk sewa alat atau buka cabang
                            </div>
                        </div>
                    </div>
                </a>
                @endrole
            </div>
        </div>
    </div>
    @endvolt
</x-app>