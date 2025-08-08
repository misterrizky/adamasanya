<?php
use App\Models\Product;
use App\Models\Master\Brand;
use function Laravel\Folio\{name};
use function Livewire\Volt\{state, computed, usesPagination};
name('brand');
usesPagination(theme: 'bootstrap');

state(['search' => '']);
$brands = computed(function() {
    return Brand::where('st', 'a')
                ->when($this->search, function($query) {
                    $query->where('name', 'like', '%'.$this->search.'%');
                })
                ->orderBy('name')
                ->paginate(12);
});
?>
<x-app>
    @volt
    <div class="d-flex flex-column flex-column-fluid">
        <!-- Hero Section -->
        <div class="hero-section py-10 py-lg-15">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-lg-6 mb-10 mb-lg-0">
                        <h1 class="display-4 fw-bold mb-5">Temukan Berbagai Merek Terbaik</h1>
                        <p class="fs-3 mb-8">Kami bekerja sama dengan merek-merek ternama untuk menyediakan produk berkualitas untuk kebutuhan Anda.</p>
                    </div>
                    <div class="col-lg-6">
                        <img src="{{ asset('media/illustrations/brands.png') }}" alt="Brand Illustration" class="img-fluid rounded-3 w-75 float-end theme-light-show">
                        <img src="{{ asset('media/illustrations/brands-dark.png') }}" alt="Brand Illustration" class="img-fluid rounded-3 w-75 float-end theme-dark-show">
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div id="kt_app_content" class="app-content flex-column-fluid py-10">
            <div class="container">
                <!-- Search & Filter -->
                <div class="d-flex flex-wrap justify-content-between align-items-center mb-10">
                    <h2 class="fw-bold fs-2hx text-gray-900 mb-5">Daftar Merek</h2>
                    
                    <div class="d-flex flex-wrap gap-5">
                        <div class="position-relative w-250px">
                            <i class="ki-duotone ki-magnifier position-absolute top-50 translate-middle-y ms-4">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>
                            <input type="text" wire:model.live="search" class="form-control form-control-solid ps-12" placeholder="Cari merek...">
                        </div>
                    </div>
                </div>

                <!-- Brands Grid -->
                @if($this->brands->count() > 0)
                <div class="row g-10">
                    @foreach($this->brands as $brand)
                    @php
                        $productCount = Product::where('brand_id', $brand->id)->count();
                    @endphp
                    <div class="col-6 col-md-4 col-lg-3 col-xl-2">
                        <a href="{{ route('brand.show', ['slug' => $brand]) }}" wire:navigate class="card card-category card-hover h-100 text-center p-5">
                            <div class="card-body d-flex flex-column align-items-center justify-content-center">
                                <div class="symbol symbol-50px mb-4">
                                    <img src="{{ Str::remove('-dark',$brand->image) }}" class="rounded-3 theme-light-show" alt="{{ $brand->name }}">
                                    <img src="{{ $brand->image }}" class="rounded-3 theme-dark-show" alt="{{ $brand->name }}">
                                </div>
                                <span class="text-muted fs-7">{{ $productCount }} produk</span>
                            </div>
                        </a>
                    </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="justify-content-center mt-10">
                {{ $this->brands->links() }}
                </div>
                @else
                <div class="text-center py-15">
                    <img src="{{ asset('media/illustrations/there-is-nothing-here.png') }}" class="w-200px mb-5 theme-light-show" alt="No Brands">
                    <img src="{{ asset('media/illustrations/there-is-nothing-here-dark.png') }}" class="w-200px mb-5 theme-dark-show" alt="No Brands">
                    <h3 class="text-gray-600">Merek tidak ditemukan</h3>
                    <p class="text-muted">Silakan cari dengan kata kunci lain</p>
                </div>
                @endif
            </div>
        </div>
    </div>
    @endvolt
</x-app>