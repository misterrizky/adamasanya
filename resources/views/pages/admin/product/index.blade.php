<?php
use App\Models\Product;
use App\Models\Master\Brand;
use App\Models\Master\Category;
use function Laravel\Folio\name;
use function Livewire\Volt\{computed, state, usesPagination};

usesPagination(theme: 'bootstrap');
name('admin.product');
state([
    'search' => '',
    'brand_id' => null,
    'category_id' => null
])->url();
state(['sortColumn' => '', 'sortDirection' => 'ASC']);

$sort = function($columnName) {
    if ($this->sortColumn === $columnName) {
        $this->sortDirection = $this->sortDirection === 'ASC' ? 'DESC' : 'ASC';
    } else {
        $this->sortColumn = $columnName;
        $this->sortDirection = 'ASC';
    }
};

$totalProducts = computed(function() {
    return Product::query()
        ->with(['brand', 'category'])
        ->when($this->search, function($query) {
            $query->where('name', 'like', '%'.$this->search.'%')
                  ->orWhere('slug', 'like', '%'.$this->search.'%')
                  ->orWhere('code', 'like', '%'.$this->search.'%');
        })
        ->when($this->brand_id, function($query) {
            $query->where('brand_id', $this->brand_id);
        })
        ->when($this->category_id, function($query) {
            $query->where('category_id', $this->category_id);
        })
        ->count();
});

$products = computed(function() {
    return Product::query()
        ->with(['brand', 'category'])
        ->when($this->search, function($query) {
            $query->where(function($q) {
                $q->where('name', 'like', '%'.$this->search.'%')
                  ->orWhere('slug', 'like', '%'.$this->search.'%')
                  ->orWhere('code', 'like', '%'.$this->search.'%');
            });
        })
        ->when($this->brand_id, function($query) {
            $query->where('brand_id', $this->brand_id);
        })
        ->when($this->category_id, function($query) {
            $query->where('category_id', $this->category_id);
        })
        ->when($this->sortColumn, function($query) {
            $query->orderBy($this->sortColumn, $this->sortDirection);
        }, function($query) {
            $query->orderBy('id', 'DESC');
        })
        ->paginate(10);
});

$brands = computed(fn() => Brand::where('st', 'a')->orderBy('name')->get());
$categories = computed(fn() => Category::where('st', 'a')->orderBy('name')->get());
?>
<x-app>
    @volt
    <div id="kt_app_content" class="app-content flex-column-fluid py-10">
        <div class="container-fluid p-0 mb-7">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-4 gap-md-6 py-3">
                <!-- Title Section -->
                <div class="d-flex flex-column">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-1">
                        <div class="d-flex flex-wrap align-items-center gap-2">
                            <h2 class="h3 fw-bold mb-0">
                                <span class="text-primary">{{ $this->totalProducts }}</span> Produk Ditemukan
                                @if($this->search)
                                    <span class="text-muted fs-6 ms-2">
                                        untuk pencarian "<span class="fw-semibold">{{ $this->search }}</span>"
                                    </span>
                                @endif
                            </h2>
                        </div>
                        
                        <!-- Floating Button -->
                        <a href="{{ route('admin.product.create') }}" wire:navigate
                        class="btn btn-light-primary d-flex d-xl-none align-items-center gap-2 rounded-1 hover-elevate-up"
                        aria-label="Add new brand">
                            <i class="ki-outline ki-plus fs-5"></i>
                            <span class="d-none d-md-inline">Tambah</span>
                        </a>
                    </div>
                    <p class="text-muted mb-0">Kelola produk Anda dengan mudah</p>
                </div>

                <!-- Controls Section -->
                <div class="d-flex flex-column flex-sm-row gap-3 align-items-end">
                    <div class="flex-grow-1 flex-sm-grow-0 w-100 w-sm-200px">
                        <input 
                            type="search" 
                            wire:model.live="search" 
                            class="form-control ps-5" 
                            placeholder="Cari produk..." 
                            aria-label="Search categories"
                        >
                    </div>
                    <!-- Status Filter -->
                    <div class="flex-grow-1 flex-sm-grow-0 w-100 w-sm-200px">
                        <select 
                            class="form-select form-select-solid" 
                            wire:model.live="brand_id"
                        >
                            <option value="">Semua Merek</option>
                            @foreach($this->brands as $brand)
                                <option value="{{ $brand->id }}">{{ $brand->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Sort By -->
                    <div class="flex-grow-1 flex-sm-grow-0 w-100 w-sm-200px">
                        <select 
                            class="form-select form-select-solid" 
                            wire:model.live="category_id"
                        >
                            <option value="">Semua Kategori</option>
                            @foreach($this->categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Add Button - Now with consistent width -->
                    <a href="{{ route('admin.product.create') }}" wire:navigate
                    class="btn btn-light-primary w-100 d-flex align-items-center justify-content-center px-3 rounded-1 hover-elevate-up"
                    aria-label="Add new brand">
                        <i class="ki-outline ki-plus fs-5 me-2"></i>
                        <span class="text-nowrap">Tambah</span>
                    </a>
                </div>
            </div>
        </div>
        <!-- Stats Cards -->
        <div class="row g-5 mb-5">
            <div class="col-sm-6 col-xl-3">
                <div class="card bg-light-primary card-flush h-md-100">
                    <div class="card-body">
                        <div class="d-flex flex-column">
                            <div class="d-flex align-items-center mb-1">
                                <span class="fs-2hx fw-bold text-primary me-2">{{ Product::count() }}</span>
                            </div>
                            <span class="text-gray-600">Total Products</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-xl-3">
                <div class="card bg-light-info card-flush h-md-100">
                    <div class="card-body">
                        <div class="d-flex flex-column">
                            <div class="d-flex align-items-center mb-1">
                                <span class="fs-2hx fw-bold text-info me-2">{{ $this->totalProducts }}</span>
                            </div>
                            <span class="text-gray-600">Filtered Results</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Results Count -->
        <div class="d-flex flex-wrap flex-stack mb-5">
            <div class="d-flex flex-wrap align-items-center my-1">
                <h3 class="fw-bold me-5 my-1">
                    {{ $this->totalProducts > 0 ? number_format($this->totalProducts) . ' Products Found' : 'No products found' }}
                </h3>
            </div>
            <div class="d-flex flex-wrap my-1">
                <button type="button" class="btn btn-sm btn-icon btn-light-primary me-3" data-bs-toggle="modal" data-bs-target="#exportModal">
                    <i class="ki-outline ki-exit-up fs-2"></i>
                </button>
            </div>
        </div>

        <!-- Products Table -->
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table align-middle table-row-dashed fs-6 table-row-gray-300 gy-7" id="products_table">
                        <thead>
                            <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
                                <th class="min-w-150px cursor-pointer" wire:click="sort('name')">
                                    <div class="d-flex align-items-center gap-2">
                                        <span>Product</span>
                                        @if($sortColumn === 'name')
                                            <i class="ki-outline ki-arrow-{{ $sortDirection === 'ASC' ? 'up' : 'down' }} fs-3 text-primary"></i>
                                        @endif
                                    </div>
                                </th>
                                <th class="min-w-100px">Brand</th>
                                <th class="min-w-100px">Category</th>
                                <th class="min-w-100px text-center">Created At</th>
                                <th class="min-w-100px text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="text-gray-600 fw-semibold">
                            @forelse ($this->products as $product)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="symbol symbol-50px symbol-circle me-3">
                                                <img src="{{ asset('storage/' . $product->thumbnail) }}" alt="{{ $product->name }}" class="w-100">
                                            </div>
                                            <div>
                                                <span class="text-gray-800 fw-bold d-block">{{ $product->name }}</span>
                                                <small class="text-muted">{{ $product->slug }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge badge-light-primary">{{ $product->brand->name ?? '-' }}</span>
                                    </td>
                                    <td>
                                        <span class="badge badge-light-info">{{ $product->category->name ?? '-' }}</span>
                                    </td>
                                    <td class="text-center">
                                        {{ $product->created_at->format('d M Y') }}
                                    </td>
                                    <td class="text-end">
                                        <div class="d-flex justify-content-end gap-2">
                                            <a 
                                                wire:navigate
                                                href="{{ route('admin.product.show', ['slug' => $product]) }}" 
                                                class="btn btn-icon btn-sm btn-light-primary"
                                                data-bs-toggle="tooltip"
                                                title="View"
                                            >
                                                <i class="ki-outline ki-eye fs-2"></i>
                                            </a>
                                            <a
                                                wire:navigate 
                                                href="{{ route('admin.product.edit', ['slug' => $product]) }}" 
                                                class="btn btn-icon btn-sm btn-light-warning"
                                                data-bs-toggle="tooltip"
                                                title="Edit"
                                            >
                                                <i class="ki-outline ki-notepad-edit fs-2"></i>
                                            </a>
                                            <button 
                                                type="button" 
                                                class="btn btn-icon btn-sm btn-light-danger"
                                                data-bs-toggle="modal" 
                                                data-bs-target="#deleteModal"
                                                wire:click="$dispatch('setDeleteId', {id: {{ $product->id }}, name: '{{ $product->name }}'})"
                                                data-bs-toggle="tooltip"
                                                title="Delete"
                                            >
                                                <i class="ki-outline ki-trash fs-2"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center py-10">
                                        <div class="d-flex flex-column align-items-center">
                                            <i class="ki-outline ki-search-list fs-4x text-muted mb-4"></i>
                                            <span class="text-gray-600 fs-6">No products found matching your criteria</span>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                @if($this->products->hasPages())
                    <div class="card-footer">
                        {{ $this->products->links() }}
                    </div>
                @endif
            </div>
        </div>

        <!-- Export Modal -->
        <div class="modal fade" tabindex="-1" id="exportModal">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h3 class="modal-title">Export Products</h3>
                        <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal" aria-label="Close">
                            <i class="ki-outline ki-cross fs-1"></i>
                        </div>
                    </div>
                    <div class="modal-body">
                        <div class="mb-5">
                            <label class="form-label">Select Format</label>
                            <select class="form-select form-select-solid" data-control="select2" data-hide-search="true">
                                <option value="csv">CSV</option>
                                <option value="excel">Excel</option>
                                <option value="pdf">PDF</option>
                            </select>
                        </div>
                        <div class="mb-5">
                            <label class="form-label">Select Columns</label>
                            <select class="form-select form-select-solid" multiple="multiple" data-control="select2" data-close-on-select="false">
                                <option value="name" selected>Name</option>
                                <option value="code" selected>Code</option>
                                <option value="brand" selected>Brand</option>
                                <option value="category" selected>Category</option>-
                                <option value="created_at" selected>Created At</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-primary">Export</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Delete Confirmation Modal -->
        <div class="modal fade" tabindex="-1" id="deleteModal" wire:ignore.self>
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h3 class="modal-title">Delete Product</h3>
                        <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal" aria-label="Close">
                            <i class="ki-outline ki-cross fs-1"></i>
                        </div>
                    </div>
                    <div class="modal-body">
                        <p>Are you sure you want to delete <strong id="deleteItemName"></strong>? This action cannot be undone.</p>
                        <p class="text-danger"><strong>Warning:</strong> Deleting this product will also remove all associated variants, attributes, and inventory data.</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-danger" wire:click="deleteProduct" data-bs-dismiss="modal">Delete</button>
                    </div>
                </div>
            </div>
        </div>

        @script
        <script>
            // Handle delete modal
            document.addEventListener('livewire:initialized', () => {
                Livewire.on('setDeleteId', (data) => {
                    document.getElementById('deleteItemName').textContent = data.name;
                    window.deleteId = data.id;
                });
            });
            
            // Initialize tooltips
            document.addEventListener('DOMContentLoaded', function() {
                const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
                tooltipTriggerList.map(function (tooltipTriggerEl) {
                    return new bootstrap.Tooltip(tooltipTriggerEl);
                });
            });
        </script>
        @endscript
    </div>
    @endvolt
</x-app>