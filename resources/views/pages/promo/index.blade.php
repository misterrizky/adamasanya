<?php

use App\Models\Promo;
use function Livewire\Volt\{computed, state};
use function Laravel\Folio\name;

name('promo');

$promos = computed(function(){
    return Promo::where('is_active', 1)
                ->where('start_date', '<=', now())
                ->where('end_date', '>=', now())
                ->get();
});
$coupons = computed(function(){
    return Promo::where('type', 'percentage')
                ->where('is_active', 1)
                ->where('start_date', '<=', now())
                ->where('end_date', '>=', now())
                ->get();
});
?>
<style>
    .promo-card, .coupon-card {
        transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
    }
    .promo-card:hover, .coupon-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 16px rgba(0,0,0,0.1) !important;
    }
    .nav-tabs .nav-link {
        border-radius: 0.5rem;
        margin-right: 0.5rem;
        padding: 0.75rem 1.5rem;
        font-weight: 500;
    }
    .nav-tabs .nav-link.active {
        background-color: #e9f7ef;
        border-color: #28a745;
    }
    @media (max-width: 576px) {
        .card-body {
            padding: 1rem !important;
        }
        .nav-tabs .nav-link {
            padding: 0.5rem 1rem;
            font-size: 0.9rem;
        }
        .card-title {
            font-size: 1.1rem;
        }
    }
</style>
<x-app>
    @volt
    <div id="kt_app_content" class="app-content flex-column-fluid py-5">
        <div class="container">
            <!-- Banner Promo Spesial -->
            <div class="card bg-success text-white mb-5 rounded-3 shadow-sm position-relative overflow-hidden">
                <div class="card-body p-4 p-md-5">
                    <h3 class="card-title mb-2">Promo Spesial Hari Ini</h3>
                    <p class="mb-0">Dapatkan penawaran terbaik untuk penyewaan dan pembelian elektronik!</p>
                    <span class="badge bg-warning text-dark position-absolute top-0 end-0 m-3">
                        <i class="fas fa-percentage"></i> Hemat Hingga 50%
                    </span>
                </div>
            </div>

            <!-- Tab Navigation -->
            <ul class="nav nav-tabs mb-4" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active text-success" id="promo-tab-btn" data-bs-toggle="tab" data-bs-target="#promo-tab" type="button" role="tab" aria-controls="promo-tab" aria-selected="true">
                        <i class="fas fa-tag me-2"></i>Promo
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link text-success" id="coupon-tab-btn" data-bs-toggle="tab" data-bs-target="#coupon-tab" type="button" role="tab" aria-controls="coupon-tab" aria-selected="false">
                        <i class="fas fa-ticket me-2"></i>Kupon
                    </button>
                </li>
            </ul>

            <!-- Tab Content -->
            <div class="tab-content">
                <!-- Promo Tab -->
                <div class="tab-pane fade show active" id="promo-tab" role="tabpanel" aria-labelledby="promo-tab-btn">
                    <div class="card shadow-sm border-0 rounded-3">
                        <div class="card-header bg-primary text-white rounded-top-3">
                            <h5 class="card-title mb-0">Promo Puncak Happy Hour</h5>
                        </div>
                        <div class="card-body p-4">
                            @if ($this->promos->isEmpty())
                                <div class="alert alert-info text-center" role="alert">
                                    Tidak ada promo aktif saat ini.
                                </div>
                            @else
                                <div class="row row-cols-1 row-cols-md-2 g-4">
                                    @foreach ($this->promos as $promo)
                                        <div class="col">
                                            <div class="card h-100 shadow-sm border-0 promo-card">
                                                <div class="row g-0">
                                                    <div class="col-md-4">
                                                        <img src="/images/promo_{{ $promo->id }}.jpg" class="img-fluid rounded-start" alt="{{ $promo->name }}" loading="lazy">
                                                    </div>
                                                    <div class="col-md-8">
                                                        <div class="card-body">
                                                            <h5 class="card-title">{{ $promo->name }}</h5>
                                                            <p class="card-text">
                                                                @if ($promo->type === 'percentage')
                                                                    <span class="badge bg-danger me-2">{{ $promo->value }}% Off</span>
                                                                @elseif ($promo->type === 'buy_x_get_y')
                                                                    <span class="badge bg-info me-2">Sewa {{ $promo->buy_quantity }} Hari, Gratis {{ $promo->get_quantity }} Hari</span>
                                                                @endif
                                                                <br>
                                                                <small class="text-muted">Kode: {{ $promo->code }}</small><br>
                                                                <small class="text-muted">Berlaku hingga: {{ $promo->end_date->format('d M Y') }}</small>
                                                            </p>
                                                            <button type="button" class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#promoModal{{ $promo->id }}">Gunakan Promo</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Promo Modal -->
                                        <div class="modal fade" id="promoModal{{ $promo->id }}" tabindex="-1" aria-labelledby="promoModalLabel{{ $promo->id }}" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered">
                                                <div class="modal-content">
                                                    <div class="modal-header bg-primary text-white">
                                                        <h5 class="modal-title" id="promoModalLabel{{ $promo->id }}">{{ $promo->name }}</h5>
                                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <p><strong>Kode Promo:</strong> {{ $promo->code }}</p>
                                                        <p><strong>Deskripsi:</strong> 
                                                            @if ($promo->type === 'percentage')
                                                                Diskon {{ $promo->value }}% untuk pembelian atau penyewaan elektronik.
                                                            @elseif ($promo->type === 'buy_x_get_y')
                                                                Sewa {{ $promo->buy_quantity }} hari, gratis {{ $promo->get_quantity }} hari tambahan.
                                                            @endif
                                                        </p>
                                                        <p><strong>Berlaku hingga:</strong> {{ $promo->end_date->format('d M Y') }}</p>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                                                        <a href="/apply-promo/{{ $promo->code }}" class="btn btn-primary">Gunakan Sekarang</a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Coupon Tab -->
                <div class="tab-pane fade" id="coupon-tab" role="tabpanel" aria-labelledby="coupon-tab-btn">
                    <div class="card shadow-sm border-0 rounded-3">
                        <div class="card-header bg-secondary text-white rounded-top-3">
                            <h5 class="card-title mb-0">Kupon Terbaik dari Bank</h5>
                        </div>
                        <div class="card-body p-4">
                            @if ($this->coupons->isEmpty())
                                <div class="alert alert-info text-center" role="alert">
                                    Tidak ada kupon aktif saat ini.
                                </div>
                            @else
                                <div class="row row-cols-1 row-cols-md-2 g-4">
                                    @foreach ($this->coupons as $coupon)
                                        <div class="col">
                                            <div class="card h-100 shadow-sm border-0 coupon-card">
                                                <div class="row g-0">
                                                    <div class="col-md-4">
                                                        <img src="/images/coupon_{{ $coupon->id }}.jpg" class="img-fluid rounded-start" alt="{{ $coupon->name }}" loading="lazy">
                                                    </div>
                                                    <div class="col-md-8">
                                                        <div class="card-body">
                                                            <h5 class="card-title">{{ $coupon->name }}</h5>
                                                            <p class="card-text">
                                                                <span class="badge bg-success me-2">Diskon {{ $coupon->value }}% s.d. Rp{{ number_format($coupon->max_uses, 0, ',', '.') }}</span>
                                                                <br>
                                                                <small class="text-muted">Min. Transaksi Rp{{ number_format($coupon->min_order_amount, 0, ',', '.') }}</small><br>
                                                                <small class="text-muted">Kode: {{ $coupon->code }}</small>
                                                            </p>
                                                            <button type="button" class="btn btn-outline-success btn-sm" data-bs-toggle="modal" data-bs-target="#couponModal{{ $coupon->id }}">{{ strtoupper($coupon->code) }}</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Coupon Modal -->
                                        <div class="modal fade" id="couponModal{{ $coupon->id }}" tabindex="-1" aria-labelledby="couponModalLabel{{ $coupon->id }}" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered">
                                                <div class="modal-content">
                                                    <div class="modal-header bg-secondary text-white">
                                                        <h5 class="modal-title" id="couponModalLabel{{ $coupon->id }}">{{ $coupon->name }}</h5>
                                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <p><strong>Kode Kupon:</strong> {{ $coupon->code }}</p>
                                                        <p><strong>Deskripsi:</strong> Diskon {{ $coupon->value }}% hingga Rp{{ number_format($coupon->max_uses, 0, ',', '.') }} untuk transaksi minimal Rp{{ number_format($coupon->min_order_amount, 0, ',', '.') }}.</p>
                                                        <p><strong>Berlaku hingga:</strong> {{ $coupon->end_date->format('d M Y') }}</p>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                                                        <a href="/apply-coupon/{{ $coupon->code }}" class="btn btn-success">Gunakan Sekarang</a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sticky Call-to-Action -->
            <div class="position-fixed bottom-0 end-0 m-3 d-none d-md-block">
                <a href="/shop" class="btn btn-primary btn-lg rounded-pill shadow">
                    <i class="fas fa-shopping-cart me-2"></i>Belanja Sekarang
                </a>
            </div>
        </div>

        <!-- Custom CSS -->
    </div>
    @endvolt
</x-app>