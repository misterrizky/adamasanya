<?php
use function Livewire\Volt\{state, computed};
use function Laravel\Folio\{name};
name('branch.show');
state(['branch' => fn() => \App\Models\Branch::findOrFail(request()->route('branch'))]);

// Get products available in this branch
$productRents = computed(function() {
    return \App\Models\ProductRent::with(['product'])
        ->where('branch_id', $this->branch->id)
        ->where('is_publish', 1)
        ->whereNull('deleted_at')
        ->limit(6)
        ->get();
});

$productSales = computed(function() {
    return \App\Models\ProductSale::with(['product'])
        ->where('branch_id', $this->branch->id)
        ->where('is_publish', 1)
        ->whereNull('deleted_at')
        ->limit(6)
        ->get();
});

// Get branch schedule and current status
$schedules = computed(function() {
    $today = date('w'); // 0 (Sunday) to 6 (Saturday)
    $currentTime = date('H:i:s');
    
    $schedules = \App\Models\BranchSchedule::where('branch_id', $this->branch->id)
        ->orderBy('day_of_week')
        ->get();
        
    // Get today's schedule
    $todaySchedule = $schedules->where('day_of_week', $today)->first();
    
    // Determine current status
    if ($todaySchedule) {
        $timeToClose = strtotime($todaySchedule->close_time) - strtotime($currentTime);
        $isClosingSoon = $timeToClose > 0 && $timeToClose <= 3600; // Within 1 hour
        
        $this->branch->is_open_today = $todaySchedule->is_open;
        $this->branch->open_time = $todaySchedule->open_time;
        $this->branch->close_time = $todaySchedule->close_time;
        $this->branch->is_currently_open = $todaySchedule->is_open && 
            $currentTime >= $todaySchedule->open_time && 
            $currentTime <= $todaySchedule->close_time;
        $this->branch->is_closing_soon = $isClosingSoon;
        $this->branch->is_closed_for_today = $todaySchedule->is_open && 
            $currentTime > $todaySchedule->close_time;
            
        // Determine status text and styling
        if (!$todaySchedule->is_open) {
            $this->branch->status_text = 'Tutup';
            $this->branch->status_class = 'danger';
            $this->branch->status_icon = 'cross-circle';
        } elseif ($this->branch->is_currently_open) {
            if ($this->branch->is_closing_soon) {
                $this->branch->status_text = 'Segera Tutup';
                $this->branch->status_class = 'warning';
                $this->branch->status_icon = 'clock';
            } else {
                $this->branch->status_text = 'Buka';
                $this->branch->status_class = 'success';
                $this->branch->status_icon = 'check-circle';
            }
        } elseif ($this->branch->is_closed_for_today) {
            $this->branch->status_text = 'Tutup';
            $this->branch->status_class = 'danger';
            $this->branch->status_icon = 'cross-circle';
        } else {
            // Will open later today
            $this->branch->status_text = 'Akan Buka';
            $this->branch->status_class = 'info';
            $this->branch->status_icon = 'clock';
        }
    } else {
        $this->branch->is_open_today = false;
        $this->branch->status_text = 'Tutup';
        $this->branch->status_class = 'danger';
        $this->branch->status_icon = 'cross-circle';
    }
    
    return $schedules;
});
?>


<x-app>
    @volt
    <div class="d-flex flex-column flex-column-fluid">
        <!-- Hero Section -->
        <div class="hero-branch position-relative mb-10">
            <div class="hero-overlay position-absolute top-0 start-0 w-100 h-100 bg-dark opacity-50"></div>
            <div class="container position-relative z-index-1 py-15">
                <div class="d-flex flex-column align-items-start">
                    <h1 class="text-white fw-bold display-4 mb-3">{{ $branch->name }}</h1>
                    <div class="d-flex align-items-center mb-5">
                        <span class="badge badge-light-primary fs-6 fw-semibold me-3">
                            <i class="ki-outline ki-geolocation fs-4 me-2"></i>
                            {{ $branch->city->name }}
                        </span>
                        <span class="badge badge-light-success fs-6 fw-semibold">
                            <i class="ki-outline ki-check-circle fs-4 me-2"></i>
                            Cabang {{ $branch->is_hq == 'y' ? 'Utama' : 'Kami' }}
                        </span>
                    </div>
                    <a href="#contact" class="btn btn-primary btn-lg px-6">
                        <i class="ki-outline ki-phone fs-2 me-2"></i> Hubungi Kami
                    </a>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div id="kt_app_content" class="app-content flex-column-fluid">
            <!-- Branch Info Cards -->
            <div class="row g-10 mb-15">
                <!-- About Branch -->
                <div class="col-lg-8">
                    <div class="card card-flush h-100">
                        <div class="card-header">
                            <h2 class="card-title fw-bold text-gray-900">Tentang Cabang</h2>
                        </div>
                        <div class="card-body">
                            <div class="fs-5 fw-semibold text-gray-600 mb-7">
                                <p>{{ $branch->description ?? 'Cabang kami menyediakan berbagai produk berkualitas untuk kebutuhan Anda.' }}</p>
                                
                                <div class="row g-5 mb-7">
                                    <div class="col-md-6">
                                        <div class="d-flex align-items-center p-4 bg-light-primary rounded-3">
                                            <i class="ki-outline ki-home-2 fs-2hx text-primary me-4"></i>
                                            <div>
                                                <h4 class="fw-bold text-gray-900 mb-1">Lokasi Strategis</h4>
                                                <span class="text-gray-600">Mudah dijangkau dari berbagai arah</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="d-flex align-items-center p-4 bg-light-success rounded-3">
                                            <i class="ki-outline ki-shield-tick fs-2hx text-success me-4"></i>
                                            <div>
                                                <h4 class="fw-bold text-gray-900 mb-1">Produk Berkualitas</h4>
                                                <span class="text-gray-600">Barang terjamin kualitasnya</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <h3 class="fw-bold text-gray-900 mb-4">Fasilitas Cabang</h3>
                                <div class="row g-4">
                                    <div class="col-6 col-md-4">
                                        <div class="d-flex align-items-center">
                                            <i class="ki-outline ki-check-circle fs-2 text-success me-3"></i>
                                            <span class="fw-semibold">Parkir Luas</span>
                                        </div>
                                    </div>
                                    <div class="col-6 col-md-4">
                                        <div class="d-flex align-items-center">
                                            <i class="ki-outline ki-check-circle fs-2 text-success me-3"></i>
                                            <span class="fw-semibold">AC Ruangan</span>
                                        </div>
                                    </div>
                                    <div class="col-6 col-md-4">
                                        <div class="d-flex align-items-center">
                                            <i class="ki-outline ki-check-circle fs-2 text-success me-3"></i>
                                            <span class="fw-semibold">Toilet Bersih</span>
                                        </div>
                                    </div>
                                    <div class="col-6 col-md-4">
                                        <div class="d-flex align-items-center">
                                            <i class="ki-outline ki-check-circle fs-2 text-success me-3"></i>
                                            <span class="fw-semibold">Wifi Gratis</span>
                                        </div>
                                    </div>
                                    <div class="col-6 col-md-4">
                                        <div class="d-flex align-items-center">
                                            <i class="ki-outline ki-check-circle fs-2 text-success me-3"></i>
                                            <span class="fw-semibold">Ruang Tunggu</span>
                                        </div>
                                    </div>
                                    <div class="col-6 col-md-4">
                                        <div class="d-flex align-items-center">
                                            <i class="ki-outline ki-check-circle fs-2 text-success me-3"></i>
                                            <span class="fw-semibold">Mushola</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Operating Hours -->
                <div class="col-lg-4">
                    <div class="card card-flush h-100">
                        <div class="card-header">
                            <h2 class="card-title fw-bold text-gray-900">Jam Operasional</h2>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle">
                                    <tbody>
                                        @foreach($this->schedules as $schedule)
                                        <tr class="{{ $schedule->is_open ? '' : 'text-muted' }}">
                                            <td class="w-50 fw-semibold">
                                                {{ \Carbon\Carbon::create()->startOfWeek()->addDays($schedule->day_of_week)->isoFormat('dddd') }}
                                            </td>
                                            <td class="w-50">
                                                @if($schedule->is_open)
                                                {{ \Carbon\Carbon::parse($schedule->open_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($schedule->close_time)->format('H:i') }}
                                                @else
                                                <span class="badge badge-light-danger">Tutup</span>
                                                @endif
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            
                            <div class="notice d-flex bg-light-primary rounded-3 p-6 mt-5">
                                <i class="ki-outline ki-information fs-2 text-primary me-4"></i>
                                <div class="d-flex flex-stack flex-grow-1">
                                    <div class="fw-semibold">
                                        <h4 class="text-gray-900 fw-bold">Perhatian!</h4>
                                        <div class="fs-6 text-gray-700">Jam operasional dapat berubah pada hari libur nasional.</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Products Section -->
            <div class="mb-15">
                <!-- Product Rent -->
                <div class="card card-flush mb-10">
                    <div class="card-header pt-7">
                        <h3 class="card-title align-items-start flex-column">
                            <span class="card-label fw-bold text-gray-800">Produk Sewa</span>
                            <span class="text-gray-500 mt-1 fw-semibold fs-6">Tersedia di cabang ini</span>
                        </h3>
                        <div class="card-toolbar">
                            <a href="{{ route('product-rent') }}?branch={{ $branch->id }}" 
                                wire:navigate 
                                class="btn btn-sm btn-light-primary">
                                Lihat Semua
                            </a>
                        </div>
                    </div>
                    <div class="card-body pt-4">
                        <div class="row g-5">
                            @foreach($this->productRents as $rent)
                            <div class="col-md-4 col-6">
                                <div class="card card-custom card-border h-100 product-card">
                                    <div class="card-body p-5">
                                        <div class="text-center mb-5">
                                            <div class="symbol symbol-150px symbol-circle mb-5">
                                                <img src="{{ $rent->product->image ?? asset('media/stock/600x400/img-73.jpg') }}" 
                                                        class="rounded-3 w-100 h-100 object-fit-cover" 
                                                        alt="{{ $rent->product->name }}">
                                            </div>
                                            <h4 class="text-gray-800 fw-bold mb-1">{{ $rent->product->name }}</h4>
                                            <span class="badge badge-light-primary mb-2">{{ $rent->product->category->name }}</span>
                                            <div class="rating mb-2">
                                                <div class="rating-label checked">
                                                    <i class="ki-outline ki-star fs-6"></i>
                                                </div>
                                                <div class="rating-label checked">
                                                    <i class="ki-outline ki-star fs-6"></i>
                                                </div>
                                                <div class="rating-label checked">
                                                    <i class="ki-outline ki-star fs-6"></i>
                                                </div>
                                                <div class="rating-label checked">
                                                    <i class="ki-outline ki-star fs-6"></i>
                                                </div>
                                                <div class="rating-label">
                                                    <i class="ki-outline ki-star fs-6"></i>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="d-flex flex-column">
                                            <span class="fw-bold text-primary fs-3 mb-3">Rp {{ number_format($rent->price) }}/hari</span>
                                            <a href="{{ route('product-rent.show', ['productRent' => $rent]) }}" 
                                                wire:navigate 
                                                class="btn btn-primary w-100">
                                                <i class="ki-outline ki-shopping-cart fs-2 me-2"></i> Sewa Sekarang
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                            
                            @if($this->productRents->isEmpty())
                            <div class="col-12 text-center py-10">
                                <img src="{{ asset('media/illustrations/sigma-1/4.png') }}" 
                                        class="w-100px mb-5" 
                                        alt="No Products">
                                <h4 class="text-gray-600">Belum ada produk sewa</h4>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
                
                <!-- Product Sale -->
                <div class="card card-flush">
                    <div class="card-header pt-7">
                        <h3 class="card-title align-items-start flex-column">
                            <span class="card-label fw-bold text-gray-800">Produk Dijual</span>
                            <span class="text-gray-500 mt-1 fw-semibold fs-6">Tersedia di cabang ini</span>
                        </h3>
                        <div class="card-toolbar">
                            <a href="{{ route('product-sale') }}?branch={{ $branch->id }}" 
                                wire:navigate 
                                class="btn btn-sm btn-light-primary">
                                Lihat Semua
                            </a>
                        </div>
                    </div>
                    <div class="card-body pt-4">
                        <div class="row g-5">
                            @foreach($this->productSales as $sale)
                            <div class="col-md-4 col-6">
                                <div class="card card-custom card-border h-100 product-card">
                                    <div class="card-body p-5">
                                        <div class="text-center mb-5">
                                            <div class="symbol symbol-150px symbol-circle mb-5">
                                                <img src="{{ $sale->product->image ?? asset('media/stock/600x400/img-73.jpg') }}" 
                                                        class="rounded-3 w-100 h-100 object-fit-cover" 
                                                        alt="{{ $sale->product->name }}">
                                            </div>
                                            <h4 class="text-gray-800 fw-bold mb-1">{{ $sale->product->name }}</h4>
                                            <span class="badge badge-light-primary mb-2">{{ $sale->product->category->name }}</span>
                                            <div class="rating mb-2">
                                                <div class="rating-label checked">
                                                    <i class="ki-outline ki-star fs-6"></i>
                                                </div>
                                                <div class="rating-label checked">
                                                    <i class="ki-outline ki-star fs-6"></i>
                                                </div>
                                                <div class="rating-label checked">
                                                    <i class="ki-outline ki-star fs-6"></i>
                                                </div>
                                                <div class="rating-label checked">
                                                    <i class="ki-outline ki-star fs-6"></i>
                                                </div>
                                                <div class="rating-label">
                                                    <i class="ki-outline ki-star fs-6"></i>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="d-flex flex-column">
                                            <span class="fw-bold text-primary fs-3 mb-3">Rp {{ number_format($sale->price) }}</span>
                                            <a href="{{ route('product-sale.show', ['productSale' => $sale]) }}" 
                                                wire:navigate 
                                                class="btn btn-primary w-100">
                                                <i class="ki-outline ki-shopping-cart fs-2 me-2"></i> Beli Sekarang
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                            
                            @if($this->productSales->isEmpty())
                            <div class="col-12 text-center py-10">
                                <img src="{{ asset('media/illustrations/sigma-1/17.png') }}" 
                                        class="w-100px mb-5" 
                                        alt="No Products">
                                <h4 class="text-gray-600">Belum ada produk dijual</h4>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Branch Gallery -->
            <div class="card mb-15">
                <div class="card-header">
                    <h3 class="card-title align-items-start flex-column">
                        <span class="card-label fw-bold text-gray-800">Galeri Cabang</span>
                        <span class="text-gray-500 mt-1 fw-semibold fs-6">Foto-foto {{ $branch->name }}</span>
                    </h3>
                </div>
                <div class="card-body p-0">
                    <div class="row g-0">
                        <div class="col-md-4">
                            <div class="gallery-item">
                                <img src="{{ asset('media/stock/600x600/img-1.jpg') }}" class="w-100 h-300px object-fit-cover" alt="Gallery">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="gallery-item">
                                <img src="{{ asset('media/stock/600x600/img-2.jpg') }}" class="w-100 h-300px object-fit-cover" alt="Gallery">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="gallery-item">
                                <img src="{{ asset('media/stock/600x600/img-3.jpg') }}" class="w-100 h-300px object-fit-cover" alt="Gallery">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="gallery-item">
                                <img src="{{ asset('media/stock/600x400/img-4.jpg') }}" class="w-100 h-300px object-fit-cover" alt="Gallery">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="gallery-item">
                                <img src="{{ asset('media/stock/600x400/img-5.jpg') }}" class="w-100 h-300px object-fit-cover" alt="Gallery">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Contact & Map Section -->
            <div class="row g-10 mb-15" id="contact">
                <!-- Contact Info -->
                <div class="col-lg-4">
                    <div class="card card-flush h-100">
                        <div class="card-header">
                            <h3 class="card-title fw-bold text-gray-900">Hubungi Kami</h3>
                        </div>
                        <div class="card-body">
                            <div class="d-flex flex-column gap-7">
                                <div class="d-flex align-items-center">
                                    <div class="symbol symbol-50px symbol-circle me-5">
                                        <span class="symbol-label bg-light-primary">
                                            <i class="ki-outline ki-phone fs-2x text-primary"></i>
                                        </span>
                                    </div>
                                    <div class="d-flex flex-column">
                                        <span class="text-gray-600 fw-semibold">Telepon</span>
                                        <a href="tel:{{ $branch->phone }}" class="text-gray-800 fw-bold text-hover-primary">{{ $branch->phone }}</a>
                                    </div>
                                </div>
                                
                                <div class="d-flex align-items-center">
                                    <div class="symbol symbol-50px symbol-circle me-5">
                                        <span class="symbol-label bg-light-success">
                                            <i class="ki-outline ki-sms fs-2x text-success"></i>
                                        </span>
                                    </div>
                                    <div class="d-flex flex-column">
                                        <span class="text-gray-600 fw-semibold">Email</span>
                                        <a href="mailto:{{ $branch->email }}" class="text-gray-800 fw-bold text-hover-primary">{{ $branch->email }}</a>
                                    </div>
                                </div>
                                
                                <div class="d-flex align-items-center">
                                    <div class="symbol symbol-50px symbol-circle me-5">
                                        <span class="symbol-label bg-light-warning">
                                            <i class="ki-outline ki-instagram fs-2x text-warning"></i>
                                        </span>
                                    </div>
                                    <div class="d-flex flex-column">
                                        <span class="text-gray-600 fw-semibold">Instagram</span>
                                        <a href="https://instagram.com/{{ $branch->ig }}" target="_blank" class="text-gray-800 fw-bold text-hover-primary">{{ $branch->ig }}</a>
                                    </div>
                                </div>
                                
                                <div class="d-flex align-items-center">
                                    <div class="symbol symbol-50px symbol-circle me-5">
                                        <span class="symbol-label bg-light-danger">
                                            <i class="ki-outline ki-geolocation fs-2x text-danger"></i>
                                        </span>
                                    </div>
                                    <div class="d-flex flex-column">
                                        <span class="text-gray-600 fw-semibold">Alamat</span>
                                        <span class="text-gray-800 fw-bold">{{ $branch->address }}, Kel. {{ $branch->village->name }}, Kec. {{ $branch->subdistrict->name }}, {{ $branch->city->name }}, {{ $branch->state->name }}, {{ $branch->country->name }} {{ $branch->village->poscode }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Branch Location Map -->
                <div class="col-lg-8">
                    <div class="card card-flush h-100">
                        <div class="card-header">
                            <h3 class="card-title fw-bold text-gray-900">Lokasi Cabang</h3>
                        </div>
                        <div class="card-body p-0">
                            <div class="map-container" style="height: 400px;">
                                {!! $branch->map_embed !!}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endvolt

    @section('custom_css')
    <style>
        .hero-branch {
            background: url('{{ asset("media/stock/1920x1080/img-1.jpg") }}') no-repeat center center;
            background-size: cover;
            height: 400px;
            display: flex;
            align-items: center;
        }
        
        .product-card {
            transition: all 0.3s ease;
            border: 1px solid #f5f5f5;
        }
        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
            border-color: var(--kt-primary);
        }
        
        .gallery-item {
            position: relative;
            overflow: hidden;
            transition: all 0.3s ease;
        }
        .gallery-item:hover {
            transform: scale(1.02);
        }
        .gallery-item:after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0,0,0,0.2);
            opacity: 0;
            transition: all 0.3s ease;
        }
        .gallery-item:hover:after {
            opacity: 1;
        }
        
        .map-container {
            position: relative;
            overflow: hidden;
            background: #f5f5f5;
        }
        .map-container iframe {
            width: 100%;
            height: 100%;
            border: none;
        }
        
        .card-border {
            border: 1px solid #f5f5f5;
            transition: all 0.3s ease;
        }
        .card-border:hover {
            border-color: var(--kt-primary);
            transform: translateY(-3px);
        }
        
        .rating-label {
            display: inline-block;
            color: #E4E6EF;
        }
        .rating-label.checked {
            color: #FFC700;
        }
    </style>
    @endsection
    
    @section('custom_js')
    <script data-navigate-once>
        // Smooth scroll for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                document.querySelector(this.getAttribute('href')).scrollIntoView({
                    behavior: 'smooth'
                });
            });
        });
    </script>
    @endsection
</x-app>