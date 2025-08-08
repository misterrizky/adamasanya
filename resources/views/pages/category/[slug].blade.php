<?php

use Carbon\Carbon;
use App\Models\Promo;
use App\Models\Master\Branch;
use App\Models\ProductBranch;
use App\Models\Master\Category;
use function Laravel\Folio\name;
use function Livewire\Volt\{computed, state};

name('category.show');
state(['category' => fn () => Category::where('slug', request()->slug)->first()]);
state([
    'branch' => '',
    'search' => '',
    'selectedColor' => null,
    'selectedStorage' => null,
    'selectedProduct' => null,
    'showViewAll' => true,
]);
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
    ->when($this->category, function($query) {
        $query->whereHas('product', function($q) {
            $q->where('category_id', $this->category->id);
        });
    })
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
?>
<x-app>
    <livewire:hero.category :category="$slug"/>
    @volt
    <div id="kt_app_content" class="app-content flex-column-fluid py-10">
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
                    
                    // Determine route based on auth status
                    if(Auth::check()) {
                        if(Auth::user()->getRoleNames()[0] == "Onboarding") {
                            $routeProduct = route('onboarding');
                        } else {
                            if(Auth::user()->st == "verified" && Auth::user()->isNotBanned() && !Auth::user()->deleted_at) {
                                $routeProduct = route('product.show', [
                                    'slug' => $product->slug,
                                    'branch' => $cabang->slug
                                ]);
                            } else {
                                $routeProduct = route('home');
                            }
                        }
                    } else {
                        $routeProduct = route('login');
                    }
                    
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
                                        $colorValue = match($variant->color->value) {
                                            // iPhone 7 Series
                                            "Black" => '#000000',
                                            "Silver" => '#C0C0C0',
                                            "Gold" => '#FFD700',
                                            "Rose Gold" => '#B76E79',
                                            "Jet Black" => '#0A0A0A',
                                            "Red" => '#FF0000',
                                            
                                            // iPhone 8/X Series
                                            "Space Gray" => '#535150',
                                            
                                            // iPhone XR
                                            "White" => '#FFFFFF',
                                            "Blue" => '#007AFF',
                                            "Yellow" => '#FFD60A',
                                            "Coral" => '#FF7E79',
                                            
                                            // iPhone 11 Series
                                            "Green" => '#A7D3A6',
                                            "Purple" => '#D1B3FF',
                                            "Midnight Green" => '#475C4D',
                                            
                                            // iPhone 12 Series
                                            "Pacific Blue" => '#2D5F7A',
                                            "Graphite" => '#4F4F4F',
                                            
                                            // iPhone 13 Series
                                            "Midnight" => '#000000',
                                            "Starlight" => '#F8F9F0',
                                            "Pink" => '#FFB6C1',
                                            "Sierra Blue" => '#9BB5CE',
                                            
                                            // iPhone 14 Series
                                            "Deep Purple" => '#4A3C5F',
                                            "Space Black" => '#333333',
                                            
                                            // iPhone 15 Series
                                            "Black Titanium" => '#2D2D2D',
                                            "White Titanium" => '#E5E4E2',
                                            "Blue Titanium" => '#5E7E9B',
                                            "Natural Titanium" => '#8B8B8B',
                                            
                                            // iPhone 16 Series (Prediksi)
                                            "Deep Blue" => '#003366',
                                            // Samsung A50s
                                            "Prism Crush Black" => '#000000',
                                            "Prism Crush White" => '#FFFFFF',
                                            "Prism Crush Green" => '#C5E3B1',
                                            "Prism Crush Violet" => '#C6B5D6',
                                            
                                            // Samsung S22 Ultra
                                            "Phantom Black" => '#2D2926',
                                            "Phantom White" => '#EAE7DE',
                                            "Green" => '#A7D3A6',
                                            "Burgundy" => '#800020',
                                            "Graphite" => '#4F4F4F',
                                            "Sky Blue" => '#87CEEB',
                                            "Red" => '#FF0000',
                                            
                                            // Samsung S23 Ultra
                                            "Cream" => '#F5F5DC',
                                            "Lavender" => '#E6E6FA',
                                            "Lime" => '#BFFF00',
                                            
                                            // Samsung S24 Ultra
                                            "Titanium Black" => '#2B2B2B',
                                            "Titanium Gray" => '#8E8E8E',
                                            "Titanium Violet" => '#B399D4',
                                            "Titanium Yellow" => '#FFD700',
                                            "Titanium Blue" => '#4682B4',
                                            "Titanium Green" => '#2E8B57',
                                            "Titanium Orange" => '#FF8C00',
                                            
                                            // Samsung S25 Ultra (Prediksi)
                                            "Onyx Black" => '#0F0F0F',
                                            "Marble White" => '#F2F0EB',
                                            "Cobalt Violet" => '#8A2BE2',
                                            "Amber Yellow" => '#FFBF00',
                                            "Sapphire Blue" => '#0F52BA',
                                            "Emerald Green" => '#50C878',
                                            
                                            // Google Pixel
                                            "Obsidian" => '#0B1215',
                                            "Hazel" => '#8E7618',
                                            "Rose" => '#FF007F',
                                            "Snow" => '#FFFAFA',
                                            default => $variant->color->value
                                        };
                                        $isActive = $this->selectedColor == $variant->color_id;
                                        $isDisabled = $this->selectedStorage && !$variations->contains(fn($v) => 
                                            $v->color_id == $variant->color_id && 
                                            $v->storage_id == $this->selectedStorage
                                        );
                                        @endphp
                                        <div 
                                            data-bs-toggle="tooltip" data-bs-placement="top" title="{{ $variant->color->value }}"
                                            class="color-option {{ $isActive ? 'active' : '' }} {{ $isDisabled ? 'disabled' : '' }}" 
                                            style="background-color: {{ $colorValue }};"
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
    </div>
    @endvolt
    @section('custom_js')
    <script data-navigate-once>
        document.addEventListener('livewire:initialized', () => {
            // Smooth scroll to top when branch changes
            Livewire.on('branch-changed', () => {
                window.scrollTo({
                    top: document.getElementById('categories').offsetTop - 20,
                    behavior: 'smooth'
                });
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