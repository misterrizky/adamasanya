<?php
use App\Models\Master\Branch;
use App\Models\Transaction\Rent;
use App\Models\Transaction\Sale;
use function Laravel\Folio\name;
use function Livewire\Volt\{computed, mount, state, usesPagination};

usesPagination(theme: 'bootstrap');
name('admin.transaction');
state([
    'search' => '',
    'transaction_type' => '',
    'status' => '',
    'branch_id' => '',
    'sortColumn' => '',
    'sortDirection' => 'ASC',
]);

$branches = computed(fn () => Branch::pluck('name', 'id')->toArray());
$statusOptions = computed(fn () => [
    '' => 'Semua Status',
    'pending' => 'Pending',
    'confirmed' => 'Confirmed',
    'completed' => 'Completed',
    'cancelled' => 'Cancelled',
]);

$sort = function($columnName) {
    if ($this->sortColumn === $columnName) {
        $this->sortDirection = $this->sortDirection === 'ASC' ? 'DESC' : 'ASC';
    } else {
        $this->sortColumn = $columnName;
        $this->sortDirection = 'ASC';
    }
};

$totalTransactions = computed(function() {
    $query = $this->buildTransactionQuery();
    return $query->count();
});

$transactions = computed(function() {
    $query = $this->buildTransactionQuery();
    return $query->when($this->sortColumn, function($query) {
        $query->orderBy($this->sortColumn, $this->sortDirection);
    }, function($query) {
        $query->orderBy('created_at', 'DESC');
    })->paginate(10);
});

$buildTransactionQuery = function() {
    $rentQuery = Rent::query()
        ->select(
            'id',
            \DB::raw('"rent" as type'),
            'code',
            'user_id',
            'branch_id',
            'total_price',
            'status',
            'created_at'
        )
        ->with(['user', 'branch']);

    $saleQuery = Sale::query()
        ->select(
            'id',
            \DB::raw('"sale" as type'),
            'code',
            'user_id',
            'branch_id',
            'total_price',
            'status',
            'created_at'
        )
        ->with(['user', 'branch']);

    $query = $rentQuery->union($saleQuery);

    return \DB::query()->fromSub($query, 'transactions')
        ->when($this->search, function($query) {
            $query->where(function($q) {
                $q->where('code', 'like', '%'.$this->search.'%')
                  ->orWhereHas('user', fn($q) => $q->where('name', 'like', '%'.$this->search.'%'));
            });
        })
        ->when($this->transaction_type, fn($query) => $query->where('type', $this->transaction_type))
        ->when($this->status, fn($query) => $query->where('status', $this->status))
        ->when($this->branch_id, fn($query) => $query->where('branch_id', $this->branch_id))
        ->when(auth()->user()->hasRole('Cabang'), fn($query) => $query->where('branch_id', auth()->user()->branch_id))
        ->when(auth()->user()->hasRole('Owner'), fn($query) => $query->whereIn('branch_id', auth()->user()->branches->pluck('id')));
};

$delete = function($type, $id) {
    if (!auth()->user()->hasAnyRole(['Super Admin', 'Owner'])) {
        $this->dispatch('toast-error', message: 'Anda tidak memiliki izin untuk menghapus transaksi.');
        return;
    }
    if ($type === 'rent') {
        Rent::find($id)->delete();
    } else {
        Sale::find($id)->delete();
    }
    $this->dispatch('toast-success', message: 'Transaksi berhasil dihapus');
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
                                <span class="text-primary">{{ $this->totalTransactions }}</span> Transaksi Ditemukan
                                @if($this->search)
                                    <span class="text-muted fs-6 ms-2">
                                        untuk pencarian "<span class="fw-semibold">{{ $this->search }}</span>"
                                    </span>
                                @endif
                            </h2>
                        </div>
                    </div>
                    <p class="text-muted mb-0">Kelola transaksi sewa dan penjualan</p>
                </div>

                <!-- Controls Section -->
                <div class="d-flex flex-column flex-sm-row gap-3 align-items-end">
                    <div class="flex-grow-1 flex-sm-grow-0 w-100 w-sm-200px">
                        <input 
                            type="search" 
                            wire:model.live="search" 
                            class="form-control ps-5" 
                            placeholder="Cari transaksi..." 
                            aria-label="Search transactions"
                        >
                    </div>
                    <div class="flex-grow-1 flex-sm-grow-0 w-100 w-sm-200px">
                        <select class="form-select" wire:model.live="transaction_type" aria-label="Filter by type">
                            <option value="">Semua Tipe</option>
                            <option value="rent">Sewa</option>
                            <option value="sale">Jual</option>
                        </select>
                    </div>
                    <div class="flex-grow-1 flex-sm-grow-0 w-100 w-sm-200px">
                        <x-form-select 
                            name="status" 
                            class="form-select form-select-solid fw-bold"
                            :options="$this->statusOptions"
                        />
                    </div>
                    @if(auth()->user()->hasAnyRole(['Super Admin', 'Owner']))
                    <div class="flex-grow-1 flex-sm-grow-0 w-100 w-sm-200px">
                        <x-form-select 
                            name="branch_id" 
                            class="form-select form-select-solid fw-bold"
                            :options="['' => 'Semua Cabang'] + $this->branches"
                        />
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="row g-5 mb-5">
            <div class="col-sm-6 col-xl-3">
                <div class="card bg-light-primary card-flush h-md-100">
                    <div class="card-body">
                        <div class="d-flex flex-column">
                            <div class="d-flex align-items-center mb-1">
                                <span class="fs-2hx fw-bold text-primary me-2">{{ Rent::count() + Sale::count() }}</span>
                            </div>
                            <span class="text-gray-600">Total Transaksi</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-xl-3">
                <div class="card bg-light-success card-flush h-md-100">
                    <div class="card-body">
                        <div class="d-flex flex-column">
                            <div class="d-flex align-items-center mb-1">
                                <span class="fs-2hx fw-bold text-success me-2">{{ Rent::where('status', 'completed')->count() + Sale::where('status', 'completed')->count() }}</span>
                            </div>
                            <span class="text-gray-600">Transaksi Selesai</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-xl-3">
                <div class="card bg-light-warning card-flush h-md-100">
                    <div class="card-body">
                        <div class="d-flex flex-column">
                            <div class="d-flex align-items-center mb-1">
                                <span class="fs-2hx fw-bold text-warning me-2">{{ Rent::where('status', 'pending')->count() + Sale::where('status', 'pending')->count() }}</span>
                            </div>
                            <span class="text-gray-600">Transaksi Pending</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-xl-3">
                <div class="card bg-light-info card-flush h-md-100">
                    <div class="card-body">
                        <div class="d-flex flex-column">
                            <div class="d-flex align-items-center mb-1">
                                <span class="fs-2hx fw-bold text-info me-2">{{ $this->totalTransactions }}</span>
                            </div>
                            <span class="text-gray-600">Hasil Filter</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Transaction Table -->
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-row-dashed table-row-gray-300 gy-7" id="transaction_table">
                        <thead>
                            <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
                                <th class="min-w-100px cursor-pointer" wire:click="sort('code')">
                                    <div class="d-flex align-items-center gap-2">
                                        <span>Kode</span>
                                        @if($sortColumn === 'code')
                                            <i class="ki-outline ki-arrow-{{ $sortDirection === 'ASC' ? 'up' : 'down' }} fs-3 text-primary"></i>
                                        @endif
                                    </div>
                                </th>
                                <th class="min-w-150px">Pelanggan</th>
                                <th class="min-w-100px text-center">Tipe</th>
                                <th class="min-w-100px text-center">Cabang</th>
                                <th class="min-w-100px text-center">Promo</th>
                                <th class="min-w-100px text-center cursor-pointer" wire:click="sort('total_price')">
                                    <div class="d-flex align-items-center gap-2 justify-content-center">
                                        <span>Total Harga</span>
                                        @if($sortColumn === 'total_price')
                                            <i class="ki-outline ki-arrow-{{ $sortDirection === 'ASC' ? 'up' : 'down' }} fs-3 text-primary"></i>
                                        @endif
                                    </div>
                                </th>
                                <th class="min-w-100px text-center">Status</th>
                                <th class="min-w-100px text-center cursor-pointer" wire:click="sort('created_at')">
                                    <div class="d-flex align-items-center gap-2 justify-content-center">
                                        <span>Tanggal</span>
                                        @if($sortColumn === 'created_at')
                                            <i class="ki-outline ki-arrow-{{ $sortDirection === 'ASC' ? 'up' : 'down' }} fs-3 text-primary"></i>
                                        @endif
                                    </div>
                                </th>
                                <th class="min-w-100px text-end">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="text-gray-600 fw-semibold">
                            @forelse ($this->transactions as $transaction)
                                <tr>
                                    <td>{{ $transaction->code }}</td>
                                    <td>{{ $transaction->customer->name ?? 'N/A' }}</td>
                                    <td class="text-center">{{ $transaction->type === 'rent' ? 'Sewa' : 'Jual' }}</td>
                                    <td class="text-center">{{ $transaction->branch->name ?? 'N/A' }}</td>
                                    <td class="text-center">{{ $transaction->promo->code ?? '-' }}</td>
                                    <td class="text-center">Rp {{ number_format($transaction->total_price, 0, ',', '.') }}</td>
                                    <td class="text-center">
                                        <span class="badge badge-light-{{ match ($transaction->status) {
                                            'pending' => 'warning',
                                            'confirmed' => 'primary',
                                            'completed' => 'success',
                                            'cancelled' => 'danger',
                                            default => 'secondary'
                                        } }}">
                                            {{ match ($transaction->status) {
                                                'pending' => 'Pending',
                                                'confirmed' => 'Confirmed',
                                                'completed' => 'Selesai',
                                                'cancelled' => 'Dibatalkan',
                                                default => $transaction->status
                                            } }}
                                        </span>
                                    </td>
                                    <td class="text-center"></td>
                                    <td class="text-end">
                                        <div class="d-flex justify-content-end gap-2">
                                            <a 
                                                wire:navigate
                                                href="{{ route('admin.transaction.proof', ['code' => $transaction->code]) }}" 
                                                class="btn btn-icon btn-sm btn-light-warning"
                                                data-bs-toggle="tooltip"
                                                title="Edit"
                                            >
                                                <i class="ki-outline ki-notepad-edit fs-2"></i>
                                            </a>
                                            @if(auth()->user()->hasAnyRole(['Super Admin', 'Owner']))
                                            <button 
                                                onclick="hapus('{{ $transaction->type }}', {{ $transaction->id }});"
                                                type="button" 
                                                class="btn btn-icon btn-sm btn-light-danger"
                                                data-bs-toggle="tooltip"
                                                title="Hapus"
                                            >
                                                <i class="ki-outline ki-trash fs-2"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center py-10">
                                        <div class="d-flex flex-column align-items-center">
                                            <i class="ki-outline ki-search-list fs-4x text-muted mb-4"></i>
                                            <span class="text-gray-600 fs-6">Tidak ada transaksi yang ditemukan</span>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                @if($this->transactions->hasPages())
                    <div class="card-footer">
                        {{ $this->transactions->links() }}
                    </div>
                @endif
            </div>
        </div>

        @section('custom_js')
        <script data-navigate-once>
            function hapus(type, id) {
                Swal.fire({
                    title: 'Hapus Transaksi?',
                    text: 'Apakah Anda yakin ingin menghapus transaksi ini? Tindakan ini tidak dapat dibatalkan',
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
                                @this.delete(type, id).then(() => {
                                    Swal.fire({
                                        title: 'Berhasil!',
                                        text: 'Transaksi berhasil dihapus',
                                        icon: 'success',
                                        timer: 2000,
                                        timerProgressBar: true
                                    });
                                }).catch(error => {
                                    Swal.fire({
                                        title: 'Gagal!',
                                        html: `Gagal menghapus transaksi: <br><span class="text-red-500">${error.message}</span>`,
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