<?php

use Carbon\Carbon;
use App\Models\User;
use App\Models\Promo;
use Illuminate\Support\Str;
use App\Models\Master\Branch;
use App\Models\ProductBranch;
use function Laravel\Folio\name;
use function Livewire\Volt\{computed, state};

name('home');

state([
    'branch' => '',
    'search' => '',
    'selectedColor' => null,
    'selectedStorage' => null,
    'selectedProduct' => null,
    'showViewAll' => true,
]);

$categories = computed(function() {
    return \App\Models\Master\Category::where('st', 'a')
        ->withCount('products')
        ->orderBy('name')
        ->get();
});

$branches = computed(function() {
    return Branch::where('st','a')
        ->orderBy('name')
        ->pluck('name', 'id')
        ->prepend('Pilih Cabang', '');
});

$productBranch = computed(function() {
    if (!$this->branch) return collect();
    
    return ProductBranch::with([
            'product.category', 
            'color',
            'storage',
            'branch'
        ])
        ->where('branch_id', $this->branch)
        ->where('is_publish', 1)
        ->when($this->search, function($query) {
            $query->whereHas('product', function($q) {
                $q->where('name', 'like', '%'.$this->search.'%')
                  ->orWhere('description', 'like', '%'.$this->search.'%');
            });
        })
        ->get()
        ->groupBy('product_id');
});

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

$set = function($type, $value) {
    $this->$type = $value;
    $this->dispatch('variant-selected');
};
$dpos = computed(function(){
    return User::onlyBanned()->get();
});
?>
<x-app>
    <livewire:hero.promo/>
    @volt
    <div id="kt_app_content" class="app-content flex-column-fluid py-10">
        <!-- Verification Section -->
        @if(!Auth::check() || Auth::user()->st !== 'verified')
        <div class="mb-12">
            <div class="card border-0 shadow-sm rounded-4 bg-light-primary overflow-hidden">
                <div class="card-body p-6 p-md-8">
                    <div class="d-flex flex-column flex-md-row align-items-center justify-content-between gap-4">
                        <div class="d-flex align-items-center gap-4">
                            <i class="ki-solid ki-shield-tick fs-2hx text-primary"></i>
                            <div>
                                <h3 class="fw-bold text-gray-900 fs-3 mb-2">
                                    @if(Auth::check()) Verifikasi Akun Anda @else Daftar dan Verifikasi Sekarang @endif
                                </h3>
                                <p class="text-gray-600 fs-5">
                                    @if(Auth::check())
                                        Lengkapi data diri untuk akses penuh ke semua fitur dan promo eksklusif!
                                    @else
                                        Daftar dan verifikasi akun Anda untuk menikmati sewa dan beli elektronik dengan mudah.
                                    @endif
                                </p>
                            </div>
                        </div>
                        <a href="{{ route('profile.verification') }}" wire:navigate
                        class="btn btn-primary rounded-pill px-6 py-3 fw-bold d-flex align-items-center gap-2"
                        aria-label="Verifikasi akun">
                            <i class="ki-outline ki-arrow-right fs-2"></i>
                            @if(Auth::check()) Verifikasi Sekarang @else Mulai Sekarang @endif
                        </a>
                    </div>
                </div>
            </div>
        </div>
        @endif
        <!-- Categories Section -->
        <div class="mb-12" id="categories">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-6">
                <div class="position-relative pb-2 mb-4 mb-md-0">
                    <h2 class="fw-bold text-gray-900 mb-0 fs-2">Kategori Produk</h2>
                    <div class="position-absolute bottom-0 start-0 w-50 h-2px bg-primary rounded"></div>
                </div>
                @if($showViewAll)
                <a href="{{ route('category') }}" wire:navigate class="btn btn-link text-primary fw-bold d-flex align-items-center">
                    Lihat Semua
                    <i class="ki-outline ki-arrow-right fs-2 ms-2"></i>
                </a>
                @endif
            </div>
            <div class="row g-3 g-md-4">
                @foreach($this->categories as $category)
                <div class="col-4 col-sm-3 col-md-2 col-lg-2 col-xl-1-5">
                    <a href="{{ route('category.show', ['slug' => $category->slug]) }}" wire:navigate 
                       class="card card-category h-100 border-0 shadow-sm rounded-3 overflow-hidden text-decoration-none text-dark">
                        <div class="card-body p-3 d-flex flex-column align-items-center justify-content-center">
                            <div class="position-relative mb-3">
                                <div class="bg-light rounded-circle p-3 d-flex align-items-center justify-content-center" 
                                    style="width: 80px; height: 80px;">
                                    <img src="{{ $category->image ?? asset('media/placeholder/category.png') }}" 
                                        class="img-fluid object-fit-contain" 
                                        alt="{{ $category->name }}"
                                        style="max-width: 50px; max-height: 50px;"
                                        loading="lazy">
                                </div>
                            </div>
                            <div class="text-center">
                                <h5 class="text-gray-800 fw-bold mb-0 fs-6 truncate-2">{{ $category->name }}</h5>
                                <span class="text-muted fs-7 mt-1 d-block">{{ $category->products_count }} produk</span>
                            </div>
                        </div>
                    </a>
                </div>
                @endforeach
            </div>
        </div>
        
        <!-- Products Section -->
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-4 mb-6">
            <h2 class="fw-bold fs-2 text-gray-900 mb-0">Produk Tersedia</h2>
            <div class="d-flex flex-column flex-sm-row gap-3 w-100 w-md-auto">
                <div class="position-relative w-100">
                    <i class="ki-outline ki-magnifier position-absolute top-50 translate-middle-y ms-4 fs-2"></i>
                    <input type="text" wire:model.live.debounce.300ms="search" 
                        class="form-control form-control-lg ps-12 rounded-pill shadow-sm" 
                        placeholder="Cari produk..."
                        aria-label="Cari produk">
                    @if($search)
                    <button wire:click="$set('search', '')" class="btn btn-sm position-absolute top-50 end-0 translate-middle-y me-4">
                        <i class="ki-outline ki-cross fs-2"></i>
                    </button>
                    @endif
                </div>
                <x-form-select name="branch" modifier="live" 
                    class="form-select form-select-lg rounded-pill shadow-sm" 
                    :options="$this->branches"
                    aria-label="Pilih cabang"/>
            </div>
        </div>
        
        @if($this->branch)
            @php
            $cabang = Branch::find($this->branch);
            @endphp
            <div class="mb-8">
                <div class="alert alert-primary d-flex align-items-center p-4 rounded-4 shadow-sm">
                    <i class="ki-outline ki-shop fs-1 me-4"></i>
                    <div class="d-flex flex-column">
                        <h4 class="mb-1">Cabang {{ $cabang->name }}</h4>
                        <span>Alamat: {{ $cabang->address }}</span>
                        <small class="text-muted">Jam Operasional: {{ $cabang->operational_hours ?? '09:00 - 21:00' }}</small>
                    </div>
                </div>
            </div>
            
            @if($this->productBranch->count() > 0)
            <div class="row g-4" id="products">
                @foreach($this->productBranch as $productId => $variations)
                @php
                    $firstVariant = $variations->first();
                    $product = $firstVariant->product;
                    $totalStock = $variations->sum('stock');
                    
                    // Get available variants based on selections
                    $availableVariants = $variations;
                    if ($this->selectedColor) {
                        $availableVariants = $availableVariants->where('color_id', $this->selectedColor);
                    }
                    if ($this->selectedStorage) {
                        $availableVariants = $availableVariants->where('storage_id', $this->selectedStorage);
                    }
                    
                    $selectedVariant = $availableVariants->first() ?? $firstVariant;
                    
                    // Get unique colors and storages
                    $uniqueColors = $variations->unique('color_id')->filter(fn($v) => $v->color);
                    $uniqueStorages = $variations->unique('storage_id')->filter(fn($v) => $v->storage);
                    
                    // Determine product image based on selected color
                    $colorImage = null;
                    if ($selectedVariant->color) {
                        $colorImage = asset('storage/product/' . $product->slug . '-' . Str::slug($selectedVariant->color->value) . '.png');
                    }
                    $cabang = Branch::where('id', $this->branch)->first();
                    
                    // Determine route based on auth status
                    $routeProduct = route('product.show', [
                        'slug' => $product->slug,
                        'branch' => $cabang->slug
                    ]);
                    // if(Auth::check()) {
                    //     if(Auth::user()->getRoleNames()[0] == "Onboarding") {
                    //         $routeProduct = route('onboarding');
                    //     } else {
                            // if(Auth::user()->st == "verified" && Auth::user()->isNotBanned() && !Auth::user()->deleted_at) {
                            //     $routeProduct = route('product.show', [
                            //         'slug' => $product->slug,
                            //         'branch' => $cabang->slug
                            //     ]);
                            // } else {
                            //     $routeProduct = route('home');
                            // }
                    //     }
                    // } else {
                    //     $routeProduct = route('login');
                    // }
                    
                    // Promo calculations
                    $promo = $this->promo;
                    $showPromo = $promo && (
                        $promo->scope === 'all' || 
                        ($promo->scope === 'products' && $promo->products->contains($product->id))
                    ) && (
                        $selectedVariant->sale_price >= ($promo->min_order_amount ?? 0) || 
                        $selectedVariant->rent_price >= ($promo->min_order_amount ?? 0)
                    ) && (
                        $promo->max_uses === null || 
                        $promo->max_uses > $promo->usages->count()
                    ) && in_array($promo->type, ['percentage', 'fixed_amount']);
                    
                    // Calculate discounted prices
                    $discountedSale = $selectedVariant->sale_price;
                    $discountedRent = $selectedVariant->rent_price;
                    
                    if($showPromo) {
                        if($promo->type === 'percentage') {
                            $discountedSale = $selectedVariant->sale_price * (1 - ($promo->value / 100));
                            $discountedRent = $selectedVariant->rent_price * (1 - ($promo->value / 100));
                        } 
                        elseif($promo->type === 'fixed_amount') {
                            $discountedSale = max(0, $selectedVariant->sale_price - $promo->value);
                            $discountedRent = max(0, $selectedVariant->rent_price - $promo->value);
                        }
                    }
                @endphp
                
                <div class="col-6 col-md-4 col-lg-3" wire:key="product-{{ $productId }}-{{ $this->selectedColor }}-{{ $this->selectedStorage }}">
                    <div class="card card-product h-100 border-0 shadow-sm rounded-4 overflow-hidden">
                        <!-- Badge Stok -->
                        @if($totalStock > 0)
                        <div class="position-absolute top-0 start-0 m-3">
                            <span class="badge bg-success bg-opacity-90 text-white py-2 px-3 fw-bold rounded-pill">
                                Stok: {{ $totalStock }}
                            </span>
                        </div>
                        @endif
                        
                        <!-- Promo Badge -->
                        @if($showPromo)
                        <div class="position-absolute top-0 end-0 m-3">
                            <span class="badge bg-danger bg-opacity-90 text-white py-2 px-3 fw-bold rounded-pill">
                                <i class="ki-solid ki-megaphone fs-4 me-1"></i>
                                Promo
                            </span>
                        </div>
                        @endif
                        
                        <!-- Product Image -->
                        <div class="product-image-container position-relative overflow-hidden d-flex align-items-center justify-content-center">
                            <img src="{{ $colorImage ?? $product->image }}" 
                                class="img-fluid object-fit-contain p-4" 
                                alt="{{ $product->name }}"
                                style="max-height: 180px;"
                                loading="lazy"
                                wire:loading.class.delay="opacity-50">
                                
                            <!-- Loading indicator -->
                            <div wire:loading.delay class="position-absolute top-50 start-50 translate-middle">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="card-body p-4" wire:ignore.self>
                            <!-- Category -->
                            <div class="mb-2">
                                <span class="badge bg-primary bg-opacity-10 text-primary fs-7 fw-semibold rounded-pill py-2">
                                    {{ $product->category->name ?? 'No Category' }}
                                </span>
                            </div>
                            
                            <!-- Product Name -->
                            <h3 class="mb-0 fs-5 fw-bold text-gray-900 truncate-2" style="min-height: 48px;">
                                {{ $product->name }}
                            </h3>
                            
                            <!-- Price Display -->
                            <div class="price-display mb-3">
                                @if($selectedVariant->sale_price > 0)
                                    <!-- Rental Price Section -->
                                    <div class="rental-price mb-3">
                                        <div class="d-flex align-items-baseline">
                                            <span class="fs-3 fw-bold text-primary me-2">
                                                Rp{{ number_format($showPromo ? $discountedRent : $selectedVariant->rent_price, 0, ',', '.') }}
                                            </span>
                                            <span class="text-muted fs-6">/hari</span>
                                            
                                            @if($showPromo)
                                                <span class="text-muted fs-7 ms-2 text-decoration-line-through">
                                                    Rp{{ number_format($selectedVariant->rent_price, 0, ',', '.') }}
                                                </span>
                                            @endif
                                        </div>
                                        {{-- <div class="text-muted fs-7 mt-1">Sewa per hari</div> --}}
                                    </div>
                                    
                                    <!-- Divider with improved spacing -->
                                    <div class="separator separator-content my-5">atau</div>
                                    
                                    <!-- Purchase Option Section -->
                                    <div class="purchase-option bg-light-success p-3 rounded-3">
                                        <div class="d-flex flex-wrap align-items-center">
                                            <span class="fs-6 me-2">Dapat dimiliki dengan:</span>
                                            <span class="fs-3 fw-bold text-success me-2">
                                                Rp{{ number_format($showPromo ? $discountedSale : $selectedVariant->sale_price, 0, ',', '.') }}
                                            </span>
                                            
                                            @if($showPromo)
                                                <span class="text-muted fs-7 text-decoration-line-through">
                                                    Rp{{ number_format($selectedVariant->sale_price, 0, ',', '.') }}
                                                </span>
                                            @endif
                                        </div>
                                        {{-- <div class="mt-1">
                                            <span class="badge bg-success bg-opacity-10 text-success fs-7">
                                                <i class="bi bi-check-circle-fill me-1"></i>Hemat hingga {{ number_format(100 - ($selectedVariant->sale_price/$selectedVariant->rent_price)*100, 0) }}%
                                            </span>
                                        </div> --}}
                                    </div>
                                @else
                                    <!-- Rental Only Section -->
                                    <div class="rental-price">
                                        <div class="d-flex align-items-baseline">
                                            <span class="fs-3 fw-bold text-primary me-2">
                                                Rp{{ number_format($showPromo ? $discountedRent : $selectedVariant->rent_price, 0, ',', '.') }}
                                            </span>
                                            <span class="text-muted fs-6">/hari</span>
                                            
                                            @if($showPromo)
                                                <span class="text-muted fs-7 ms-2 text-decoration-line-through">
                                                    Rp{{ number_format($selectedVariant->rent_price, 0, ',', '.') }}
                                                </span>
                                            @endif
                                        </div>
                                        {{-- <div class="text-muted fs-7 mt-1">Sewa per hari</div> --}}
                                    </div>
                                @endif
                            </div>
                            
                            <!-- Rating & Location -->
                            <div class="d-flex align-items-center text-muted fs-7 mb-4">
                                <span class="me-3">
                                    <i class="ki-solid ki-star text-warning me-1"></i>
                                    {{ number_format($product->averageRating()) }}
                                    @if($product->ratings->count() > 0)
                                        <span class="text-muted">({{ $product->ratings->count() }})</span>
                                    @endif
                                </span>
                                @if(!$this->branch)
                                <span>
                                    <i class="bi bi-geo-alt-fill text-danger me-1"></i> 
                                    {{ $selectedVariant->branch->city ?? 'Unknown' }}
                                </span>
                                @endif
                            </div>
                            
                            <!-- Color Options -->
                            @if($uniqueColors->count() > 1)
                            <div class="mb-3">
                                <label class="form-label fw-semibold d-block mb-2">Warna</label>
                                <div class="d-flex flex-wrap gap-2">
                                    @foreach($uniqueColors as $variant)
                                        @if($variant->color)
                                        @php
                                        $isActive = $this->selectedColor == $variant->color_id;
                                        $isDisabled = $this->selectedStorage && !$variations->contains(fn($v) => 
                                            $v->color_id == $variant->color_id && 
                                            $v->storage_id == $this->selectedStorage
                                        );
                                        @endphp
                                        <div 
                                            data-bs-toggle="tooltip" data-bs-placement="top" title="{{ $variant->color->value }}"
                                            class="color-option {{ $isActive ? 'active' : '' }} {{ $isDisabled ? 'disabled' : '' }}" 
                                            style="background-color: {{ $variant->warna($variant->color->value) }};"
                                            wire:click="set('selectedColor', {{ $variant->color_id }})"
                                            aria-label="Pilih warna {{ $variant->color->value }}"
                                            @if($isDisabled) aria-disabled="true" @endif
                                        ></div>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                            @endif
                            
                            <!-- Storage Options -->
                            @if($uniqueStorages->count() > 1)
                            <div class="mb-4">
                                <label class="form-label fw-semibold d-block mb-2">Storage</label>
                                <div class="d-flex flex-wrap gap-2">
                                    @foreach($uniqueStorages as $variant)
                                        @php
                                            $isActive = $this->selectedStorage == $variant->storage_id;
                                            $isDisabled = $this->selectedColor && !$variations->contains(fn($v) => 
                                                $v->storage_id == $variant->storage_id && 
                                                $v->color_id == $this->selectedColor
                                            );
                                        @endphp
                                        <span 
                                            class="badge bg-light text-dark py-2 px-3 storage-option {{ $isActive ? 'active' : '' }} {{ $isDisabled ? 'disabled' : '' }}"
                                            wire:click="set('selectedStorage', {{ $variant->storage_id }})"
                                            aria-label="Pilih storage {{ $variant->storage->value }}"
                                            @if($isDisabled) aria-disabled="true" @endif
                                        >
                                            {{ $variant->storage->value }}
                                        </span>
                                    @endforeach
                                </div>
                            </div>
                            @endif
                            
                            <!-- Action Buttons -->
                            <div class="d-grid gap-2">
                                <div class="d-flex gap-2"> <!-- atau gunakan 'btn-group' jika ingin tampilan grup tombol -->
                                    <a href="{{ $routeProduct }}" wire:navigate
                                        class="btn btn-icon btn-light-primary rounded-pill fw-bold py-2 shadow-sm d-flex align-items-center justify-content-center" data-bs-toggle="tooltip" data-bs-placement="top" title="Sewa Sekarang">
                                        <i class="ki-filled ki-calendar fs-3"></i> 
                                    </a>
                                    @if($selectedVariant->sale_price > 0)
                                        <button class="btn btn-icon btn-light-warning rounded-pill fw-bold py-2 shadow-sm d-flex align-items-center justify-content-center" wire:click="addToCart('buy', {{ $selectedVariant->id }})" data-bs-toggle="tooltip" data-bs-placement="top" title="Beli Sekarang">
                                            <i class="ki-filled ki-purchase fs-3"></i> 
                                        </button>
                                        <button class="btn btn-icon btn-light-success rounded-pill fw-bold py-2 d-flex align-items-center justify-content-center" wire:click="addToCart({{ $selectedVariant->id }})" data-bs-toggle="tooltip" data-bs-placement="top" title="Tambah ke Keranjang">
                                            <i class="ki-filled ki-handcart fs-3"></i> 
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <div class="text-center py-15">
                <img src="{{ asset('media/illustrations/there-is-nothing-here.png') }}" 
                     class="w-200px mb-5 theme-light-show" 
                     alt="No Products"
                     loading="lazy">
                <img src="{{ asset('media/illustrations/there-is-nothing-here-dark.png') }}" 
                     class="w-200px mb-5 theme-dark-show" 
                     alt="No Products"
                     loading="lazy">
                <h3 class="text-gray-600">Produk tidak ditemukan</h3>
                <p class="text-muted">Silakan cari dengan kata kunci lain atau pilih cabang berbeda</p>
                <button class="btn btn-primary rounded-pill px-6" wire:click="$set('search', '')">
                    Reset Pencarian
                </button>
            </div>
            @endif
        @else
            <div class="text-center py-15">
                <img src="{{ asset('media/illustrations/shop.png') }}" 
                     class="w-200px mb-5 theme-light-show" 
                     alt="Select Branch"
                     loading="lazy">
                <img src="{{ asset('media/illustrations/shop-dark.png') }}" 
                     class="w-200px mb-5 theme-dark-show" 
                     alt="Select Branch"
                     loading="lazy">
                <h3 class="text-gray-600">Silakan pilih cabang terlebih dahulu</h3>
                <p class="text-muted">Pilih cabang untuk melihat produk yang tersedia</p>
            </div>
        @endif
        <div class="mb-15">
            <div class="text-center mb-10">
                <h2 class="fw-bold fs-2hx text-gray-900 mb-5">Daftar Pencarian Orang (DPO)</h2>
                <div class="fs-5 text-muted fw-semibold">
                    <p class="mb-0">Orang-orang yang membawa kabur barang sewa dan sedang dalam pencarian</p>
                </div>
            </div>

            @if($this->dpos->count() > 0)
            <div class="tns tns-default" wire:ignore style="direction: ltr">
                <div data-tns="true" data-tns-loop="true" data-tns-swipe-angle="false" data-tns-speed="2000" data-tns-autoplay="true" data-tns-autoplay-timeout="18000" data-tns-controls="true" data-tns-nav="false" data-tns-items="1" data-tns-center="false" data-tns-dots="false" data-tns-prev-button="#dpo_prev" data-tns-next-button="#dpo_next" data-tns-responsive="{1200: {items: 3}, 992: {items: 2}}">
                    @foreach ($this->dpos as $dpo)
                    <div class="text-center px-5">
                        <div class="card card-flush h-100">
                            <div class="card-body">
                                <div class="octagon mx-auto mb-5 d-flex w-200px h-200px bgi-no-repeat bgi-size-contain bgi-position-center" style="background-image:url('{{ $dpo->profile->image }}')"></div>
                                <div class="mb-5">
                                    <h3 class="text-gray-900 fw-bold text-hover-primary fs-3 mb-2">{{ $dpo->name }}</h3>
                                    <div class="text-danger fw-semibold fs-5">Status: DPO</div>
                                </div>
                                <button class="btn btn-sm btn-light-danger fw-bold" data-bs-toggle="modal" data-bs-target="#reportModal" data-dpo-id="{{ $dpo->id }}">
                                    <i class="ki-outline ki-information fs-2 me-2"></i> Laporkan Penemuan
                                </button>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                <button class="btn btn-icon btn-active-color-primary position-absolute top-50 start-0 translate-middle-y" id="dpo_prev">
                    <i class="ki-outline ki-left fs-2x"></i>
                </button>
                <button class="btn btn-icon btn-active-color-primary position-absolute top-50 end-0 translate-middle-y" id="dpo_next">
                    <i class="ki-outline ki-right fs-2x"></i>
                </button>
            </div>
            @else
            <div class="text-center py-10">
                <img src="{{ asset('media/illustrations/sigma-1/13.png') }}" class="w-200px mb-5 theme-light-show" alt="No DPO">
                <img src="{{ asset('media/illustrations/sigma-1/13-dark.png') }}" class="w-200px mb-5 theme-dark-show" alt="No DPO">
                <h3 class="text-gray-600">Tidak ada DPO saat ini</h3>
                <p class="text-muted">Semua pelanggan telah mengembalikan barang sewa dengan baik</p>
            </div>
            @endif
        </div>
    </div>
    @endvolt
    @section('custom_js')
    <script data-navigate-once>
        document.addEventListener('livewire:initialized', () => {
            // Smooth scroll to top when branch changes
            Livewire.on('branch-changed', () => {
                const categoriesElement = document.getElementById('categories');
                if (categoriesElement) {
                    window.scrollTo({
                        top: categoriesElement.offsetTop - 20,
                        behavior: 'smooth'
                    });
                }
            });
            
            // Handle variant selection animation
            Livewire.on('variant-selected', () => {
                const card = document.querySelector('.card-product:hover');
                if (card) {
                    card.classList.add('variant-selected');
                    setTimeout(() => {
                        card.classList.remove('variant-selected');
                    }, 300);
                }
            });
        });
        document.addEventListener('livewire:navigated', () => {
            // Reinitialize your scripts here if needed
            // Smooth scroll to top when branch changes
            Livewire.on('branch-changed', () => {
                const categoriesElement = document.getElementById('categories');
                if (categoriesElement) {
                    window.scrollTo({
                        top: categoriesElement.offsetTop - 20,
                        behavior: 'smooth'
                    });
                }
            });
            
            // Handle variant selection animation
            Livewire.on('variant-selected', () => {
                const card = document.querySelector('.card-product:hover');
                if (card) {
                    card.classList.add('variant-selected');
                    setTimeout(() => {
                        card.classList.remove('variant-selected');
                    }, 300);
                }
            });
        });
    </script>
    @endsection
</x-app>