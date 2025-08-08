<?php
use Midtrans\Snap;
use Midtrans\Config;
use App\Models\Master\Branch;
use App\Models\Transaction\Rent;
use App\Models\Transaction\Sale;
use App\Services\MidtransService;
use function Laravel\Folio\name;
use App\Models\Transaction\PaymentRent;
use App\Models\Transaction\PaymentSale;
use function Livewire\Volt\{state, computed, usesPagination};

usesPagination(theme: 'bootstrap');
name('consumer.transaction');
state(['search' => '', 'type' => '', 'status' => '', 'branch_id' => null]);

$transactions = computed(function() {
    // Query untuk rents
    $rents = Rent::query()
        ->select(
            'rents.id',
            'rents.code',
            'rents.user_id',
            'rents.branch_id',
            'rents.start_date',
            'rents.end_date',
            'rents.start_time',
            'rents.total_days',
            'rents.notes',
            'rents.total_hour_late',
            'rents.discount_amount',
            'rents.deposit_amount',
            'rents.proof_of_collection',
            'rents.total_price',
            'rents.total_paid',
            'rents.status',
            'rents.created_at',
            'rents.updated_at',
            DB::raw("'rent' as type")
        )
        ->with(['branch', 'rentItems.productBranch', 'rentItems.productBranch.product'])
        ->where('user_id', auth()->id())
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
        });

    // Query untuk sales
    $sales = Sale::query()
        ->select(
            'sales.id',
            'sales.id as ABC',
            'sales.id as BBA',
            'sales.id as CBA',
            'sales.id as DBA',
            'sales.code',
            'sales.user_id',
            'sales.branch_id',
            'sales.sale_date',
            'sales.discount_amount',
            'sales.total_price',
            'sales.total_paid',
            'sales.receipt_number',
            'sales.shipping_price',
            'sales.payment_type',
            'sales.status',
            'sales.created_at',
            'sales.updated_at',
            DB::raw("'sale' as type")
        )
        ->with(['branch', 'saleItems.productBranch', 'saleItems.productBranch.product'])
        ->where('user_id', auth()->id())
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
        });

    // Gabungkan dengan UNION
    return $rents->union($sales)
        ->orderBy('created_at', 'desc')
        ->paginate(10);
});

$branches = computed(fn() => Branch::where('st', 'a')->orderBy('name')->get());
$batalkan = function($transaction){
    $transaksi = Rent::with(['user', 'items.productBranch'])->findOrFail($transaction) 
        ?? Sale::with(['user', 'items.productBranch'])->findOrFail($transaction);
    if ($transaksi->user_id !== auth()->id()) {
        abort(403, 'Unauthorized');
    }
    // Logika untuk membatalkan transaksi
    // Misalnya, mengupdate status transaksi menjadi 'cancelled'
    $transaksi->castAndUpdate(['status' => 'canceled']);
    $this->dispatch('toast-success', message: "Transaksi tela dibatalkan");
    $this->redirect(route('consumer.transaction'), navigate: true);
};

$payNow = function($code) {
    $transaksi = Rent::with(['user', 'rentItems.productBranch.product', 'branch', 'paymentRent'])
        ->where('code', $code)
        ->first()
        ?? Sale::with(['user', 'saleItems.productBranch.product', 'branch', 'paymentSale'])
            ->where('code', $code)
            ->firstOrFail();

    if ($transaksi->user_id !== auth()->id()) {
        abort(403, 'Unauthorized');
    }

    $pembayaran = $transaksi instanceof Rent ? $transaksi->paymentRent : $transaksi->paymentSale;

    // Check if payment exists and is already settled
    if ($pembayaran && in_array($pembayaran->transaction_status, ['settlement', 'capture'])) {
        $this->dispatch('toast-info', message: 'Tagihan sudah dibayar');
        return;
    }

    try {
        $midtransService = app(MidtransService::class);
        $orderId = $midtransService->generateOrderId($transaksi);
        $snapToken = $midtransService->createSnapToken($transaksi);

        $paymentModel = $transaksi instanceof Rent ? PaymentRent::class : PaymentSale::class;
        $payment = $paymentModel::updateOrCreate(
            [
                ($transaksi instanceof Rent ? 'rent_id' : 'sale_id') => $transaksi->id,
                'user_id' => auth()->id()
            ],
            [
                'midtrans_order_id' => $orderId,
                'gross_amount' => $transaksi->total_price,
                'transaction_status' => 'pending',
                'snap_token' => $snapToken
            ]
        );

        return $this->redirect(
            route('consumer.transaction.pay', [
                'code' => $transaksi->code,
                'payment' => $payment,
                'snapToken' => $snapToken
            ]),
            navigate: true
        );
    } catch (\Exception $e) {
        $this->dispatch('toast-error', message: 'Gagal inisialisasi pembayaran: ' . $e->getMessage());
    }
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
            <div class="col-12 col-md-6 col-lg-4 mb-3">
                <input type="search" wire:model.live="search" class="form-control" placeholder="Cari transaksi..." aria-label="Search transactions">
            </div>
            <div class="col-12 col-md-6 col-lg-4 mb-3">
                <select wire:model.live="type" class="form-select">
                    <option value="">Semua Transaksi</option>
                    <option value="rent">Sewa</option>
                    <option value="sale">Pembelian</option>
                </select>
            </div>
            <div class="col-12 col-md-6 col-lg-4 mb-3">
                <select wire:model.live="status" class="form-select">
                    <option value="">Semua Status</option>
                    <option value="pending">Menunggu</option>
                    <option value="completed">Selesai</option>
                    <option value="cancelled">Dibatalkan</option>
                </select>
            </div>
            <div class="col-12 col-md-6 col-lg-4 mb-3">
                <select wire:model.live="branch_id" class="form-select">
                    <option value="">Semua Cabang</option>
                    @foreach($this->branches as $branch)
                        <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="row g-4">
            @forelse($this->transactions as $transaction)
                <div class="col-12 col-md-6 col-lg-4 mb-4">
                    <div class="card card-dashed shadow-sm h-100 transition-all hover-shadow-lg">
                        <div class="card-header d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between p-3 p-md-4">
                            <div class="d-flex flex-column me-md-4">
                                <span class="badge bg-{{ $transaction->type === 'rent' ? 'info' : 'success' }} bg-opacity-10 text-{{ $transaction->type === 'rent' ? 'info' : 'success' }} mb-2 align-self-start">
                                    {{ $transaction->type === 'rent' ? 'Sewa' : 'Belanja' }}
                                </span>
                                <h3 class="fs-5 fw-bold text-gray-900 mb-1">
                                    #{{ $transaction->code }}
                                </h3>
                                <span class="text-muted fs-7">
                                    <i class="bi bi-calendar3 me-1"></i>
                                    {{ $transaction->start_date->format('d M y') }} - {{ $transaction->end_date->format('d M y') }} 
                                    <span class="d-inline-block d-md-none">({{ $transaction->total_days }} Hari)</span>
                                </span>
                            </div>
                            <div class="d-flex align-items-center mt-2 mt-md-0">
                                <span class="badge {{ $transaction->status['class'] }} me-3 fs-7">
                                    {{ $transaction->status['text'] }}
                                </span>
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-icon btn-light rounded-circle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="bi bi-three-dots-vertical"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end">
                                        @if($transaction->type == "rent")
                                            @if($transaction->hasBeenSigned())
                                            <li><a class="dropdown-item" href="{{ route('consumer.transaction.sign', ['code' => $transaction->code]) }}" wire:navigate>Lihat Surat Perjanjian</a></li>
                                            @endif
                                        @endif
                                        @if($transaction->type == "rent" ? $transaction->paymentRent : $transaction->paymentSale)
                                            <li><a class="dropdown-item" href="{{ route('consumer.transaction.invoice', ['code' => $transaction->code]) }}" wire:navigate>Cetak Invoice</a></li>
                                        @endif
                                        <li><a class="dropdown-item" href="{{ route('consumer.transaction.view', ['code' => $transaction->code]) }}" wire:navigate>Detail</a></li>
                                        @if($transaction->status['text'] == "Menunggu Konfirmasi")
                                        <li><hr class="dropdown-divider"></li>
                                        <li><a class="dropdown-item text-danger" href="#">Batalkan</a></li>
                                        @endif
                                    </ul>
                                </div>
                            </div>
                        </div>
                        
                        <div class="card-body p-3 p-md-4">
                            <div class="scrollable-items" style="max-height: 200px; overflow-y: auto;">
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
                                                x {{ $transaction->total_days }} hari
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        <div class="card-footer bg-transparent p-3 p-md-4 border-0">
                            <div class="d-flex flex-column flex-md-row align-items-center justify-content-between">
                                <div class="mb-3 mb-md-0">
                                    <span class="text-muted fs-7 d-block">Total Harga</span>
                                    <span class="text-primary fw-bold fs-5">
                                        Rp {{ number_format($transaction->total_price, 0, ',', '.') }}
                                    </span>
                                </div>
                                
                                @if($transaction->type == "rent")
                                    @if($transaction->status['text'] == "Menunggu Konfirmasi")
                                        @if($transaction->hasBeenSigned())
                                        <button wire:click="payNow('{{ $transaction->code }}')"
                                        class="btn btn-primary btn-sm px-4 py-2 d-flex align-items-center transition-all">
                                            <i class="ki-filled ki-wallet me-2"></i> Bayar Sekarang
                                        </button>
                                        @else
                                        <a href="{{ route('consumer.transaction.sign', ['code' => $transaction->code]) }}" 
                                        wire:navigate 
                                        class="btn btn-primary btn-sm px-4 py-2 d-flex align-items-center transition-all">
                                            <i class="ki-filled ki-notepad-edit me-2"></i> Tanda Tangan Perjanjian
                                        </a>
                                        @endif
                                    @elseif($transaction->status['text'] == "Barang Belum Kembali")
                                    <button class="btn btn-warning btn-sm px-3">
                                        <i class="bi bi-box-seam me-1"></i> Perpanjang Durasi Sewa
                                    </button>
                                    @elseif($transaction->status['text'] == "Sedang Berjalan")
                                    <button class="btn btn-info btn-sm px-3">
                                        <i class="bi bi-clock-history me-1"></i> Perpanjang Durasi Sewa
                                    </button>
                                    @elseif($transaction->status['text'] == "Selesai")
                                        @if($transaction->rating)
                                        <button class="btn btn-success btn-sm px-3">
                                            <i class="bi bi-check-circle me-1"></i> Sewa Lagi
                                        </button>
                                        @else
                                        <button class="btn btn-light-warning btn-sm px-3">
                                            <i class="ki-filled ki-star me-1"></i> Beri Ulasan
                                        </button>
                                        @endif
                                    @endif
                                    @if($transaction->status['text'] != "Dibatalkan")
                                    <button onclick="batalkan('{{$transaction->id}}')" class="btn btn-light-danger btn-sm px-3">
                                        <i class="ki-filled ki-cross-circle"></i> Batalkan
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
                function batalkan(id) {
                    Swal.fire({
                        title: 'Batalkan transaksi ini?',
                        text: "Transaksi ini akan dibatalkan dan tidak dapat dikembalikan.",
                        icon: 'info',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#6c757d',
                        confirmButtonText: 'Ya, Batalkan',
                        cancelButtonText: 'Tidak',
                        reverseButtons: true,
                        backdrop: true,
                        allowOutsideClick: false,
                    }).then((result) => {
                        if (result.isConfirmed) {
                            Swal.fire({
                                title: 'Membatalkan transaksi...',
                                html: 'Sedang memproses permintaan Anda',
                                allowOutsideClick: false,
                                didOpen: () => {
                                    Swal.showLoading();
                                    @this.batalkan(id).then(() => {
                                        Swal.fire({
                                            title: 'Berhasil!',
                                            text: 'Transaksi berhasil dibatalkan',
                                            icon: 'success',
                                            timer: 2000,
                                            timerProgressBar: true
                                        });
                                    }).catch(error => {
                                        Swal.fire({
                                            title: 'Gagal!',
                                            html: `Gagal membatalkan transaksi: <br><span class="text-red-500">${error.message}</span>`,
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