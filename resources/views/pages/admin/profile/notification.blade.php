<?php
use function Laravel\Folio\name;
name('admin.profile.notification');
?>
<x-app>
    <x-toolbar-mobile 
        :breadcrumbs="[
            ['icon' => 'arrow-left', 'url' => route('admin.profile.setting')],
            ['text' => 'Notifikasi', 'active' => true]
        ]"
    />
    @volt
    <div id="kt_app_content" class="app-content flex-column-fluid">
        <div class="card shadow-sm mb-5">
            <div class="card-header">
                <p class="card-title">
                    Atur bagaimana kamu menerima notifikasi berbelanja dan beraktivitas di {{ config('app.name') }}
                </p>
            </div>
            <div id="pengaturan_akun" class="collapse show">
                <a href="{{ route('profile.push-notification') }}" wire:navigate class="card card-dashed hover-elevate-up shadow-sm text-decoration-none">
                    <div class="card-body d-flex align-items-center">
                        <i class="ki-filled ki-notification fs-3x"></i>
                        <div class="ms-3">
                            <div class="text-gray-700 parent-hover-primary fs-4 fw-bold">
                                Push Notification
                            </div>
                        </div>
                    </div>
                </a>
                <a href="{{ route('profile.pin') }}" wire:navigate class="card card-dashed hover-elevate-up shadow-sm parent-hover text-decoration-none">
                    <div class="card-body d-flex align-items-center">
                        <i class="ki-filled ki-directbox-default fs-3x"></i>
                        <div class="ms-3">
                            <div class="text-gray-700 parent-hover-primary fs-4 fw-bold">
                                E-mail
                            </div>
                        </div>
                    </div>
                </a>
                <a href="{{ route('profile.biometric') }}" wire:navigate class="card card-dashed hover-elevate-up shadow-sm parent-hover text-decoration-none">
                    <div class="card-body d-flex align-items-center">
                        <i class="ki-filled ki-sms fs-3x"></i>
                        <div class="ms-3">
                            <div class="text-gray-700 parent-hover-primary fs-4 fw-bold">
                                SMS
                            </div>
                        </div>
                    </div>
                </a>
            </div>
        </div>
    </div>
    @endvolt
</x-app>