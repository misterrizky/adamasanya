<?php
use function Livewire\Volt\{state};
?>
<div id="kt_app_footer" class="app-footer py-6 mt-10">
    <div class="container d-none d-xl-block">
        <div class="row align-items-center">
            <!-- Brand and copyright -->
            <div class="col-lg-4 mb-4 mb-lg-0">
                <div class="d-flex align-items-center">
                    <a href="{{ route('home') }}" wire:navigate class="d-flex align-items-center text-decoration-none">
                        <span class="fs-4 fw-bold text-primary">
                            <img alt="Logo" src="{{ asset('media/icons/logo.png') }}" class="h-60px" />
                        </span>
                    </a>
                    <span class="text-muted mx-2">|</span>
                    <span class="text-muted small">© 2025 All rights reserved</span>
                </div>
                <div class="mt-3">
                    <!-- Social media links would go here -->
                </div>
            </div>
            <!-- Navigation links -->
            <div class="col-lg-8">
                <div class="row">
                    <div class="col-4 mb-4 mb-md-0">
                        <h5 class="fw-semibold mb-3">Perusahaan</h5>
                        <ul class="nav flex-column">
                            <li class="nav-item mb-2">
                                <a href="{{ route('about') }}" wire:navigate class="nav-link p-0 text-muted text-hover-primary">Tentang Kami</a>
                            </li>
                            <li class="nav-item mb-2">
                                <a href="{{ route('career') }}" wire:navigate class="nav-link p-0 text-muted text-hover-primary">Karir</a>
                            </li>
                            <li class="nav-item mb-2">
                                <a href="{{ route('contact') }}" wire:navigate class="nav-link p-0 text-muted text-hover-primary">Kontak</a>
                            </li>
                        </ul>
                    </div>
                    <div class="col-4 mb-4 mb-md-0">
                        <h5 class="fw-semibold mb-3">Bantuan</h5>
                        <ul class="nav flex-column">
                            <li class="nav-item mb-2">
                                <a href="{{ route('term-refund') }}" wire:navigate class="nav-link p-0 text-muted text-hover-primary">Customer Care</a>
                            </li>
                            <li class="nav-item mb-2">
                                <a href="#" class="nav-link p-0 text-muted text-hover-primary">FAQ</a>
                            </li>
                        </ul>
                    </div>
                    <div class="col-4">
                        <h5 class="fw-semibold mb-3">Legal</h5>
                        <ul class="nav flex-column">
                            <li class="nav-item mb-2">
                                <a href="{{ route('term-condition') }}" wire:navigate class="nav-link p-0 text-muted text-hover-primary">Syarat & Ketentuan</a>
                            </li>
                            <li class="nav-item mb-2">
                                <a href="{{ route('privacy-policy') }}" wire:navigate class="nav-link p-0 text-muted text-hover-primary">Kebijakan Privasi</a>
                            </li>
                            <li class="nav-item mb-2">
                                <a href="{{ route('term-refund') }}" wire:navigate class="nav-link p-0 text-muted text-hover-primary">Kebijakan Refund</a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <div class="mt-5 pt-4 border-top text-justify text-muted small">
            <p class="d-none d-sm-block">{{ config('app.desc') }}</p>
        </div>
    </div>
</div>