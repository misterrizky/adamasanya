<?php
use App\Models\Product;
use App\Models\Master\Branch;
use App\Models\ProductBranch;
use App\Models\Master\Attribute;
use function Laravel\Folio\name;
use function Livewire\Volt\{computed, state, usesPagination};
usesPagination(theme: 'bootstrap');

name('admin.product.inventory');
state(['product' => fn () => Product::with(['brand', 'category'])->where('slug' ,request()->route('slug'))->first()])->locked();
state([
    'product_id' => request()->route('product'),
    'branch_id' => null,
    'color_id' => null,
    'storage_id' => null,
    'search' => ''
])->url();
$branches = computed(fn() => Branch::where('st', 'a')->orderBy('name')->get());
$colors = computed(fn() => Attribute::where('attributable_id', $this->product->id)
                    ->where('attributable', 'App\Models\Product')
                    ->where('title', 'color')
                    ->get());
$storages = computed(fn() => Attribute::where('attributable_id', $this->product->id)
                    ->where('attributable', 'App\Models\Product')
                    ->where('title', 'storage')
                    ->get());

$inventoryItems = computed(function() {
    return ProductBranch::with(['branch', 'color', 'storage'])
        ->where('product_id', $this->product->id)
        ->when($this->branch_id, fn($q) => $q->where('branch_id', $this->branch_id))
        ->when($this->color_id, fn($q) => $q->where('color_id', $this->color_id))
        ->when($this->storage_id, fn($q) => $q->where('storage_id', $this->storage_id))
        ->when($this->search, fn($q) => $q->whereHas('branch', fn($q) => 
            $q->where('name', 'like', '%'.$this->search.'%')
        ))
        ->orderBy('branch_id')
        ->paginate(10);
});
?>
<x-app>
    <x-toolbar 
        title="Manage Inventory"
        :breadcrumbs="[
            ['icon' => 'ki-outline ki-home', 'url' => route('admin.dashboard')],
            ['text' => 'Produk', 'url' => route('admin.product')],
            ['text' => $slug, 'url' => route('admin.product.show', ['slug' => $slug])],
            ['text' => 'Inventaris', 'active' => true]
        ]"
        toolbar-class="py-3 py-lg-6"
        :buttons="[
            [
            'text' => 'Tambah Inventaris',
            'attributes' => [
                'data-bs-toggle' => 'modal',
                'data-bs-target' => '#addInventoryModal'
            ]]
        ]"
    />
    @volt
    <div id="kt_app_content" class="app-content flex-column-fluid py-10">
        <!-- Filters Card -->
        <div class="card mb-5">
            <div class="card-body">
                <div class="d-flex flex-column flex-md-row align-items-md-center gap-5">
                    <!-- Branch Filter -->
                    <div class="flex-grow-1">
                        <select 
                            class="form-select form-select-solid" 
                            wire:model.live="branch_id"
                        >
                            <option value="">All Branches</option>
                            @foreach($this->branches as $branch)
                                <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <!-- Color Filter -->
                    <div class="w-200px">
                        <select 
                            class="form-select form-select-solid" 
                            wire:model.live="color_id"
                        >
                            <option value="">All Colors</option>
                            @foreach($this->colors as $color)
                                <option value="{{ $color->id }}">{{ $color->value }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <!-- Storage Filter -->
                    <div class="w-200px">
                        <select 
                            class="form-select form-select-solid" 
                            wire:model.live="storage_id"
                        >
                            <option value="">All Storage</option>
                            @foreach($this->storages as $storage)
                                <option value="{{ $storage->id }}">{{ $storage->value }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <!-- Search Input -->
                    <div class="w-250px">
                        <div class="d-flex align-items-center position-relative">
                            <i class="ki-outline ki-magnifier fs-3 position-absolute ms-3"></i>
                            <input 
                                type="text" 
                                class="form-control form-control-solid ps-10" 
                                placeholder="Search branches..." 
                                wire:model.live.debounce.300ms="search"
                            >
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Inventory Table -->
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table align-middle table-row-dashed fs-6 gy-5" id="inventory_table">
                        <thead>
                            <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
                                <th class="min-w-150px">Branch</th>
                                <th class="min-w-100px">Color</th>
                                <th class="min-w-100px">Storage</th>
                                <th class="min-w-100px text-end">Rent Price</th>
                                <th class="min-w-100px text-end">Sale Price</th>
                                <th class="min-w-100px text-center">Status</th>
                                <th class="min-w-100px text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="text-gray-600 fw-semibold">
                            @forelse ($this->inventoryItems as $item)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="symbol symbol-50px symbol-circle me-3">
                                                <img src="{{ asset('storage/' . $item->branch->thumbnail) }}" alt="{{ $item->branch->name }}" class="w-100">
                                            </div>
                                            <div>
                                                <span class="text-gray-800 fw-bold d-block">{{ $item->branch->name }}</span>
                                                <small class="text-muted">{{ $item->branch->category }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        @if($item->color)
                                            <span class="badge" style="background-color: {{ $item->color->value }}">
                                                {{ $item->color->value }}
                                            </span>
                                        @else
                                            <span class="text-muted">N/A</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($item->storage)
                                            <span class="badge badge-light-dark">
                                                {{ $item->storage->value }}
                                            </span>
                                        @else
                                            <span class="text-muted">N/A</span>
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        {{ number_format($item->rent_price) }}
                                    </td>
                                    <td class="text-end">
                                        {{ $item->sale_price ? number_format($item->sale_price) : '-' }}
                                    </td>
                                    <td class="text-center">
                                        <span class="badge badge-light-{{ $item->is_publish ? 'success' : 'danger' }}">
                                            {{ $item->is_publish ? 'Published' : 'Hidden' }}
                                        </span>
                                    </td>
                                    <td class="text-end">
                                        <div class="d-flex justify-content-end gap-2">
                                            <button 
                                                type="button" 
                                                class="btn btn-icon btn-sm btn-light-warning"
                                                data-bs-toggle="modal" 
                                                data-bs-target="#editInventoryModal"
                                                wire:click="$dispatch('setEditId', {id: {{ $item->id }}})"
                                                data-bs-toggle="tooltip"
                                                title="Edit"
                                            >
                                                <i class="ki-outline ki-notepad-edit fs-2"></i>
                                            </button>
                                            <button 
                                                type="button" 
                                                class="btn btn-icon btn-sm btn-light-danger"
                                                data-bs-toggle="modal" 
                                                data-bs-target="#deleteInventoryModal"
                                                wire:click=""
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
                                    <td colspan="7" class="text-center py-10">
                                        <div class="d-flex flex-column align-items-center">
                                            <i class="ki-outline ki-inbox fs-4x text-muted mb-4"></i>
                                            <span class="text-gray-600 fs-6">No inventory items found matching your criteria</span>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                @if($this->inventoryItems->hasPages())
                    <div class="card-footer">
                        {{ $this->inventoryItems->links() }}
                    </div>
                @endif
            </div>
        </div>

        <!-- Add Inventory Modal -->
        <div class="modal fade" tabindex="-1" id="addInventoryModal" wire:ignore.self>
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h3 class="modal-title">Add Inventory</h3>
                        <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal" aria-label="Close">
                            <i class="ki-outline ki-cross fs-1"></i>
                        </div>
                    </div>
                    <form wire:submit="addInventory">
                        <div class="modal-body">
                            <div class="mb-5">
                                <label class="form-label required">Branch</label>
                                <select 
                                    class="form-select form-select-solid" 
                                    wire:model="newInventory.branch_id"
                                    required
                                >
                                    <option value="">Select Branch</option>
                                    @foreach($this->branches as $branch)
                                        <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <div class="row g-5 mb-5">
                                <div class="col-md-6">
                                    <label class="form-label">Color</label>
                                    <select 
                                        class="form-select form-select-solid" 
                                        wire:model="newInventory.color_id"
                                    >
                                        <option value="">Select Color</option>
                                        @foreach($this->colors as $color)
                                            <option value="{{ $color->id }}">{{ $color->value }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Storage</label>
                                    <select 
                                        class="form-select form-select-solid" 
                                        wire:model="newInventory.storage_id"
                                    >
                                        <option value="">Select Storage</option>
                                        @foreach($this->storages as $storage)
                                            <option value="{{ $storage->id }}">{{ $storage->value }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            
                            <div class="row g-5">
                                <div class="col-md-6">
                                    <label class="form-label required">Rent Price</label>
                                    <input 
                                        type="number" 
                                        class="form-control form-control-solid" 
                                        wire:model="newInventory.rent_price"
                                        required
                                    >
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Sale Price</label>
                                    <input 
                                        type="number" 
                                        class="form-control form-control-solid" 
                                        wire:model="newInventory.sale_price"
                                    >
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary">Add Inventory</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Edit Inventory Modal -->
        <div class="modal fade" tabindex="-1" id="editInventoryModal" wire:ignore.self>
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h3 class="modal-title">Edit Inventory</h3>
                        <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal" aria-label="Close">
                            <i class="ki-outline ki-cross fs-1"></i>
                        </div>
                    </div>
                    <form wire:submit="updateInventory">
                        <div class="modal-body">
                            <div class="mb-5">
                                <label class="form-label">Branch</label>
                                <input 
                                    type="text" 
                                    class="form-control form-control-solid" 
                                    value="{{ $editInventory->branch->name ?? '' }}"
                                    readonly
                                >
                            </div>
                            
                            <div class="row g-5 mb-5">
                                <div class="col-md-6">
                                    <label class="form-label">Color</label>
                                    <select 
                                        class="form-select form-select-solid" 
                                        wire:model="editInventory.color_id"
                                    >
                                        <option value="">Select Color</option>
                                        @foreach($this->colors as $color)
                                            <option value="{{ $color->id }}">{{ $color->value }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Storage</label>
                                    <select 
                                        class="form-select form-select-solid" 
                                        wire:model="editInventory.storage_id"
                                    >
                                        <option value="">Select Storage</option>
                                        @foreach($this->storages as $storage)
                                            <option value="{{ $storage->id }}">{{ $storage->value }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            
                            <div class="row g-5">
                                <div class="col-md-6">
                                    <label class="form-label required">Rent Price</label>
                                    <input 
                                        type="number" 
                                        class="form-control form-control-solid" 
                                        wire:model="editInventory.rent_price"
                                        required
                                    >
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Sale Price</label>
                                    <input 
                                        type="number" 
                                        class="form-control form-control-solid" 
                                        wire:model="editInventory.sale_price"
                                    >
                                </div>
                            </div>
                            
                            <div class="mt-5">
                                <label class="form-check form-switch form-check-custom form-check-solid">
                                    <input 
                                        class="form-check-input" 
                                        type="checkbox" 
                                        wire:model="editInventory.is_publish"
                                    >
                                    <span class="form-check-label fw-semibold text-muted">
                                        Published (Visible to customers)
                                    </span>
                                </label>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary">Update Inventory</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        @script
        <script>
            // Handle modals
            document.addEventListener('livewire:initialized', () => {
                Livewire.on('setEditId', (data) => {
                    window.editId = data.id;
                });
                
                Livewire.on('setDeleteId', (data) => {
                    document.getElementById('deleteItemName').textContent = data.name;
                    window.deleteId = data.id;
                });
                
                // Refresh inventory after modals close
                const modals = ['addInventoryModal', 'editInventoryModal', 'deleteInventoryModal'];
                modals.forEach(modalId => {
                    document.getElementById(modalId).addEventListener('hidden.bs.modal', () => {
                        Livewire.dispatch('refreshInventory');
                    });
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