<?php
use Carbon\Carbon;
use App\Models\Promo;
use function Laravel\Folio\name;
use LevelUp\Experience\Models\Achievement;
use function Livewire\Volt\{computed, state};

name('profile');
state([
    'user' => fn() => Auth::user(),
    'roles' => fn() => Auth::user()->getRoleNames()[0]
]);
$promo = computed(function() {
    $now = now();
    $dayOfWeek = $now->dayOfWeek;
    $isWeekend = in_array($dayOfWeek, [Carbon::FRIDAY, Carbon::SATURDAY, Carbon::SUNDAY]);

    return Promo::where('is_active', true)
        ->where('start_date', '<=', $now)
        ->where('end_date', '>=', $now)
        ->where(function($query) use ($isWeekend) {
            $query->where('day_restriction', 'all')
                ->orWhere('day_restriction', $isWeekend ? 'weekend' : 'weekday');
        })
        ->first();
});
// $rentNotRating = computed(function() {
//     return Auth::user()->rents()->where('st', 'returned')->whereDoesntHave('ratingRent')->count();
// });

// $saleNotRating = computed(function() {
//     return Auth::user()->sales()->where('st', 'delivered')->whereDoesntHave('ratingSale')->count();
// });

$recommendForYou = computed(function() {
    return \App\Models\ProductBranch::inRandomOrder()->limit(20)->get();
});
?>
<x-app>
    @volt
    <div id="kt_app_content" class="app-content flex-column-fluid py-10">
        <div class="container py-4">
            <!-- Profil -->
            <div class="d-flex align-items-center justify-content-between mb-3" data-aos="fade-down">
                <div class="d-flex align-items-center">
                    <div class="symbol symbol-60px symbol-lg-100px symbol-fixed position-relative me-4">
                        <img src="{{ Auth::user()->image }}" alt="{{ Auth::user()->name }}" />
                        <div class="position-absolute translate-middle bottom-0 start-100 mb-6 bg-success rounded-circle border border-4 border-body h-20px w-20px"></div>
                    </div>
                    <div>
                        <h5 class="mb-0">
                            {{ Auth::user()->name }}
                        </h5>
                        {{-- <button class="btn btn-success btn-sm mt-1">Sambungin ke TikTok Shop</button> --}}
                    </div>
                </div>
                <div>
                    <a href="{{ route('contact') }}" class="btn btn-link btn-color-muted btn-active-color-primary" wire:navigate>
                        <i class="ki-filled ki-delivery-24 fs-3x me-2"></i>
                    </a>
                    <a href="{{ route('profile.setting') }}" class="btn btn-link btn-color-muted btn-active-color-primary" wire:navigate>
                        <i class="ki-filled ki-setting-2 fs-3x"></i>
                    </a>
                </div>
            </div>
            @if($this->promo)
            <!-- Promo -->
            <div class="alert alert-success rounded-box" role="alert" data-aos="fade-right">
                <strong>{{ $this->promo->name }}</strong>! <span class="text-muted d-block">Spesial buatmu, penawaran terbatas</span>
            </div>
            @endif

            <!-- Transaksi -->
            <div class="p-3 mb-3 rounded-box shadow-sm d-none" data-aos="fade-up">
                <h6 class="mb-3">Transaksi Kamu</h6>
                <div class="row text-center transaction-icons">
                    <div class="col">
                        <div class="p-0">
                            <div class="mx-auto bg-info bg-opacity-10 rounded-circle p-2 mb-2" style="width: 40px; height: 40px">
                                <i class="ki-filled ki-notepad-edit text-info fs-3x"></i>
                            </div>
                            <span class="text-muted small">TTD</span>
                        </div>
                    </div>
                    <div class="col">
                        <div class="p-0">
                            <div class="mx-auto bg-primary bg-opacity-10 rounded-circle p-2 mb-2" style="width: 40px; height: 40px">
                                <i class="ki-filled ki-credit-cart text-primary fs-3x"></i>
                            </div>
                            <span class="text-muted small">Bayar</span>
                        </div>
                    </div>
                    <div class="col">
                        <div class="p-0">
                            <div class="mx-auto bg-warning bg-opacity-10 rounded-circle p-2 mb-2" style="width: 40px; height: 40px">
                                <i class="ki-filled ki-calendar text-warning fs-3x"></i>
                            </div>
                            <span class="text-muted small">Dipesan</span>
                        </div>
                    </div>
                    <div class="col">
                        <div class="p-0">
                            <div class="mx-auto bg-success bg-opacity-10 rounded-circle p-2 mb-2" style="width: 40px; height: 40px">
                                <i class="ki-filled ki-delivery-door text-success fs-3x"></i>
                            </div>
                            <span class="text-muted small">Sedang Berjalan</span>
                        </div>
                    </div>
                    <div class="col">
                        <div class="p-0 position-relative">
                            <div class="mx-auto bg-danger bg-opacity-10 rounded-circle p-2 mb-2" style="width: 40px; height: 40px">
                                <i class="ki-filled ki-star text-danger fs-3x"></i>
                            </div>
                            <span class="text-muted small">Ulasan</span>
                            <span class="position-absolute top-0 start-50 translate-middle badge rounded-pill bg-danger" style="font-size: 10px">
                                1
                            </span>
                        </div>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-3">
                    <img src="{{ asset('media/no-product.png') }}" alt="Product" class="rounded" style="width: 64px; height: 64px; object-fit: cover">
                    <div class="flex-grow-1">
                        <h4 class="mb-0 fw-medium">Motorcycle Gloves Tactic...</h4>
                        <div class="d-flex align-items-center gap-1">
                            <div class="text-warning">
                                <i class="bi bi-star-fill"></i>
                                <i class="bi bi-star-fill"></i>
                                <i class="bi bi-star-fill"></i>
                                <i class="bi bi-star-fill"></i>
                                <i class="bi bi-star-fill"></i>
                            </div>
                            <span class="text-muted small">4.8 (120 reviews)</span>
                        </div>
                    </div>
                    <a href="#" class="text-primary small">View Order</a>
                </div>
            </div>
            <div class="p-3 mb-3 rounded-box shadow-sm d-none" data-aos="fade-up">
                <h6 class="mb-3">Pembelian Kamu</h6>
                <div class="row text-center transaction-icons">
                    <div class="col">
                        <div class="p-0">
                            <div class="mx-auto bg-primary bg-opacity-10 rounded-circle p-2 mb-2" style="width: 40px; height: 40px">
                                <i class="ki-filled ki-credit-cart text-primary fs-3x"></i>
                            </div>
                            <span class="text-muted small">Bayar</span>
                        </div>
                    </div>
                    <div class="col">
                        <div class="p-0">
                            <div class="mx-auto bg-info bg-opacity-10 rounded-circle p-2 mb-2" style="width: 40px; height: 40px">
                                <i class="ki-filled ki-parcel text-info fs-3x"></i>
                            </div>
                            <span class="text-muted small">Diproses</span>
                        </div>
                    </div>
                    <div class="col">
                        <div class="p-0">
                            <div class="mx-auto bg-warning bg-opacity-10 rounded-circle p-2 mb-2" style="width: 40px; height: 40px">
                                <i class="ki-filled ki-delivery text-warning fs-3x"></i>
                            </div>
                            <span class="text-muted small">Dikirim</span>
                        </div>
                    </div>
                    <div class="col">
                        <div class="p-0">
                            <div class="mx-auto bg-success bg-opacity-10 rounded-circle p-2 mb-2" style="width: 40px; height: 40px">
                                <i class="ki-filled ki-delivery-door text-success fs-3x"></i>
                            </div>
                            <span class="text-muted small">Sudah Tiba</span>
                        </div>
                    </div>
                    <div class="col">
                        <div class="p-0 position-relative">
                            <div class="mx-auto bg-danger bg-opacity-10 rounded-circle p-2 mb-2" style="width: 40px; height: 40px">
                                <i class="ki-filled ki-star text-danger fs-3x"></i>
                            </div>
                            <span class="text-muted small">Ulasan</span>
                            <span class="position-absolute top-0 start-50 translate-middle badge rounded-pill bg-danger" style="font-size: 10px">
                                1
                            </span>
                        </div>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-3">
                    <img src="{{ asset('media/no-product.png') }}" alt="Product" class="rounded" style="width: 64px; height: 64px; object-fit: cover">
                    <div class="flex-grow-1">
                        <h4 class="mb-0 fw-medium">Motorcycle Gloves Tactic...</h4>
                        <div class="d-flex align-items-center gap-1">
                            <div class="text-warning">
                                <i class="bi bi-star-fill"></i>
                                <i class="bi bi-star-fill"></i>
                                <i class="bi bi-star-fill"></i>
                                <i class="bi bi-star-fill"></i>
                                <i class="bi bi-star-fill"></i>
                            </div>
                            <span class="text-muted small">4.8 (120 reviews)</span>
                        </div>
                    </div>
                    <a href="#" class="text-primary small">View Order</a>
                </div>
            </div>
            <!-- Saldo dan Points -->
            <div class="p-3 mb-3 rounded-box shadow-sm">
                <h6 class="mb-3">Saldo & Points</h6>
                <div class="row text-center">
                    <div class="col">
                        <div class="text-warning">
                            {{ $this->user->getPoints() }}
                        </div>
                        <small>Point</small>
                    </div>
                    <div class="col">
                        <a href="{{ route('consumer.wallet') }}" wire:navigate>
                            <div class="text-success">
                                Rp. {{ number_format($this->user->balance) }}
                            </div>
                            <small>Saldo Adamasanya</small>
                        </a>
                    </div>
                    <div class="col">
                        <a href="#" class="text-decoration-none text-primary">
                            {{ $this->user->getUserAchievements()[0]->name }}
                        </a>
                        <br>
                        <small>Membership</small>
                    </div>
                </div>
            </div>

            <!-- Menu Lain -->
            <div class="p-3 rounded-box shadow-sm d-none">
                <div class="row text-center">
                    <div class="col">
                        <i class="ki-filled ki-shop fs-3x"></i><br><small>Buka Toko</small>
                    </div>
                    <div class="col">
                        <i class="ki-filled ki-discount fs-3x"></i><br><small>Kupon Saya</small>
                    </div>
                    <div class="col">
                        <i class="ki-filled ki-heart fs-3x"></i><br><small>Wishlist</small>
                    </div>
                </div>
            </div>
            <!-- Recommendations -->
            <div class="mt-5 mb-4 mb-md-5 d-none">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h3 class="h5 mb-0 fw-bold">Rekomendasi untuk kamu</h3>
                    {{-- <a href="#" class="text-primary small">Lihat semua</a> --}}
                </div>
                <div class="row g-3">
                    @foreach($this->recommendForYou as $product)
                    <div class="col-6 col-md-3">
                        <div class="card border-0 shadow-sm h-100 hover-shadow transition">
                            <img src="{{ $product->product->image ?? asset('media/no-product.png') }}" alt="{{ $product->name }}" 
                                 class="card-img-top" style="height: 160px; object-fit: cover">
                            <div class="card-body">
                                <h5 class="card-title fs-6 mb-1 text-truncate">{{ $product->name }}</h5>
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="fw-bold text-primary">Rp{{ number_format($product->price, 0, ',', '.') }}</span>
                                    <div class="d-flex align-items-center text-warning">
                                        <i class="bi bi-star-fill small"></i>
                                        <span class="text-muted small ms-1">4.8</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    @endvolt
</x-app>