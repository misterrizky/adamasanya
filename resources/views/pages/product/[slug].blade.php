<?php

use Carbon\Carbon;
use App\Models\Promo;
use App\Models\Product;
use App\Models\Master\Branch;
use App\Models\ProductBranch;
use App\Models\Transaction\Rent;
use function Laravel\Folio\name;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Transaction\RentItem;
use App\Models\Master\BranchSchedule;
use function Livewire\Volt\{computed, mount, state, rules};

name('product.show');
state([
    'product' => null,
    'promo' => null,
    'variants' => null,
    'selectedBranch' => null,
    'selectedColor' => null,
    'selectedStorage' => null,
    'selectedVariant' => null,
    'quantity' => 1,
    'tanggal_ambil' => null,
    'jam_ambil' => null,
    'jumlah_hari' => 1,
    'branch_hours_error' => null,
    'time_past_error' => null,
    'total_price_item' => 0,
    'subtotal' => 0,
    'biaya_layanan' => 4000,
    'diskon' => 0,
    'deposit' => 0,
    'grandtotal' => 0,
]);

mount(function () {
    $this->product = Product::where('slug', request()->slug)->firstOrFail();
    
    // Get branch by slug
    $this->selectedBranch = Branch::where('slug', request()->branch)->first();
    
    if(Auth::user()->userAddress){
        if(Auth::user()->userAddress->city->name != $this->selectedBranch->city->name){
            $this->deposit = 500000;
        }else{
            $this->deposit = 0;
        }
    }
    // Load product variants for this branch
    $this->variants = ProductBranch::with(['color', 'storage', 'branch'])
        ->where('product_id', $this->product->id)
        ->where('branch_id', $this->selectedBranch->id)
        ->where('is_publish', 1)
        ->get();

    // If there are variants, set the first as selected
    if ($this->variants->count() > 0) {
        $this->selectedVariant = $this->variants->first();
        $this->selectedColor = $this->selectedVariant->color_id;
        $this->selectedStorage = $this->selectedVariant->storage_id;
    }

    // Set default pickup date and time
    $this->tanggal_ambil = now()->format('Y-m-d');
    $this->jam_ambil = now()->format('H:i');
    // $this->jumlah_hari = 1;

    // Check for active promo
    $this->promo = $this->getActivePromo();
});

// Get active promo
$getActivePromo = function () {
    $now = now();
    $dayOfWeek = $now->dayOfWeek;
    $isWeekend = in_array($dayOfWeek, [Carbon::FRIDAY, Carbon::SATURDAY, Carbon::SUNDAY]);

    return Promo::where('is_active', true)
        ->where('start_date', '<=', $now)
        ->where('end_date', '>=', $now)
        ->where(function ($query) use ($isWeekend) {
            $query->where('day_restriction', 'all')
                ->orWhere('day_restriction', $isWeekend ? 'weekend' : 'weekday');
        })
        ->first();
};

// Computed properties for unique colors and storages
$colors = computed(function () {
    return $this->variants->unique('color_id')->filter(fn ($v) => $v->color);
});

$storages = computed(function () {
    return $this->variants->unique('storage_id')->filter(fn ($v) => $v->storage);
});

// When selectedColor or selectedStorage changes, update selectedVariant
$updatedSelectedColor = function () {
    $this->updateSelectedVariant();
};

$updatedSelectedStorage = function () {
    $this->updateSelectedVariant();
};

$updateSelectedVariant = function () {
    $variant = $this->variants
        ->when($this->selectedColor, function ($query) {
            return $query->where('color_id', $this->selectedColor);
        })
        ->when($this->selectedStorage, function ($query) {
            return $query->where('storage_id', $this->selectedStorage);
        })
        ->first();

    $this->selectedVariant = $variant;
};

// Add to cart methods
$addToCart = function ($type, $variantId = null) {
    $variant = $variantId ? ProductBranch::find($variantId) : $this->selectedVariant;

    if (!$variant) {
        $this->dispatch('toast-error', message: 'Varian tidak valid');
        return;
    }

    if ($variant->stock < 1) {
        $this->dispatch('toast-error', message: 'Stok tidak mencukupi');
        return;
    }

    // Validate pickup date and time for rental
    if ($type === 'rent' && (!$this->pickupDate || !$this->pickupTime)) {
        $this->dispatch('toast-error', message: 'Harap tentukan tanggal dan jam pengambilan');
        return;
    }

    // Determine route based on auth status
    if (auth()->check()) {
        // Add to cart logic
        try {
            \Cart::add([
                'id' => $variant->id,
                'name' => $this->product->name,
                'price' => $type === 'rent' ? $variant->rent_price : $variant->sale_price,
                'quantity' => $this->quantity,
                'attributes' => [
                    'type' => $type,
                    'product_id' => $this->product->id,
                    'color' => $variant->color->value ?? null,
                    'storage' => $variant->storage->value ?? null,
                    'branch_id' => $variant->branch_id,
                    'branch_name' => $variant->branch->name,
                    'image' => $this->getProductImage($variant),
                    'pickup_date' => $type === 'rent' ? $this->pickupDate : null,
                    'pickup_time' => $type === 'rent' ? $this->pickupTime : null,
                ],
            ]);

            $this->dispatch('cart-updated');
            $this->dispatch('toast-success', message: 'Produk ditambahkan ke keranjang');
        } catch (\Exception $e) {
            $this->dispatch('toast-error', message: 'Gagal menambahkan ke keranjang: ' . $e->getMessage());
        }
    } else {
        return redirect()->route('login');
    }
};

$getProductImage = function ($variant) {
    if ($variant->color) {
        $colorImage = asset('storage/product/' . $this->product->slug . '-' . Str::slug($variant->color->value) . '.png');
        if ($this->checkImageExists($colorImage)) {
            return $colorImage;
        }
    }
    return $this->product->image;
};

$checkImageExists = function ($url) {
    $headers = @get_headers($url);
    return $headers && strpos($headers[0], '200');
};

// Check promo applicability
$showPromo = computed(function () {
    if (!$this->promo || !$this->selectedVariant) {
        return false;
    }

    return $this->promo && (
        $this->promo->scope === 'all' || 
        ($this->promo->scope === 'products' && $this->promo->products->contains($this->product->id))
    ) && (
        $this->selectedVariant->sale_price >= ($this->promo->min_order_amount ?? 0) || 
        $this->selectedVariant->rent_price >= ($this->promo->min_order_amount ?? 0)
    ) && (
        $this->promo->max_uses === null || 
        $this->promo->max_uses > $this->promo->usages->count()
    ) && in_array($this->promo->type, ['percentage', 'fixed_amount']);
});

// Calculate discounted prices
$discountedRent = computed(function () {
    if (!$this->selectedVariant || !$this->selectedVariant->rent_price) {
        return 0;
    }
    if (!$this->showPromo) {
        return $this->selectedVariant->rent_price;
    }
    if ($this->promo->type === 'percentage') {
        return max(0, $this->selectedVariant->rent_price * (1 - ($this->promo->value / 100)));
    } 
    if ($this->promo->type === 'fixed_amount') {
        return max(0, $this->selectedVariant->rent_price - $this->promo->value);
    }
    return $this->selectedVariant->rent_price;
});

$discountedSale = computed(function () {
    if (!$this->selectedVariant || !$this->selectedVariant->sale_price) {
        return 0;
    }
    if (!$this->showPromo) {
        return $this->selectedVariant->sale_price;
    }
    if ($this->promo->type === 'percentage') {
        return max(0, $this->selectedVariant->sale_price * (1 - ($this->promo->value / 100)));
    } 
    if ($this->promo->type === 'fixed_amount') {
        return max(0, $this->selectedVariant->sale_price - $this->promo->value);
    }
    return $this->selectedVariant->sale_price;
});

// Branch operational hours
$branchHours = computed(function () {
    return BranchSchedule::where('branch_id', $this->selectedBranch->id)
        ->orderBy('day_of_week')
        ->get();
});

$set = function($type, $value) {
    $this->$type = $value;
    $this->dispatch('variant-selected');
};
$checkBranchHours = function() {
    // Reset errors
    $this->branch_hours_error = null;
    $this->time_past_error = null;

    // Cek jika tanggal dan jam sudah diisi
    if (!$this->tanggal_ambil || !$this->jam_ambil) {
        return;
    }

    // Buat DateTime object untuk waktu booking dan waktu sekarang
    $bookingDateTime = \Carbon\Carbon::createFromFormat('Y-m-d H:i', $this->tanggal_ambil . ' ' . $this->jam_ambil);
    $now = \Carbon\Carbon::now();

    // Cek jika waktu booking sudah terlewat
    if ($bookingDateTime->lt($now)) {
        $this->time_past_error = 'Waktu booking tidak boleh lebih awal dari waktu sekarang';
        return;
    }

    // Lanjut pengecekan jam operasional cabang
    $dayOfWeek = $bookingDateTime->dayOfWeek; // 0 (Sunday) to 6 (Saturday)
    
    $branchHour = BranchSchedule::where('branch_id', $this->selectedBranch->id)
                        ->where('day_of_week', $dayOfWeek)
                        ->first();

    if (!$branchHour || !$branchHour->is_open) {
        $this->branch_hours_error = 'Cabang tutup pada hari yang dipilih';
        return;
    }

    $selectedTime = $bookingDateTime->format('H:i:s');
    $openTime = $branchHour->open_time;
    $closeTime = $branchHour->close_time;

    if ($selectedTime < $openTime || $selectedTime > $closeTime) {
        $this->branch_hours_error = "Jam operasional cabang: {$openTime} - {$closeTime}";
    }
};
$generateCode = function() {
    // Format: BR{YYMM}{nomor urut 4 digit}
    $currentYearMonth = date('ym'); // Tahun dan bulan 2 digit (contoh: 2406 untuk Juni 2024)
    
    // Ambil nomor urut terakhir di bulan ini
    $lastRent = Rent::where('code', 'like', 'RENT'.$currentYearMonth.'%')
                    ->orderBy('code', 'desc')
                    ->first();
    
    $sequenceNumber = 1;
    if ($lastRent) {
        // Ekstrak nomor urut dari kode terakhir
        $lastSequence = (int) substr($lastRent->code, 6);
        $sequenceNumber = $lastSequence + 1;
    }
    
    // Format nomor urut 4 digit dengan leading zero
    $formattedSequence = str_pad($sequenceNumber, 4, '0', STR_PAD_LEFT);
    
    return 'RENT' . $currentYearMonth . $formattedSequence;
};
$checkProductAvailability = function($productId, $startDate, $endDate) {
    // Cek stok total produk
    $product = ProductBranch::find($productId);
    $totalStock = $product ? 1 : 0;
    
    // Hitung produk yang sedang dipinjam pada rentang waktu tersebut
    $rentedCount = RentItem::join('rents', 'rent_items.rent_id', '=', 'rents.id')
        ->where('rent_items.product_branch_id', $productId)
        ->where(function($query) use ($startDate, $endDate) {
            $query->whereBetween('rents.start_date', [$startDate, $endDate])
                  ->orWhereBetween('rents.end_date', [$startDate, $endDate])
                  ->orWhere(function($q) use ($startDate, $endDate) {
                      $q->where('rents.start_date', '<=', $startDate)
                        ->where('rents.end_date', '>=', $endDate);
                  });
        })
        ->whereIn('rents.status', ['confirmed', 'on_rent']) // Status yang menggunakan stok
        ->count();
    
    return $totalStock - $rentedCount;
};
$sewaSekarang = function(){
    $this->validate([
        'tanggal_ambil' => 'required|date',
        'jam_ambil' => 'required',
        'jumlah_hari' => 'required',
    ]);
    $this->checkBranchHours();
    if ($this->time_past_error || $this->branch_hours_error) {
        $errorMessage = $this->time_past_error ?? $this->branch_hours_error;
        $this->dispatch('toast-info', message: 'Tidak bisa memesan: ' . $errorMessage);
        return;
    }
    $startDateTime = Carbon::createFromFormat('Y-m-d H:i', $this->tanggal_ambil . ' ' . $this->jam_ambil);
    $endDateTime = (clone $startDateTime)->addDays($this->jumlah_hari);
    // Cek ketersediaan produk
    $availableStock = $this->checkProductAvailability(
        $this->selectedVariant->id,
        $startDateTime->format('Y-m-d'),
        $endDateTime->format('Y-m-d')
    );
    
    if ($availableStock < 1) {
        $this->dispatch('toast-info', message: 'Produk tidak tersedia untuk tanggal dan jam yang dipilih');
    }

    try {
        DB::beginTransaction();

        $rentCode = $this->generateCode();
        // Generate kode booking
        // Buat data pemesanan
        $st = $this->selectedVariant->rent_price * $this->jumlah_hari;
        $gt = $this->selectedVariant->rent_price * $this->jumlah_hari + 4000;
        $rent = Rent::castAndCreate([
            'branch_id' => $this->selectedBranch->id,
            'user_id' => Auth::id(),
            'code' => $rentCode,
            'notes' => $this->notes ?? null,
            'start_date' => $startDateTime->format('Y-m-d'),
            'end_date' => $endDateTime->format('Y-m-d'),
            'start_time' => $this->jam_ambil,
            'end_time' => $this->jam_ambil, // Bisa disesuaikan
            'total_days' => $this->jumlah_hari,
            'discount_amount' => 0,
            'deposit_amount' => $this->deposit,
            'total_price' => $gt,
            'total_paid' => 0,
            'payment_type' => 'transfer',
            'type' => 'online',
            'status' => 'pending',
            'status_paid' => 'pending',
        ]);

        // Buat item pemesanan
        $item = RentItem::castAndCreate([
            'rent_id' => $rent->id,
            'product_branch_id' => $this->selectedVariant->id,
            'price' => $this->selectedVariant->rent_price,
            'qty' => $this->jumlah_hari,
            'discount' => 0,
            'subtotal' => $st,
        ]);

        DB::commit();
        return $this->redirect(route('consumer.transaction.sign', ['code' => $rent->code]), navigate: true);

    } catch (\Exception $e) {
        DB::rollBack();
        $this->dispatch('toast-error', message: 'Gagal membuat pemesanan: ' . $e->getMessage());
        Log::error('Booking error: ' . $e->getMessage());
    }
};
$calculateTotal = function(){
    $this->total_price_item = $this->selectedVariant->price * $this->jumlah_hari;
    $this->subtotal = $this->selectedVariant->price * $this->jumlah_hari;
    $this->grandtotal = $this->selectedVariant->price * $this->jumlah_hari + $this->biaya_layanan + $this->deposit;
};
$decreaseHari = function() {
    $this->jumlah_hari--;
    $this->calculateTotal();
};
$increaseHari = function(){
    $this->jumlah_hari++;
    $this->calculateTotal();
};
?>
<x-app>
    @volt
    <div id="kt_app_content" class="app-content flex-column-fluid py-10">
        <div class="container">
            <div class="row g-10">
                <!-- Product Gallery -->
                <div class="col-lg-7">
                    <div class="product-gallery">
                        <img src="{{ $this->getProductImage($this->selectedVariant) }}" 
                             class="img-fluid w-100" 
                             alt="{{ $this->product->name }}"
                             loading="lazy">
                    </div>
                    
                    <!-- Ulasan Section -->
                    <div class="card mt-10">
                        <div class="card-body p-8">
                            <div class="d-flex justify-content-between align-items-center mb-6">
                                <h4 class="mb-0">Ulasan</h4>
                                @if($this->product->ratings->count() > 0)
                                    <a href="#all-reviews" class="btn btn-sm btn-light-primary">
                                        Lihat Semua ({{ $this->product->ratings->count() }})
                                    </a>
                                @endif
                            </div>
                            
                            @if($this->product->ratings->count() > 0)
                                <!-- Rating Summary -->
                                <div class="row mb-8">
                                    <div class="col-md-4 text-center mb-4 mb-md-0">
                                        <div class="display-4 fw-bold text-primary">{{ number_format($this->product->averageRating(), 1) }}</div>
                                        <div class="rating-stars mb-2">
                                            @for($i = 1; $i <= 5; $i++)
                                                <i class="ki-{{ $i <= $this->product->averageRating() ? 'filled text-warning' : 'outline' }} ki-star"></i>
                                            @endfor
                                        </div>
                                        <div class="text-muted">Berdasarkan {{ $this->product->ratings->count() }} ulasan</div>
                                    </div>
                                    <div class="col-md-8">
                                        @for($i = 5; $i >= 1; $i--)
                                            <div class="d-flex align-items-center mb-2">
                                                <span class="me-2">{{ $i }} <i class="ki-filled ki-star text-warning"></i></span>
                                                <div class="progress flex-grow-1" style="height: 8px;">
                                                    @php
                                                        $count = $this->product->ratings->where('rating', $i)->count();
                                                        $percentage = $this->product->ratings->count() > 0 ? ($count / $this->product->ratings->count()) * 100 : 0;
                                                    @endphp
                                                    <div class="progress-bar bg-warning" role="progressbar" style="width: {{ $percentage }}%" aria-valuenow="{{ $percentage }}" aria-valuemin="0" aria-valuemax="100"></div>
                                                </div>
                                                <span class="ms-2 text-muted">{{ $count }}</span>
                                            </div>
                                        @endfor
                                    </div>
                                </div>
                                
                                <!-- Featured Reviews -->
                                <div class="mb-8">
                                    <h5 class="mb-4">Ulasan Teratas</h5>
                                    <div class="row g-4">
                                        @foreach($this->product->ratings->where('status', 'approved')->sortByDesc('created_at')->take(2) as $rating)
                                            <div class="col-md-6">
                                                <div class="review-card p-4 h-100">
                                                    <div class="d-flex align-items-center mb-3">
                                                        <div class="symbol symbol-35px symbol-circle me-3">
                                                            @if($rating->is_anonymous)
                                                                <span class="symbol-label bg-light-primary text-primary fw-bold">
                                                                    A
                                                                </span>
                                                            @else
                                                                <span class="symbol-label bg-light-primary text-primary fw-bold">
                                                                    {{ substr($rating->user->name, 0, 1) }}
                                                                </span>
                                                            @endif
                                                        </div>
                                                        <div>
                                                            <div class="fw-bold">
                                                                {{ $rating->is_anonymous ? 'Pengguna Anonim' : $rating->user->name }}
                                                            </div>
                                                            <div class="text-muted fs-7">
                                                                {{ $rating->created_at->diffForHumans() }}
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="rating-stars mb-2">
                                                        @for($i = 1; $i <= 5; $i++)
                                                            <i class="ki-{{ $i <= $rating->rating ? 'filled text-warning' : 'outline' }} ki-star"></i>
                                                        @endfor
                                                    </div>
                                                    <p class="mb-3">{{ $rating->review }}</p>
                                                    @if($rating->media->count() > 0)
                                                        <div class="review-media">
                                                            @foreach($rating->media->take(3) as $media)
                                                                <img src="{{ asset($media->media_path) }}" alt="Review media" class="img-thumbnail">
                                                            @endforeach
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                                
                                <!-- All Reviews Link -->
                                <div class="text-center">
                                    <a href="#all-reviews" class="btn btn-light-primary">Lihat Semua Ulasan</a>
                                </div>
                            @else
                                <div class="text-center py-8">
                                    <i class="ki-filled ki-message-text-2 fs-2x text-muted mb-3"></i>
                                    <p class="text-muted">Belum ada ulasan untuk produk ini</p>
                                    @auth
                                        <button class="btn btn-primary">Beri Ulasan</button>
                                    @endauth
                                </div>
                            @endif
                        </div>
                    </div>
                    
                    <!-- All Reviews Section -->
                    @if($this->product->ratings->count() > 0)
                        <div class="card mt-10" id="all-reviews">
                            <div class="card-body p-8">
                                <h4 class="mb-6">Semua Ulasan</h4>
                                <div class="row g-6">
                                    @foreach($this->product->ratings->where('status', 'approved')->sortByDesc('created_at') as $rating)
                                        <div class="col-12">
                                            <div class="review-card p-4">
                                                <div class="d-flex align-items-center mb-3">
                                                    <div class="symbol symbol-35px symbol-circle me-3">
                                                        @if($rating->is_anonymous)
                                                            <span class="symbol-label bg-light-primary text-primary fw-bold">
                                                                A
                                                            </span>
                                                        @else
                                                            <span class="symbol-label bg-light-primary text-primary fw-bold">
                                                                {{ substr($rating->user->name, 0, 1) }}
                                                            </span>
                                                        @endif
                                                    </div>
                                                    <div>
                                                        <div class="fw-bold">
                                                            {{ $rating->is_anonymous ? 'Pengguna Anonim' : $rating->user->name }}
                                                        </div>
                                                        <div class="text-muted fs-7">
                                                            {{ $rating->created_at->diffForHumans() }}
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="rating-stars mb-2">
                                                    @for($i = 1; $i <= 5; $i++)
                                                        <i class="ki-{{ $i <= $rating->rating ? 'filled text-warning' : 'outline' }} ki-star"></i>
                                                    @endfor
                                                </div>
                                                <p class="mb-3">{{ $rating->review }}</p>
                                                @if($rating->media->count() > 0)
                                                    <div class="review-media">
                                                        @foreach($rating->media as $media)
                                                            <img src="{{ asset($media->media_path) }}" alt="Review media" class="img-thumbnail">
                                                        @endforeach
                                                    </div>
                                                @endif
                                                @if($rating->rating > 3)
                                                    <div class="mt-3">
                                                        <span class="badge bg-success bg-opacity-10 text-success">
                                                            <i class="ki-filled ki-check-circle me-1"></i>Merekomendasikan produk ini
                                                        </span>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
                
                <!-- Product Details -->
                <div class="col-lg-5">
                    <div class="card">
                        <div class="card-body p-8">
                            <!-- Category -->
                            <div class="mb-4">
                                <span class="badge bg-primary bg-opacity-10 text-primary fs-7 fw-semibold rounded-pill py-2">
                                    {{ $this->product->category->name ?? 'No Category' }}
                                </span>
                            </div>
                            
                            <!-- Product Name -->
                            <h1 class="mb-2 fs-2x fw-bold text-gray-900">{{ $this->product->name }}</h1>
                            
                            <!-- Rating & Location -->
                            <div class="d-flex align-items-center text-muted fs-7 mb-6">
                                <span class="me-3">
                                    <i class="ki-solid ki-star text-warning me-1"></i>
                                    {{ number_format($this->product->averageRating()) }}
                                    @if($this->product->ratings->count() > 0)
                                        <span class="text-muted">({{ $this->product->ratings->count() }})</span>
                                    @endif
                                </span>
                                <span>
                                    <i class="ki-filled ki-geolocation text-danger me-1"></i> 
                                    {{ $this->selectedBranch->city->name ?? 'Unknown' }}
                                </span>
                            </div>
                            
                            <!-- Branch Info -->
                            <div class="alert alert-primary d-flex align-items-center p-4 rounded-4 shadow-sm mb-8">
                                <i class="ki-filled ki-shop fs-1 me-4"></i>
                                <div class="d-flex flex-column">
                                    <h4 class="mb-1">Cabang {{ $this->selectedBranch->name }}</h4>
                                    <span>Alamat: {{ $this->selectedBranch->address }}</span>
                                    <small class="text-muted">Jam Operasional: {{ $this->selectedBranch->operational_hours ?? '09:00 - 21:00' }}</small>
                                </div>
                            </div>
                            
                            <!-- Color Options -->
                            @if($this->colors->count() > 1)
                            <div class="mb-6">
                                <label class="form-label fw-semibold d-block mb-3">Warna</label>
                                <div class="d-flex flex-wrap gap-3">
                                    @foreach($this->colors as $variant)
                                        @if($variant->color)
                                        @php
                                        $isActive = $this->selectedColor == $variant->color_id;
                                        $isDisabled = $this->selectedStorage && !$this->variants->contains(fn($v) => 
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
                            @if($this->storages->count() > 1)
                            <div class="mb-8">
                                <label class="form-label fw-semibold d-block mb-3">Storage</label>
                                <div class="d-flex flex-wrap gap-3">
                                    @foreach($this->storages as $variant)
                                        @php
                                            $isActive = $this->selectedStorage == $variant->storage_id;
                                            $isDisabled = $this->selectedColor && !$this->variants->contains(fn($v) => 
                                                $v->storage_id == $variant->storage_id && 
                                                $v->color_id == $this->selectedColor
                                            );
                                        @endphp
                                        <span 
                                            class="badge bg-light text-dark py-3 px-4 fs-6 fw-semibold storage-option {{ $isActive ? 'active' : '' }} {{ $isDisabled ? 'disabled' : '' }}"
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
                            
                            <!-- Price Display -->
                            <div class="price-display mb-8">
                                @if($this->selectedVariant && $this->selectedVariant->sale_price > 0)
                                    <!-- Rental Price Section -->
                                    <div class="rental-price mb-6">
                                        <div class="d-flex align-items-baseline">
                                            <span class="fs-2x fw-bold text-primary me-2">
                                                Rp{{ number_format($this->discountedRent, 0, ',', '.') }}
                                            </span>
                                            <span class="text-muted fs-6">/hari</span>
                                            
                                            @if($this->showPromo)
                                                <span class="text-muted fs-7 ms-2 text-decoration-line-through">
                                                    Rp{{ number_format($this->selectedVariant->rent_price, 0, ',', '.') }}
                                                </span>
                                            @endif
                                        </div>
                                        <div class="text-muted fs-7 mt-1">Sewa per hari</div>
                                    </div>
                                    
                                    <!-- Divider with improved spacing -->
                                    <div class="separator separator-content my-6">atau</div>
                                    
                                    <!-- Purchase Option Section -->
                                    <div class="purchase-option bg-light-success p-4 rounded-3 mb-6">
                                        <div class="d-flex flex-wrap align-items-center">
                                            <span class="fs-6 me-2">Dapat dimiliki dengan:</span>
                                            <span class="fs-2x fw-bold text-success me-2">
                                                Rp{{ number_format($this->discountedSale, 0, ',', '.') }}
                                            </span>
                                            
                                            @if($this->showPromo)
                                                <span class="text-muted fs-7 text-decoration-line-through">
                                                    Rp{{ number_format($this->selectedVariant->sale_price, 0, ',', '.') }}
                                                </span>
                                            @endif
                                        </div>
                                        <div class="mt-1">
                                            <span class="badge bg-success bg-opacity-10 text-success fs-7">
                                                <i class="ki-filled ki-star me-1"></i>Hemat hingga {{ number_format(100 - ($this->selectedVariant->sale_price/$this->selectedVariant->rent_price)*100, 0) }}%
                                            </span>
                                        </div>
                                    </div>
                                @else
                                    <!-- Rental Only Section -->
                                    <div class="rental-price mb-6">
                                        <div class="d-flex align-items-baseline">
                                            <span class="fs-2x fw-bold text-primary me-2">
                                                Rp{{ number_format($this->discountedRent, 0, ',', '.') }}
                                            </span>
                                            <span class="text-muted fs-6">/hari</span>
                                            
                                            @if($this->showPromo)
                                                <span class="text-muted fs-7 ms-2 text-decoration-line-through">
                                                    Rp{{ number_format($this->selectedVariant->rent_price, 0, ',', '.') }}
                                                </span>
                                            @endif
                                        </div>
                                        <div class="text-muted fs-7 mt-1">Sewa per hari</div>
                                    </div>
                                @endif
                            </div>
                            
                            <!-- Pickup Date and Time -->
                            <div class="pickup-datetime mb-8">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <x-form-group name="tanggal_ambil" label="Tanggal Ambil" required>
                                            <x-form-input type="date" name="tanggal_ambil" min="{{ now()->format('Y-m-d') }}" class="bg-transparent" id="tanggal_ambil"/>
                                        </x-form-group>
                                    </div>
                                    <div class="col-md-6">
                                        <x-form-group name="jam_ambil" label="Jam Ambil" required>
                                            <x-form-input type="time" name="jam_ambil" min="{{ now()->format('H:i') }}" class="bg-transparent" id="jam_ambil"/>
                                        </x-form-group>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Quantity Selector -->
                            <div class="d-flex align-items-center mb-8">
                                <label class="form-label fw-semibold me-4">
                                    Jumlah @if($this->selectedVariant->sale_price == 0) Hari @endif :
                                    <span class="ms-1" data-bs-toggle="tooltip" aria-label="{{$this->selectedVariant->sale_price > 0 ? 'Jumlah = Jumlah hari sewa atau Quantity yang ingin di beli' : '' }}" data-bs-original-title="{{$this->selectedVariant->sale_price > 0 ? 'Jumlah = Jumlah hari sewa atau Quantity yang ingin di beli' : 'Jumlah hari kamu ingin sewa' }}">
                                        <i class="ki-filled ki-information text-gray-500 fs-6"></i>
                                    </span>
                                </label>
                                <!--begin::Dialer-->
                                <div class="position-relative w-md-300px"
                                    data-kt-dialer="true"
                                    data-kt-dialer-min="1"
                                    data-kt-dialer-max="365"
                                    data-kt-dialer-step="1"
                                    data-kt-dialer-prefix=""
                                    data-kt-dialer-decimals="0">

                                    <!--begin::Decrease control-->
                                    <button type="button" wire:click="decreaseHari" class="btn btn-icon btn-active-color-gray-700 position-absolute translate-middle-y top-50 start-0" data-kt-dialer-control="decrease">
                                        <i class="ki-filled ki-minus-squared fs-2"></i>
                                    </button>
                                    <!--end::Decrease control-->

                                    <!--begin::Input control-->
                                    <input type="text" wire:model.live="jumlah_hari" class="form-control form-control-solid border-0 ps-12" data-kt-dialer-control="input" placeholder="Amount" readonly />
                                    <!--end::Input control-->

                                    <!--begin::Increase control-->
                                    <button type="button" wire:click="increaseHari" class="btn btn-icon btn-active-color-gray-700 position-absolute translate-middle-y top-50 end-0" data-kt-dialer-control="increase">
                                        <i class="ki-filled ki-plus-squared fs-2"></i>
                                    </button>
                                    <!--end::Increase control-->
                                </div>
                                <!--end::Dialer-->
                            </div>
                            <small class="form-text text-muted mb-10">
                                
                            </small>
                            
                            <!-- Action Buttons (Desktop) -->
                            <div class="d-grid gap-3 d-none d-lg-block">
                                <div class="d-flex gap-3">
                                    <button class="btn btn-primary rounded-pill fw-bold py-3 flex-grow-1" 
                                            wire:click="sewaSekarang">
                                        <i class="ki-filled ki-calendar fs-2 me-2"></i> Sewa Sekarang
                                    </button>
                                    @if($this->selectedVariant->sale_price > 0)
                                        <button class="btn btn-warning rounded-pill fw-bold py-3 flex-grow-1" 
                                                wire:click="addToCart('buy')">
                                            <i class="ki-filled ki-purchase fs-2 me-2"></i> Beli Sekarang
                                        </button>
                                        <button class="btn btn-success rounded-pill fw-bold py-3 flex-grow-1" 
                                                wire:click="addToCart('cart')">
                                            <i class="ki-filled ki-cart fs-2 me-2"></i> Keranjang
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Branch Operational Hours -->
                    <div class="card mt-10">
                        <div class="card-body p-8">
                            <h4 class="mb-4">Jam Operasional Cabang</h4>
                            <div class="table-responsive">
                                <table class="table table-row-dashed align-middle">
                                    <thead>
                                        <tr>
                                            <th>Hari</th>
                                            <th>Jam Buka</th>
                                            <th>Jam Tutup</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($this->branchHours as $hour)
                                        @php
                                        // Format day of week
                                        $formatDay = function ($day) {
                                            $days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
                                            return $days[$day] ?? $day;
                                        };
                                        @endphp
                                        <tr>
                                            <td>{{ $formatDay($hour->day_of_week) }}</td>
                                            <td>{{ $hour->open_time }}</td>
                                            <td>{{ $hour->close_time }}</td>
                                            <td>
                                                @if($hour->is_open)
                                                    <span class="badge bg-success">Buka</span>
                                                @else
                                                    <span class="badge bg-danger">Tutup</span>
                                                @endif
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Sticky Action Bar (Mobile) -->
        <div class="sticky-action-bar d-lg-none" id="stickyActionBar">
            <div class="container">
                <div class="d-flex align-items-center justify-content-between">
                    <div class="d-flex flex-column me-3">
                        <span class="fw-bold text-gray-900 fs-4">Rp{{ number_format($this->discountedRent, 0, ',', '.') }}</span>
                        <span class="text-muted fs-7">/hari</span>
                    </div>
                    <div class="d-flex gap-2">
                        <button class="btn btn-primary rounded-pill fw-bold py-3 px-4" 
                                wire:click="addToCart('rent')">
                            <i class="ki-filled ki-calendar fs-2 me-1"></i> Sewa
                        </button>
                        @if($this->selectedVariant->sale_price > 0)
                            <button class="btn btn-warning rounded-pill fw-bold py-3 px-4" 
                                    wire:click="addToCart('buy')">
                                <i class="ki-filled ki-purchase fs-2 me-1"></i> Beli
                            </button>
                            <button class="btn btn-success rounded-pill fw-bold py-3 px-4" 
                                    wire:click="addToCart('cart')">
                                <i class="ki-filled ki-cart fs-2 me-1"></i> Keranjang
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endvolt
    @section('custom_js')
    <script data-navigate-once>
        document.addEventListener('DOMContentLoaded', () => {
            // Quantity controls
            Livewire.on('decrease-hari', () => {
                if (this.jumlah_hari > 1) {
                    this.jumlah_hari--;
                }
            });
            
            Livewire.on('increase-hari', () => {
                this.jumlah_hari++;
            });
            
            // Show sticky action bar on scroll
            window.addEventListener('scroll', function() {
                const actionBar = document.getElementById('stickyActionBar');
                if (window.scrollY > 100) {
                    actionBar.classList.add('show');
                } else {
                    actionBar.classList.remove('show');
                }
            });
            
            // Initialize date picker
            const today = new Date().toISOString().split('T')[0];
            document.getElementById('pickupDate').min = today;
            
            // Set default pickup time to next hour
            const now = new Date();
            const nextHour = new Date(now.getTime() + 60 * 60 * 1000);
            document.getElementById('pickupTime').value = nextHour.toTimeString().substring(0, 5);
        });
        document.addEventListener('livewire:navigated', () => {
            // Quantity controls
            Livewire.on('decrease-hari', () => {
                if (this.jumlah_hari > 1) {
                    this.jumlah_hari--;
                }
            });
            
            Livewire.on('increase-hari', () => {
                this.jumlah_hari++;
            });
            
            // Show sticky action bar on scroll
            window.addEventListener('scroll', function() {
                const actionBar = document.getElementById('stickyActionBar');
                if (window.scrollY > 100) {
                    actionBar.classList.add('show');
                } else {
                    actionBar.classList.remove('show');
                }
            });
            
            // Initialize date picker
            const today = new Date().toISOString().split('T')[0];
            document.getElementById('pickupDate').min = today;
            
            // Set default pickup time to next hour
            const now = new Date();
            const nextHour = new Date(now.getTime() + 60 * 60 * 1000);
            document.getElementById('pickupTime').value = nextHour.toTimeString().substring(0, 5);
        });
    </script>
    @endsection
</x-app>