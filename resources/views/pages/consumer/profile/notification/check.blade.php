<?php
use Illuminate\Support\Facades\Cache;
use function Laravel\Folio\name;
name('profile.push-notification-check');

$refreshToken = function(){
    $newToken = 'TOKEN-' . strtoupper(str_random(8)); // atau gunakan UUID
    // Simpan ke database jika perlu
    return response()->json([
        'status' => 'success',
        'token' => $newToken
    ]);
};
$clearCaches = function(){
    Cache::flush();

    return response()->json([
        'status' => 'success',
        'message' => 'Cache berhasil dibersihkan.'
    ]);
};
?>

<x-app>
    <x-toolbar-mobile 
        :breadcrumbs="[
            ['icon' => 'arrow-left', 'url' => route('profile.push-notification')],
            ['text' => 'Push Notification', 'active' => true]
        ]"
    />
    @volt
    <div id="kt_app_content" class="app-content flex-column-fluid">
        <!-- Success Header Section -->
        <div class="text-center my-6">
            <div class="p-4 bg-success bg-opacity-10 rounded-3 d-inline-block mb-4">
                <div class="mx-auto bg-success bg-opacity-20 rounded-circle p-4 d-flex align-items-center justify-content-center" style="width: 120px; height: 120px">
                    <i class="ki-filled ki-check-circle text-success fs-1"></i>
                </div>
            </div>
            <h3 class="fw-bold text-dark mb-2">Push Notification Aktif</h3>
            <span class="text-muted fw-semibold fs-6">Anda akan menerima notifikasi dari {{ config('app.name') }}</span>
        </div>

        <!-- Notification Settings Card -->
        <div class="card card-flush shadow-sm mb-6">
            <div class="card-header pt-5">
                <h3 class="card-title fw-bold text-dark">Pengaturan Notifikasi</h3>
            </div>
            <div class="card-body pt-0">
                <!-- Notification Item -->
                <div id="notification-status" class="d-flex align-items-center p-4 rounded-3 bg-light-success bg-opacity-10 mb-4">
                    <div class="symbol symbol-50px me-5">
                        <span id="notification-bg-icon" class="symbol-label bg-danger bg-opacity-10">
                            <i id="notification-icon" class="ki-filled ki-cross fs-2x text-danger"></i>
                        </span>
                    </div>
                    <div class="d-flex flex-column flex-grow-1 pe-5">
                        <span class="fw-bold fs-5 text-gray-900">Notifikasi {{ config('app.name') }}</span>
                        <span id="notification-text" class="text-danger fw-semibold"></span>
                    </div>
                    <button id="enable-notification" class="btn btn-sm btn-warning">Aktifkan</button>
                </div>

                <!-- Notification Item -->
                <div id="sound-status" class="d-flex align-items-center p-4 rounded-3 bg-light-danger bg-opacity-10 mb-4">
                    <div class="symbol symbol-50px me-5">
                        <span class="symbol-label bg-danger bg-opacity-10">
                            <i class="ki-filled ki-cross fs-2x text-danger"></i>
                        </span>
                    </div>
                    <div class="d-flex flex-column flex-grow-1 pe-5">
                        <span class="fw-bold fs-5 text-gray-900">Notifikasi HP</span>
                        <span class="text-danger fw-semibold">Tidak Aktif</span>
                    </div>
                    <button class="btn btn-sm btn-warning">Aktifkan</button>
                </div>

                <!-- Notification Item -->
                <div class="d-flex align-items-center p-4 rounded-3 bg-light-danger bg-opacity-10 mb-4">
                    <div class="symbol symbol-50px me-5">
                        <span id="sound-bg-icon" class="symbol-label bg-danger bg-opacity-10">
                            <i id="sound-icon" class="ki-filled ki-cross fs-2x text-danger"></i>
                        </span>
                    </div>
                    <div class="d-flex flex-column flex-grow-1 pe-5">
                        <span class="fw-bold fs-5 text-gray-900">Bunyi Notifikasi</span>
                        <span id="sound-text" class="text-danger fw-semibold">Tidak Aktif</span>
                    </div>
                    <button id="enable-sound" class="btn btn-sm btn-warning">Aktifkan</button>
                </div>

                <!-- Token Refresh -->
                <div class="d-flex align-items-center p-4 rounded-3 bg-light-warning bg-opacity-10">
                    <div class="symbol symbol-50px me-5">
                        <span class="symbol-label bg-warning bg-opacity-10">
                            <i class="ki-filled ki-information fs-2x text-warning"></i>
                        </span>
                    </div>
                    <div class="d-flex flex-column flex-grow-1 pe-5">
                        <span class="fw-bold fs-5 text-gray-900">Refresh Token</span>
                        <span class="text-muted fs-7">Perbarui token notifikasi Anda</span>
                    </div>
                    <button wire:click="refreshToken" class="btn btn-sm btn-light-warning">
                        Refresh
                    </button>
                </div>
            </div>
        </div>

        <!-- Help Section -->
        <div class="card card-flush shadow-sm bg-light-primary bg-opacity-10 border-0 mb-6">
            <div class="card-body p-6">
                <div class="d-flex align-items-center mb-4">
                    <div class="symbol symbol-40px me-4">
                        <span class="symbol-label bg-primary bg-opacity-10">
                            <i class="ki-filled ki-question fs-2x text-primary"></i>
                        </span>
                    </div>
                    <h4 class="fw-bold text-gray-900 mb-0">Masalah Notifikasi?</h4>
                </div>
                
                <p class="text-gray-700 mb-4">Jika Anda belum menerima notifikasi tes, coba bersihkan cache untuk memastikan Anda tetap menerima notifikasi dari {{ config('app.name') }}.</p>
                
                <button id="btnCache" class="btn btn-primary w-100 mb-4">
                    <i class="ki-outline ki-trash fs-3 me-2"></i> Bersihkan Cache
                </button>
                
                <div class="text-center">
                    <span class="text-muted fs-7">Butuh bantuan lebih lanjut?</span>
                    <a href="#" class="text-primary fw-semibold ms-2">Kunjungi Pusat Bantuan</a>
                </div>
            </div>
        </div>

        <!-- Token Info -->
        <div class="text-center p-4 bg-light-info bg-opacity-10 rounded-3 mb-10">
            <span class="text-muted fs-7 me-2">Token saat ini:</span>
            <span class="badge badge-light-info fw-bold fs-6">MCRYx22s</span>
            <div class="mt-2">
                <span class="text-success fs-8 fw-semibold">
                    <i class="ki-outline ki-check-circle fs-4 text-success me-1"></i> Token terbaru
                </span>
            </div>
        </div>
    </div>
    @endvolt
    @section('custom_js')
    <script data-navigate-once>
        checkNotificationPermission();
        checkSoundPermission();
        document.getElementById("enable-notification").addEventListener("click", requestNotificationPermission);
        document.getElementById("enable-sound").addEventListener("click", requestMicrophonePermission);
    </script>
    @endsection
</x-app>