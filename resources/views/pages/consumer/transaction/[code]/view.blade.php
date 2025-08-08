<?php
use App\Models\Transaction\Rent;
use App\Models\Transaction\Sale;
use function Laravel\Folio\name;
use function Livewire\Volt\{state, computed};

name('consumer.transaction.view');

state(['code' => null]);

$transaction = computed(function () {
    // Try to find the transaction in Rent model
    $rent = Rent::query()
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
        ->where('code', $this->code)
        ->first();

    // If rent not found, try Sale model
    if (!$rent) {
        $sale = Sale::query()
            ->select(
                'sales.id',
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
            ->where('code', $this->code)
            ->first();
        
        return $sale;
    }

    return $rent;
});
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
}
?>

<x-app>
    @volt
    <div id="kt_app_content" class="app-content flex-column-fluid">
        <div class="row mb-4">
            <div class="col-12">
                <h2 class="h3 fw-bold">Detail Transaksi</h2>
                <p class="text-muted">Informasi lengkap tentang transaksi Anda</p>
            </div>
        </div>

        @if($this->transaction)
            <div class="row g-4">
                <div class="col-12 col-lg-8">
                    <div class="card card-dashed shadow-sm">
                        <div class="card-header p-4">
                            <div class="d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between">
                                <div class="d-flex flex-column">
                                    <span class="badge bg-{{ $this->transaction->type === 'rent' ? 'info' : 'success' }} bg-opacity-10 text-{{ $this->transaction->type === 'rent' ? 'info' : 'success' }} mb-2 align-self-start">
                                        {{ $this->transaction->type === 'rent' ? 'Sewa' : 'Belanja' }}
                                    </span>
                                    <h3 class="fs-5 fw-bold text-gray-900 mb-1">
                                        #{{ $this->transaction->code }}
                                    </h3>
                                    <span class="text-muted fs-7">
                                        <i class="bi bi-calendar3 me-1"></i>
                                        {{ $this->transaction->type === 'rent' ? $this->transaction->start_date->format('d M Y') . ' - ' . $this->transaction->end_date->format('d M Y') : $this->transaction->sale_date->format('d M Y') }}
                                        @if($this->transaction->type === 'rent')
                                            ({{ $this->transaction->total_days }} Hari)
                                        @endif
                                    </span>
                                </div>
                                <span class="badge {{ $this->transaction->status['class'] }} fs-7">
                                    {{ $this->transaction->status['text'] }}
                                </span>
                            </div>
                        </div>

                        <div class="card-body p-4">
                            <h4 class="fs-6 fw-bold mb-3">Detail Produk</h4>
                            <div class="scrollable-items" style="max-height: 300px; overflow-y: auto;">
                                @foreach($this->transaction->type === 'rent' ? $this->transaction->rentItems : $this->transaction->saleItems as $item)
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
                                                    Rp{{ number_format($this->transaction->type === 'rent' ? $item->productBranch->rent_price : $item->productBranch->sale_price) }}
                                                    {{ $this->transaction->type === 'rent' ? '/Hari' : '' }}
                                                </span>
                                                @if($this->transaction->type === 'rent')
                                                    <span class="text-muted fs-7">
                                                        x {{ $this->transaction->total_days }} hari
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            @if($this->transaction->notes)
                                <h4 class="fs-6 fw-bold mt-4 mb-3">Catatan</h4>
                                <p class="text-muted">{{ $this->transaction->notes }}</p>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="col-12 col-lg-4">
                    <div class="card card-dashed shadow-sm">
                        <div class="card-header p-4">
                            <h4 class="fs-6 fw-bold">Ringkasan Pembayaran</h4>
                        </div>
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Subtotal</span>
                                <span>Rp {{ number_format($this->transaction->total_price + $this->transaction->discount_amount, 0, ',', '.') }}</span>
                            </div>
                            @if($this->transaction->discount_amount)
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="text-muted">Diskon</span>
                                    <span>- Rp {{ number_format($this->transaction->discount_amount, 0, ',', '.') }}</span>
                                </div>
                            @endif
                            @if($this->transaction->type === 'rent' && $this->transaction->deposit_amount)
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="text-muted">Deposit</span>
                                    <span>Rp {{ number_format($this->transaction->deposit_amount, 0, ',', '.') }}</span>
                                </div>
                            @endif
                            @if($this->transaction->type === 'sale' && $this->transaction->shipping_price)
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="text-muted">Biaya Pengiriman</span>
                                    <span>Rp {{ number_format($this->transaction->shipping_price, 0, ',', '.') }}</span>
                                </div>
                            @endif
                            <div class="d-flex justify-content-between fw-bold">
                                <span>Total</span>
                                <span>Rp {{ number_format($this->transaction->total_price + ($this->transaction->type === 'sale' ? $this->transaction->shipping_price : 0), 0, ',', '.') }}</span>
                            </div>
                            <div class="d-flex justify-content-between mt-3">
                                <span class="text-muted">Total Dibayar</span>
                                <span>Rp {{ number_format($this->transaction->total_paid, 0, ',', '.') }}</span>
                            </div>
                        </div>
                        <div class="card-footer bg-transparent p-4 border-0">
                            @if($this->transaction->type === 'rent')
                                @if($this->transaction->status['text'] === 'Menunggu Konfirmasi')
                                    @if($this->transaction->hasBeenSigned())
                                        <a href="{{ route('consumer.transaction.sign', ['code' => $this->transaction->code]) }}" 
                                           wire:navigate 
                                           class="btn btn-primary btn-sm px-4 py-2 d-flex align-items-center w-100 mb-2">
                                            <i class="ki-filled ki-wallet me-2"></i> Bayar Sekarang
                                        </a>
                                    @else
                                        <a href="{{ route('consumer.transaction.sign', ['code' => $this->transaction->code]) }}" 
                                           wire:navigate 
                                           class="btn btn-primary btn-sm px-4 py-2 d-flex align-items-center w-100 mb-2">
                                            <i class="ki-filled ki-notepad-edit me-2"></i> Tanda Tangan Perjanjian
                                        </a>
                                    @endif
                                @elseif($this->transaction->status['text'] === 'Barang Belum Kembali')
                                    <button class="btn btn-warning btn-sm px-4 py-2 w-100 mb-2">
                                        <i class="bi bi-box-seam me-1"></i> Konfirmasi Pengembalian
                                    </button>
                                @elseif($this->transaction->status['text'] === 'Sedang Berjalan')
                                    <button class="btn btn-info btn-sm px-4 py-2 w-100 mb-2">
                                        <i class="bi bi-clock-history me-1"></i> Sedang Berjalan
                                    </button>
                                @elseif($this->transaction->status['text'] === 'Selesai')
                                    <button class="btn btn-success btn-sm px-4 py-2 w-100 mb-2">
                                        <i class="bi bi-check-circle me-1"></i> Transaksi Selesai
                                    </button>
                                @endif
                                @if($this->transaction->paymentRent)
                                    <button class="btn btn-outline-secondary btn-sm px-4 py-2 w-100 mb-2">
                                        <i class="ki-filled ki-printer me-1"></i> Cetak Invoice
                                    </button>
                                @endif
                                @if($this->transaction->status['text'] != 'Dibatalkan')
                                    <button onclick="batalkan('{{$this->transaction->id}}')" class="btn btn-danger btn-sm px-4 py-2 w-100 mb-2">
                                        <i class="bi bi-x-circle me-1"></i> Dibatalkan
                                    </button>
                                @endif
                            @else
                                <a href="{{ route('consumer.transaction.invoice', ['code' => $this->transaction->code]) }}" 
                                   wire:navigate 
                                   class="btn btn-outline-primary btn-sm px-4 py-2 w-100 mb-2">
                                    <i class="bi bi-receipt me-1"></i> Lihat Invoice
                                </a>
                            @endif
                            @if($this->transaction->type === 'rent' && $this->transaction->hasBeenSigned())
                                <a href="{{ route('consumer.transaction.sign', ['code' => $this->transaction->code]) }}" 
                                   wire:navigate 
                                   class="btn btn-outline-secondary btn-sm px-4 py-2 w-100">
                                    <i class="bi bi-file-text me-1"></i> Lihat Surat Perjanjian
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @else
            <div class="col-12 text-center">
                <p class="text-muted">Transaksi tidak ditemukan.</p>
            </div>
        @endif

        <div class="row mt-4">
            <div class="col-12">
                <a href="{{ route('consumer.transaction') }}" wire:navigate class="btn btn-light btn-sm">
                    <i class="bi bi-arrow-left me-1"></i> Kembali ke Daftar Transaksi
                </a>
            </div>
        </div>
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