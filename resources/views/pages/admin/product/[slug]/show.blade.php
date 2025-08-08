<?php
use App\Models\Product;
use function Laravel\Folio\name;
use function Livewire\Volt\{computed, state};
name('admin.product.show');
state(['product' => fn () => Product::with(['brand', 'category', 'attributes'])->with(['productBranches' => function($query) {
        $query->with(['branch', 'color', 'storage']);
    }])
    ->where('slug' ,request()->route('slug'))->first()
])->locked();
?>
<x-app>
    <x-toolbar 
        title="Produk"
        :breadcrumbs="[
            ['icon' => 'ki-outline ki-home', 'url' => route('admin.dashboard')],
            ['text' => 'Produk', 'url' => route('admin.product')],
            ['text' => 'Lihat Produk', 'active' => true]
        ]"
        toolbar-class="py-3 py-lg-6"
    />
    @volt
    <div id="kt_app_content" class="app-content flex-column-fluid py-10">
        @php
            $product = $this->product;
            
            $colorAttributes = $product->attributes->where('title', 'color');
            $storageAttributes = $product->attributes->where('title', 'storage');
        @endphp
        <div class="row g-5">
            <!-- Left Column - Product Info -->
            <div class="col-lg-8">
                <!-- Product Card -->
                <div class="card mb-5">
                    <div class="card-body">
                        <div class="d-flex flex-column flex-md-row align-items-md-center gap-5">
                            <!-- Product Image -->
                            <div class="symbol symbol-200px symbol-circle mb-5 mb-md-0">
                                <img src="{{ asset('storage/' . $product->thumbnail) }}" alt="{{ $product->name }}" class="w-100">
                            </div>
                            
                            <!-- Product Details -->
                            <div class="flex-grow-1">
                                <div class="d-flex flex-column">
                                    <h2 class="fw-bold text-gray-800 mb-1">{{ $product->name }}</h2>
                                    <span class="text-muted fs-6 mb-2">{{ $product->code }}</span>
                                    
                                    <div class="d-flex align-items-center gap-2 mb-3">
                                        <span class="badge badge-light-primary">{{ $product->brand->name ?? 'No Brand' }}</span>
                                        <span class="badge badge-light-info">{{ $product->category->name ?? 'No Category' }}</span>
                                    </div>
                                    
                                    <div class="mb-5">
                                        <h4 class="fw-bold text-gray-800 mb-3">Deskripsi</h4>
                                        <div class="text-gray-600 fs-6">
                                            {!! $product->description_rent ?? '<span class="text-muted">No description available</span>' !!}
                                        </div>
                                    </div>
                                    
                                    <div class="d-flex flex-wrap gap-3">
                                        <div class="d-flex flex-column">
                                            <span class="text-muted fs-7">Created At</span>
                                            <span class="fw-bold">{{ $product->created_at->format('d M Y') }}</span>
                                        </div>
                                        <div class="d-flex flex-column">
                                            <span class="text-muted fs-7">Updated At</span>
                                            <span class="fw-bold">{{ $product->updated_at->format('d M Y') }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Attributes Section -->
                <div class="card mb-5">
                    <div class="card-header">
                        <h3 class="card-title fw-bold">Atribut Produk</h3>
                    </div>
                    <div class="card-body">
                        <div class="row g-5">
                            <!-- Colors -->
                            @if($colorAttributes->count())
                            <div class="col-md-6">
                                <h4 class="fw-bold text-gray-800 mb-3">Warna yang Tersedia</h4>
                                <div class="d-flex flex-wrap gap-3">
                                    @foreach($colorAttributes as $attribute)
                                        @php
                                        $colorValue = match($attribute->value) {
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
                                            default => $attribute->value
                                        };
                                        @endphp
                                        <div class="symbol symbol-50px symbol-circle">
                                            <div class="symbol-label" style="background-color: {{ $colorValue }}"></div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                            @endif

                            <!-- Storage -->
                            @if($storageAttributes->count())
                            <div class="col-md-6">
                                <h4 class="fw-bold text-gray-800 mb-3">Penyimpanan yang Tersedia</h4>
                                <div class="d-flex flex-wrap gap-3">
                                    @foreach($storageAttributes as $attribute)
                                        <span class="badge badge-light-dark fs-6 py-3 px-4">
                                            {{ $attribute->value }}
                                        </span>
                                    @endforeach
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column - Inventory & Actions -->
            <div class="col-lg-4">
                <!-- Inventory by Branch -->
                <div class="card mb-5">
                    <div class="card-header">
                        <h3 class="card-title fw-bold">Inventaris berdasarkan Cabang</h3>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table align-middle table-row-dashed fs-6 gy-3">
                                <thead>
                                    <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
                                        <th class="min-w-100px">Cabang</th>
                                        <th class="min-w-100px">Warna</th>
                                        <th class="min-w-100px">Penyimpanan</th>
                                        <th class="min-w-100px text-end">Harga Sewa</th>
                                    </tr>
                                </thead>
                                <tbody class="text-gray-600 fw-semibold">
                                    @forelse($product->productBranches as $inventory)
                                        <tr>
                                            <td>
                                                <span class="text-gray-800 fw-bold">{{ $inventory->branch->name }}</span>
                                            </td>
                                            <td>
                                                @if($inventory->color)
                                                    <span class="badge" style="background-color: {{ $inventory->color->value }}">
                                                        {{ $inventory->color->value }}
                                                    </span>
                                                @else
                                                    <span class="text-muted">N/A</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($inventory->storage)
                                                    <span class="badge badge-light-dark">
                                                        {{ $inventory->storage->value }}
                                                    </span>
                                                @else
                                                    <span class="text-muted">N/A</span>
                                                @endif
                                            </td>
                                            <td class="text-end">
                                                {{ number_format($inventory->rent_price) }}
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center py-10">
                                                <div class="d-flex flex-column align-items-center">
                                                    <i class="ki-outline ki-inbox fs-4x text-muted mb-4"></i>
                                                    <span class="text-gray-600 fs-6">No inventory available</span>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title fw-bold">Tindakan Cepat</h3>
                    </div>
                    <div class="card-body">
                        <div class="d-flex flex-column gap-3">
                            <a wire:navigate href="{{ route('admin.product.edit', ['slug' => $product]) }}" class="btn btn-light-warning">
                                <i class="ki-outline ki-notepad-edit fs-2 me-2"></i> Edit Detail Produk
                            </a>
                            <a wire:navigate href="{{ route('admin.product.inventory', ['slug' => $product]) }}" class="btn btn-light-primary">
                                <i class="ki-outline ki-basket fs-2 me-2"></i> Kelola Inventaris
                            </a>
                            <a href="#" class="btn btn-light-info">
                                <i class="ki-outline ki-image fs-2 me-2"></i> Perbarui Gambar Produk
                            </a>
                            <button 
                                type="button" 
                                class="btn btn-light-danger"
                                data-bs-toggle="modal" 
                                data-bs-target="#deleteModal"
                            >
                                <i class="ki-outline ki-trash fs-2 me-2"></i> Hapus Produk
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endvolt
</x-app>