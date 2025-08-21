<?php
use Midtrans\Snap;
use Midtrans\Config;
use App\Models\Master\Branch;
use App\Models\Transaction\Rent;
use App\Models\Transaction\Sale;
use function Laravel\Folio\name;
use App\Services\MidtransService;
use App\Models\Transaction\Payment;
use function Livewire\Volt\{state, computed, usesPagination};
use Illuminate\Support\Facades\DB;

usesPagination(theme: 'bootstrap');
name('admin.transaction');

state(['search' => '', 'type' => '', 'status' => '', 'branch_id' => null]);

$transactions = computed(function() {
    $commonSelect = [
        'id',
        'code',
        'user_id',
        'branch_id',
        DB::raw('IFNULL(start_date, sale_date) as date_start'),
        DB::raw('end_date as date_end'),
        DB::raw('pickup_time as time_start'),
        DB::raw('DATEDIFF(IFNULL(end_date, sale_date), start_date) as total_days'),
        'notes',
        DB::raw('0 as total_hour_late'), // Placeholder, hitung di method jika perlu
        DB::raw('IFNULL((SELECT value FROM promos WHERE id = rents.promo_id), 0) as discount_amount'),
        'deposit_amount',
        DB::raw('IFNULL(pickup_signature, receipt_number) as proof'),
        DB::raw('IFNULL(total_amount, total_amount) as total_amount'),
        DB::raw('IFNULL(paid_amount, paid_amount) as paid_amount'),
        'status',
        'created_at',
        'updated_at',
        DB::raw("type"),
    ];

    $rents = Rent::query()
        ->select([
            'id', 'code', 'user_id', 'branch_id', 'start_date', 'end_date',
            'pickup_time', 'notes', 'deposit_amount', 'pickup_signature',
            'total_amount', 'paid_amount', 'status', 'created_at', 'updated_at', 'promo_id'
        ])
        ->with(['branch:id,name', 'rentItems.productBranch.product:id,name', 'promo:id,value,type'])
        ->where('branch_id', auth()->user()->branch_id)
        ->whereNull('deleted_at')
        ->when($this->type === '' || $this->type === 'rent', fn($q) => $q)
        ->when($this->search, function($query) {
            $query->where('code', 'like', '%'.$this->search.'%')
                  ->orWhereHas('rentItems.productBranch.product', function($q) {
                      $q->where('name', 'like', '%'.$this->search.'%');
                  });
        })
        ->when($this->status, function($query) {
            $query->where('status', $this->status);
        })
        ->when($this->branch_id, function($query) {
            $query->where('branch_id', $this->branch_id);
        })
        ->addSelect(DB::raw("'rent' as type"));

    $sales = Sale::query()
        ->select([
            'id', 'code', 'user_id', 'branch_id', 'sale_date', 'total_amount',
            'paid_amount', 'receipt_number', 'status', 'created_at', 'updated_at'
        ])
        ->with(['branch:id,name', 'saleItems.productBranch.product:id,name'])
        ->where('branch_id', auth()->user()->branch_id)
        ->whereNull('deleted_at')
        ->when($this->type === '' || $this->type === 'sale', fn($q) => $q)
        ->when($this->search, function($query) {
            $query->where('code', 'like', '%'.$this->search.'%')
                  ->orWhereHas('saleItems.productBranch.product', function($q) {
                      $q->where('name', 'like', '%'.$this->search.'%');
                  });
        })
        ->when($this->status, function($query) {
            $query->where('status', $this->status);
        })
        ->when($this->branch_id, function($query) {
            $query->where('branch_id', $this->branch_id);
        })
        ->addSelect(DB::raw("'sale' as type"))
        ->addSelect(DB::raw('NULL as end_date, NULL as pickup_time, 0 as deposit_amount, NULL as pickup_signature, NULL as promo_id'));

    return $rents->union($sales)->latest()->paginate(10);
});

$branches = computed(function() {
    return Branch::select('id', 'name')->get();
});

$statusMap = computed(function() {
    return [
        'pending' => ['class' => 'badge-light-warning', 'text' => 'Menunggu'],
        'confirmed' => ['class' => 'badge-light-success', 'text' => 'Pembayaran Diterima'],
        'active' => ['class' => 'badge-light-success', 'text' => 'Sedang Berjalan'],
        'completed' => ['class' => 'badge-light-success', 'text' => 'Selesai'],
        'cancelled' => ['class' => 'badge-light-dark', 'text' => 'Dibatalkan'],
        'overdue' => ['class' => 'badge-light-warning', 'text' => 'Terlambat'],
        'paid' => ['class' => 'badge-light-success', 'text' => 'Dibayar'],
        'shipped' => ['class' => 'badge-light-primary', 'text' => 'Dikirim'],
    ];
});
$selesai = function($transaction){
    $transaksi = Rent::findOrFail($transaction) 
        ?? Sale::findOrFail($transaction);
    $transaksi->castAndUpdate(['status' => 'completed']);
    $this->dispatch('toast-success', message: "Transaksi telah diselesaikan");
    $this->redirect(route('admin.transaction'), navigate: true);
};
?>

<x-app>
    @volt
    <div id="kt_app_content" class="app-content flex-column-fluid">
        <div class="row mb-4">
            <div class="col-12">
                <h2 class="h3 fw-bold">Transaksi Saya</h2>
                <p class="text-muted">Lacak sewa dan pembelian Anda dengan mudah</p>
            </div>
        </div>
        <div class="row mb-4">
            <div class="col-12 col-md-6 col-lg-3 mb-3">
                <input type="search" wire:model.live="search" class="form-control" placeholder="Cari transaksi..." aria-label="Search transactions">
            </div>
            <div class="col-12 col-md-6 col-lg-3 mb-3">
                <select wire:model.live="type" class="form-select">
                    <option value="">Semua Transaksi</option>
                    <option value="rent">Sewa</option>
                    <option value="sale">Pembelian</option>
                </select>
            </div>
            <div class="col-12 col-md-6 col-lg-3 mb-3">
                <select wire:model.live="status" class="form-select">
                    <option value="">Semua Status</option>
                    <option value="pending">Menunggu</option>
                    <option value="confirmed">Pembayaran Diterima</option>
                    <option value="active">Sedang Berjalan</option>
                    <option value="completed">Selesai</option>
                    <option value="cancelled">Dibatalkan</option>
                    <option value="overdue">Terlambat</option>
                    <option value="paid">Dibayar</option>
                    <option value="shipped">Dikirim</option>
                </select>
            </div>
            <div class="col-12 col-md-6 col-lg-3 mb-3">
                <select wire:model.live="branch_id" class="form-select">
                    <option value="">Semua Cabang</option>
                    @foreach($this->branches as $branch)
                        <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="row">
            @forelse($this->transactions as $transaction)
                <div class="col-12 col-md-4 mb-4">
                    @php
                        // Mapping manual untuk status
                        $rentStatusMap = [
                            'pending' => ['class' => 'badge-light-primary', 'text' => 'Menunggu Konfirmasi'],
                            'confirmed' => ['class' => 'badge-light-success', 'text' => 'Pembayaran Diterima'],
                            'active' => ['class' => 'badge-light-success', 'text' => 'Sedang Berjalan'],
                            'completed' => ['class' => 'badge-light-success', 'text' => 'Selesai'],
                            'cancelled' => ['class' => 'badge-light-dark', 'text' => 'Dibatalkan'],
                            'overdue' => ['class' => 'badge-light-danger', 'text' => 'Jatuh Tempo'],
                        ];

                        $saleStatusMap = [
                            'pending' => ['class' => 'badge-light-primary', 'text' => 'Menunggu Konfirmasi'],
                            'confirmed' => ['class' => 'badge-light-success', 'text' => 'Dikonfirmasi'],
                            'on_delivery' => ['class' => 'badge-light-info', 'text' => 'Dalam Pengiriman'],
                            'completed' => ['class' => 'badge-light-success', 'text' => 'Selesai'],
                            'cancelled' => ['class' => 'badge-light-dark', 'text' => 'Dibatalkan'],
                        ];

                        $statusInfo = $transaction->type === 'rent' 
                            ? ($rentStatusMap[$transaction->status] ?? ['class' => 'badge-light-secondary', 'text' => $transaction->status])
                            : ($saleStatusMap[$transaction->status] ?? ['class' => 'badge-light-secondary', 'text' => $transaction->status]);
                    @endphp
                    <div class="card card-dashed shadow-sm h-100 transition-all hover-shadow-lg">
                        <div class="card-header d-flex flex-row flex-md-row align-items-start align-items-md-center justify-content-between p-3 p-md-4">
                            <div class="d-flex flex-column me-md-4">
                                <span class="badge bg-{{ $transaction->type === 'rent' ? 'info' : 'success' }} bg-opacity-10 text-{{ $transaction->type === 'rent' ? 'info' : 'success' }} mb-2 align-self-start">
                                    {{ $transaction->type === 'rent' ? 'Sewa' : 'Belanja' }}
                                </span>
                                <span class="text-muted fs-7">
                                    <i class="bi bi-calendar3 me-1"></i>
                                    {{ $transaction->start_date->format('d M y') }} - {{ $transaction->end_date->format('d M y') }} 
                                    <span class="d-inline-block d-md-none">({{ $transaction->total_days }} Hari)</span>
                                </span>
                                <p class="mb-1"><i class="ki-filled ki-shop me-1"></i> Cabang: {{ $transaction->branch->name ?? 'N/A' }}</p>
                            </div>
                            <div class="d-flex align-items-center mt-2 mt-md-0">
                                <span class="badge {{ $transaction->type === 'rent' ? ($transaction->status === 'active' && Rent::find($transaction->id)->isDue() ? 'badge-light-warning' : $this->statusMap[$transaction->status]['class'] ?? 'badge-light-primary') : ($this->statusMap[$transaction->status]['class'] ?? 'badge-light-primary') }} fw-semibold">
                                    {{ $transaction->type === 'rent' ? ($transaction->status === 'active' && Rent::find($transaction->id)->isDue() ? 'Barang Belum Kembali' : $this->statusMap[$transaction->status]['text'] ?? ucfirst($transaction->status)) : ($this->statusMap[$transaction->status]['text'] ?? ucfirst($transaction->status)) }}
                                </span>
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-icon btn-light rounded-circle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="bi bi-three-dots-vertical"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end">
                                        @if($transaction->type == "rent")
                                            @if($transaction->hasBeenSigned())
                                            <li><a class="dropdown-item" href="{{ route('admin.transaction.sign', ['code' => $transaction->code]) }}" wire:navigate>Lihat Surat Perjanjian</a></li>
                                            @endif
                                        @endif
                                        @if($transaction->type == "rent" ? $transaction->paymentRent : $transaction->paymentSale)
                                            <li><a class="dropdown-item" href="{{ route('admin.transaction.invoice', ['code' => $transaction->code]) }}" wire:navigate>Cetak Invoice</a></li>
                                        @endif
                                        <li><a class="dropdown-item" href="{{ route('admin.transaction.view', ['code' => $transaction->code]) }}" wire:navigate>Detail</a></li>
                                        @if($statusInfo['text'] == "Menunggu Konfirmasi")
                                        <li><hr class="dropdown-divider"></li>
                                        <li><a class="dropdown-item text-danger" href="#">Batalkan</a></li>
                                        @endif
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <div class="card-body p-3 p-md-4">
                            @foreach ($transaction->items as $item)
                            <div class="d-flex align-items-center mb-3 pb-3 border-bottom border-gray-200 last-border-0">
                                <div class="symbol symbol-60px symbol-circle me-3 flex-shrink-0">
                                    <img src="{{ asset('storage/' . $item->productBranch->product->thumbnail) }}" 
                                        alt="{{ $item->productBranch->product->name }}" 
                                        class="w-100 object-fit-cover"
                                        onerror="this.src='https://placehold.co/600?text=Produk'">
                                </div>
                                <div class="flex-grow-1 min-w-0">
                                    <h4 class="text-gray-800 fw-bold fs-6 text-truncate mb-1" title="{{ $item->productBranch->product->name }}">
                                        {{ $item->productBranch->product->name }}
                                    </h4>
                                    <div class="d-flex flex-wrap align-items-center">
                                        <span class="text-muted fs-7 me-2">
                                            <i class="bi bi-tag-fill me-1"></i>
                                            Rp{{ number_format($transaction->type == "rent" ? $item->productBranch->rent_price : $item->productBranch->sale_price) }}
                                            {{ $transaction->type == "rent" ? '/Hari' : '' }}
                                        </span>
                                        <span class="text-muted fs-7">
                                            x {{ $item->duration_days }} hari
                                        </span>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                            <p class="mb-0"><i class="ki-filled ki-notepad me-1"></i> Catatan: {{ $transaction->notes ?? 'Tidak ada' }}</p>
                        </div>
                        <div class="card-footer bg-transparent p-3 p-md-4 border-0">
                            <div class="d-flex flex-row flex-md-row align-items-center justify-content-between">
                                <div class="mb-3 mb-md-0">
                                    <span class="text-muted fs-7 d-block">
                                        Total Harga
                                        <i class="ki-filled ki-information" data-bs-toggle="tooltip" data-bs-html="true" title="<em>Tooltip</em> <u>with</u> <b>HTML</b>">
                                        </i>
                                    </span>
                                    <span class="text-primary fw-bold fs-5">
                                        Rp {{ number_format($transaction->total_amount, 0, ',', '.') }}
                                    </span>
                                </div>
                                @if($transaction->type === 'rent')
                                    @if($transaction->status == "confirmed")
                                        @if(!$transaction->hasBeenSigned())
                                            <a wire:navigate href="{{ route('admin.transaction.sign', ['code' => $transaction->code]) }}" class="btn btn-primary btn-sm px-4 py-2 d-flex align-items-center transition-all">
                                                <i class="ki-filled ki-notepad-edit me-2"></i> Tanda Tangan Perjanjian
                                            </a>
                                        @endif
                                    @elseif($transaction->status == "active")
                                        <button onclick="selesai({{ $transaction->id }})" class="btn btn-success btn-sm px-3">
                                            <i class="ki-filled ki-check me-1"></i> Barang sudah kembali
                                        </button>
                                    @endif
                                @else
                                    <button class="btn btn-outline-primary btn-sm px-3">
                                        <i class="bi bi-receipt me-1"></i> Lihat Invoice
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12 text-center">
                    <p class="text-muted">Tidak ada transaksi ditemukan.</p>
                </div>
            @endforelse
        </div>
        @if($this->transactions->hasPages())
            <div class="row mt-4">
                <div class="col-12">
                    {{ $this->transactions->links() }}
                </div>
            </div>
        @endif
        @section('custom_js')
            <script data-navigate-once>
                function selesai(id) {
                    Swal.fire({
                        title: 'Transaksi sudah selesai?',
                        text: "Transaksi ini akan selesai dan tidak dapat dikembalikan.",
                        icon: 'info',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#6c757d',
                        confirmButtonText: 'Ya, Selesaikan',
                        cancelButtonText: 'Tidak',
                        reverseButtons: true,
                        backdrop: true,
                        allowOutsideClick: false,
                    }).then((result) => {
                        if (result.isConfirmed) {
                            Swal.fire({
                                title: 'Selesaikan transaksi...',
                                html: 'Sedang memproses permintaan Anda',
                                allowOutsideClick: false,
                                didOpen: () => {
                                    Swal.showLoading();
                                    @this.selesai(id).then(() => {
                                        Swal.fire({
                                            title: 'Berhasil!',
                                            text: 'Transaksi berhasil diselesaikan',
                                            icon: 'success',
                                            timer: 2000,
                                            timerProgressBar: true
                                        });
                                    }).catch(error => {
                                        Swal.fire({
                                            title: 'Gagal!',
                                            html: `Gagal menyelesaikan transaksi: <br><span class="text-red-500">${error.message}</span>`,
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