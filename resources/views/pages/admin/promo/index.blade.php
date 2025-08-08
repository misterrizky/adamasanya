<?php
use App\Models\Promo;
use function Laravel\Folio\name;
use function Livewire\Volt\{computed, mount, state, usesPagination};

usesPagination(theme: 'bootstrap');
name('admin.promo');
state(['search' => '', 'is_active' => ''])->url();
state(['sortColumn' => '', 'sortDirection' => 'ASC']);

$sort = function($columnName) {
    if ($this->sortColumn === $columnName) {
        $this->sortDirection = $this->sortDirection === 'ASC' ? 'DESC' : 'ASC';
    } else {
        $this->sortColumn = $columnName;
        $this->sortDirection = 'ASC';
    }
};

$totalPromo = computed(function() {
    return Promo::query()
        ->when($this->search, function($query) {
            $query->where('name', 'like', '%'.$this->search.'%')
                  ->orWhere('code', 'like', '%'.$this->search.'%');
        })
        ->when($this->is_active !== '', function($query) {
            $query->where('is_active', $this->is_active);
        })
        ->count();
});

$promos = computed(function() {
    return Promo::query()
        ->when($this->search, function($query) {
            $query->where(function($q) {
                $q->where('name', 'like', '%'.$this->search.'%')
                  ->orWhere('code', 'like', '%'.$this->search.'%');
            });
        })
        ->when($this->is_active !== '', function($query) {
            $query->where('is_active', $this->is_active);
        })
        ->when($this->sortColumn, function($query) {
            $query->orderBy($this->sortColumn, $this->sortDirection);
        }, function($query) {
            $query->orderBy('id', 'DESC');
        })
        ->with(['branches', 'categories', 'products'])
        ->paginate(10);
});

$hapus = function($id) {
    $promo = Promo::find($id);
    $promo->delete();
    return $this->redirect(route('admin.promo'), navigate: true);
};
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
                                <span class="text-primary">{{ $this->totalPromo }}</span> Promo Ditemukan
                                @if($this->search)
                                    <span class="text-muted fs-6 ms-2">
                                        untuk pencarian "<span class="fw-semibold">{{ $this->search }}</span>"
                                    </span>
                                @endif
                            </h2>
                        </div>
                        <!-- Floating Button -->
                        <a href="{{ route('admin.promo.create') }}" wire:navigate
                           class="btn btn-light-primary d-flex d-xl-none align-items-center gap-2 rounded-1 hover-elevate-up"
                           aria-label="Add new promo">
                            <i class="ki-outline ki-plus fs-5"></i>
                            <span class="d-none d-md-inline">Tambah</span>
                        </a>
                    </div>
                    <p class="text-muted mb-0">Kelola promo untuk produk dan penyewaan</p>
                </div>

                <!-- Controls Section -->
                <div class="d-flex flex-column flex-sm-row gap-3 align-items-end">
                    <div class="flex-grow-1 flex-sm-grow-0 w-100 w-sm-200px">
                        <input 
                            type="search" 
                            wire:model.live="search" 
                            class="form-control ps-5" 
                            placeholder="Cari promo..." 
                            aria-label="Search promos"
                        >
                    </div>
                    <!-- Status Filter -->
                    <div class="flex-grow-1 flex-sm-grow-0 w-100 w-sm-200px">
                        <select class="form-select" wire:model.live="is_active" aria-label="Filter by status">
                            <option value="">Semua Status</option>
                            <option value="1">Aktif</option>
                            <option value="0">Nonaktif</option>
                        </select>
                    </div>
                    <!-- Sort By -->
                    <div class="flex-grow-1 flex-sm-grow-0 w-100 w-sm-200px">
                        <select class="form-select" wire:model.live="sortColumn" aria-label="Sort by">
                            <option value="">Urutkan</option>
                            <option value="name">Nama (A-Z)</option>
                            <option value="created_at">Terbaru</option>
                            <option value="start_date">Tanggal Mulai</option>
                            <option value="end_date">Tanggal Berakhir</option>
                        </select>
                    </div>
                    <!-- Add Button -->
                    <a href="{{ route('admin.promo.create') }}" wire:navigate
                       class="btn btn-light-primary w-100 d-flex align-items-center justify-content-center px-3 rounded-1 hover-elevate-up"
                       aria-label="Add new promo">
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
                                <span class="fs-2hx fw-bold text-primary me-2">{{ Promo::count() }}</span>
                            </div>
                            <span class="text-gray-600">Total Promo</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-xl-3">
                <div class="card bg-light-success card-flush h-md-100">
                    <div class="card-body">
                        <div class="d-flex flex-column">
                            <div class="d-flex align-items-center mb-1">
                                <span class="fs-2hx fw-bold text-success me-2">{{ Promo::where('is_active', 1)->count() }}</span>
                            </div>
                            <span class="text-gray-600">Promo Aktif</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-xl-3">
                <div class="card bg-light-danger card-flush h-md-100">
                    <div class="card-body">
                        <div class="d-flex flex-column">
                            <div class="d-flex align-items-center mb-1">
                                <span class="fs-2hx fw-bold text-danger me-2">{{ Promo::where('is_active', 0)->count() }}</span>
                            </div>
                            <span class="text-gray-600">Promo Nonaktif</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-xl-3">
                <div class="card bg-light-info card-flush h-md-100">
                    <div class="card-body">
                        <div class="d-flex flex-column">
                            <div class="d-flex align-items-center mb-1">
                                <span class="fs-2hx fw-bold text-info me-2">{{ $this->totalPromo }}</span>
                            </div>
                            <span class="text-gray-600">Hasil Filter</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Promo Table -->
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-row-dashed table-row-gray-300 gy-7" id="promo_table">
                        <thead>
                            <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
                                <th class="min-w-150px cursor-pointer" wire:click="sort('name')">
                                    <div class="d-flex align-items-center gap-2">
                                        <span>Nama Promo</span>
                                        @if($sortColumn === 'name')
                                            <i class="ki-outline ki-arrow-{{ $sortDirection === 'ASC' ? 'up' : 'down' }} fs-3 text-primary"></i>
                                        @endif
                                    </div>
                                </th>
                                <th class="min-w-100px">Kode</th>
                                <th class="min-w-100px text-center">Tipe</th>
                                <th class="min-w-100px text-center">Nilai</th>
                                <th class="min-w-100px text-center">Cakupan</th>
                                <th class="min-w-100px text-center">Berlaku Untuk</th>
                                <th class="min-w-100px text-center">Tanggal Mulai</th>
                                <th class="min-w-100px text-center">Tanggal Berakhir</th>
                                <th class="min-w-100px text-center">Status</th>
                                <th class="min-w-100px text-end">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="text-gray-600 fw-semibold">
                            @forelse ($this->promos as $promo)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="symbol symbol-50px symbol-circle me-3">
                                                <img src="{{ asset('storage/' . $promo->thumbnail) }}" alt="{{ $promo->name }}" class="w-100">
                                            </div>
                                            <div>
                                                <span class="text-gray-800 fw-bold d-block">{{ $promo->name }}</span>
                                                <small class="text-muted">{{ $promo->description }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ $promo->code }}</td>
                                    <td class="text-center">
                                        {{ match ($promo->type) {
                                            'percentage' => 'Persentase',
                                            'fixed_amount' => 'Nominal Tetap',
                                            'buy_x_get_y' => 'Beli X Dapat Y',
                                            'free_shipping' => 'Gratis Ongkir',
                                            default => $promo->type
                                        } }}
                                    </td>
                                    <td class="text-center">
                                        @if($promo->type === 'percentage')
                                            {{ $promo->value }}%
                                        @elseif($promo->type === 'fixed_amount')
                                            Rp {{ number_format($promo->value, 0, ',', '.') }}
                                        @elseif($promo->type === 'buy_x_get_y')
                                            Beli {{ $promo->buy_quantity }} Dapat {{ $promo->get_quantity }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        {{ match ($promo->scope) {
                                            'all' => 'Semua',
                                            'products' => 'Produk (' . $promo->products->count() . ')',
                                            'categories' => 'Kategori (' . $promo->categories->count() . ')',
                                            'branches' => 'Cabang (' . $promo->branches->count() . ')',
                                            default => $promo->scope
                                        } }}
                                    </td>
                                    <td class="text-center">
                                        {{ match ($promo->applicable_for) {
                                            'all' => 'Semua',
                                            'rent' => 'Sewa',
                                            'sale' => 'Jual',
                                            default => $promo->applicable_for
                                        } }}
                                    </td>
                                    <td class="text-center">{{ $promo->start_date->format('d M Y H:i') }}</td>
                                    <td class="text-center">{{ $promo->end_date->format('d M Y H:i') }}</td>
                                    <td class="text-center">
                                        <span class="badge badge-light-{{ $promo->is_active ? 'success' : 'danger' }}">
                                            {{ $promo->is_active ? 'Aktif' : 'Nonaktif' }}
                                        </span>
                                    </td>
                                    <td class="text-end">
                                        <div class="d-flex justify-content-end gap-2">
                                            <a 
                                                wire:navigate
                                                href="{{ route('admin.promo.edit', ['slug' => $promo->slug]) }}" 
                                                class="btn btn-icon btn-sm btn-light-warning"
                                                data-bs-toggle="tooltip"
                                                title="Edit"
                                            >
                                                <i class="ki-outline ki-notepad-edit fs-2"></i>
                                            </a>
                                            <button 
                                                onclick="hapus({{ $promo->id }});"
                                                type="button" 
                                                class="btn btn-icon btn-sm btn-light-danger"
                                                data-bs-toggle="tooltip"
                                                title="Hapus"
                                            >
                                                <i class="ki-outline ki-trash fs-2"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="10" class="text-center py-10">
                                        <div class="d-flex flex-column align-items-center">
                                            <i class="ki-outline ki-search-list fs-4x text-muted mb-4"></i>
                                            <span class="text-gray-600 fs-6">Tidak ada promo yang ditemukan</span>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                @if($this->promos->hasPages())
                    <div class="card-footer">
                        {{ $this->promos->links() }}
                    </div>
                @endif
            </div>
        </div>

        @section('custom_js')
        <script data-navigate-once>
            function hapus(id) {
                Swal.fire({
                    title: 'Hapus Promo?',
                    text: 'Apakah Anda yakin ingin menghapus promo ini? Tindakan ini tidak dapat dibatalkan',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Ya, Hapus',
                    cancelButtonText: 'Batalkan',
                    reverseButtons: true,
                    backdrop: true,
                    allowOutsideClick: false,
                }).then((result) => {
                    if (result.isConfirmed) {
                        Swal.fire({
                            title: 'Menghapus Data...',
                            html: 'Sedang memproses permintaan Anda',
                            allowOutsideClick: false,
                            didOpen: () => {
                                Swal.showLoading();
                                @this.hapus(id).then(() => {
                                    Swal.fire({
                                        title: 'Berhasil!',
                                        text: 'Promo berhasil dihapus',
                                        icon: 'success',
                                        timer: 2000,
                                        timerProgressBar: true
                                    });
                                }).catch(error => {
                                    Swal.fire({
                                        title: 'Gagal!',
                                        html: `Gagal menghapus promo: <br><span class="text-red-500">${error.message}</span>`,
                                        icon: 'error'
                                    });
                                });
                            }
                        });
                    }
                });
            }
        </script>
        @endsection
    </div>
    @endvolt
</x-app>