<?php
use App\Models\Promo;
use App\Models\Transaction\Rent;
use App\Models\Transaction\Sale;
use function Laravel\Folio\name;
use App\Services\CouponService;
use function Livewire\Volt\{mount, state, computed};

name('consumer.transaction.pay');

state([
    'code' => null,
    'transaction' => null,
    'kode_kupon' => '',
    'kupon_terpakai' => false,
    'total_price_item' => 0,
    'subtotal' => 0,
    'biaya_layanan' => 4000,
    'diskon' => 0,
    'deposit' => 0,
    'grandtotal' => 0,
    'current_coupon' => null,
    'total_days' => 0,
    'items' => collect([]),
    'is_loading' => false,
    'snapToken' => null,
]);

mount(function ($code) {
    if (!auth()->check()) {
        abort(403, 'Please login to continue');
    }

    $this->code = $code;
    $this->transaction = Rent::with(['user', 'rentItems.productBranch.product', 'branch', 'payment'])
        ->where('code', $code)
        ->first() 
        ?? Sale::with(['user', 'saleItems.productBranch.product', 'branch', 'payment'])
            ->where('code', $code)
            ->firstOrFail();

    if ($this->transaction->user_id !== auth()->id()) {
        abort(403, 'Unauthorized');
    }

    $this->snapToken = $this->transaction->payment->snap_token;
    $this->initializeTransaction();
});

$initializeTransaction = function () {
    $this->items = $this->transaction instanceof Rent ? $this->transaction->rentItems : $this->transaction->saleItems;
    $this->total_days = $this->transaction instanceof Rent ? $this->transaction->total_days ?? 0 : 1;
    $this->deposit = $this->transaction->deposit ?? 0;

    // Initialize coupon state from transaction
    if ($this->transaction->discount_amount > 0) {
        $this->kupon_terpakai = true;
        $this->current_coupon = Promo::find($this->transaction->promo_id);
        $this->kode_kupon = $this->current_coupon?->code ?? '';
    }

    $this->calculateTotals();
};

$calculateTotals = function () {
    $couponService = app(CouponService::class);
    $totals = $couponService->calculateDiscount($this->transaction, $this->current_coupon);

    $this->subtotal = $totals['subtotal'];
    $this->diskon = $totals['diskon'];
    $this->grandtotal = $totals['grandtotal'];
    $this->total_days = $totals['total_days'];
    $this->total_price_item = $this->subtotal;
};

$terapkanKupon = function () {
    $this->validate([
        'kode_kupon' => 'required|exists:promos,code',
    ]);

    $this->is_loading = true;

    try {
        $couponService = app(CouponService::class);
        $result = $couponService->applyCoupon($this->transaction->code, $this->kode_kupon);

        $this->kupon_terpakai = true;
        $this->current_coupon = Promo::where('code', $this->kode_kupon)->first();
        $this->diskon = $result['totals']['diskon'];
        $this->grandtotal = $result['totals']['grandtotal'];
        $this->subtotal = $result['totals']['subtotal'];
        $this->total_days = $result['totals']['total_days'];

        $this->dispatch('toast-success', message: $result['message']);
    } catch (\Exception $e) {
        $this->dispatch('toast-error', message: $e->getMessage());
    } finally {
        $this->is_loading = false;
    }
};

$resetCoupon = function () {
    $this->is_loading = true;

    try {
        $couponService = app(CouponService::class);
        $result = $couponService->resetCoupon($this->transaction);

        $this->kupon_terpakai = false;
        $this->current_coupon = null;
        $this->kode_kupon = '';
        $this->diskon = $result['totals']['diskon'];
        $this->grandtotal = $result['totals']['grandtotal'];
        $this->subtotal = $result['totals']['subtotal'];
        $this->total_days = $result['totals']['total_days'];

        $this->dispatch('toast-success', message: 'Kupon berhasil dihapus.');
    } catch (\Exception $e) {
        $this->dispatch('toast-error', message: 'Gagal menghapus kupon: ' . $e->getMessage());
    } finally {
        $this->is_loading = false;
    }
};
?>

<x-app>
    @volt
    <div id="kt_app_content" class="app-content flex-column-fluid">
        <div class="container">
            <div class="d-flex align-items-center justify-content-center flex-wrap flex-lg-nowrap gap-3 gap-lg-2 pt-3 mb-5">
                <div class="text-sm lh-1 position-relative d-flex align-items-center gap-2 px-3 py-2 rounded-pill border border-secondary text-body bg-secondary bg-opacity-10">
                    <i class="ki-solid ki-check-circle text-success position-absolute top-0 end-0 translate-middle fs-6"></i>
                    <span class="text-body-secondary">
                        <i class="ki-filled ki-subtitle fs-6"></i>
                    </span>
                    Booking
                </div>
                
                <div class="d-none d-lg-block w-12 border-top border-dashed border-secondary border-opacity-30"></div>
                
                <div class="text-sm lh-1 position-relative d-flex align-items-center gap-2 px-3 py-2 rounded-pill border border-secondary text-body bg-secondary bg-opacity-10">
                    <i class="ki-solid ki-check-circle text-success position-absolute top-0 end-0 translate-middle fs-6"></i>
                    <span class="text-text-body-secondary">
                        <i class="ki-filled ki-notepad-edit fs-6"></i>
                    </span>
                    Tanda Tangan
                </div>
                
                <div class="d-none d-lg-block w-12 border-top border-dashed border-secondary border-opacity-30"></div>
                
                <div class="text-sm lh-1 position-relative d-flex align-items-center gap-2 px-3 py-2 rounded-pill border font-medium border-primary bg-primary bg-opacity-10 text-primary">
                    @if($this->transaction->payment && in_array($this->transaction->payment->transaction_status, ['settlement', 'capture']))
                    <i class="ki-solid ki-check-circle text-success position-absolute top-0 end-0 translate-middle fs-6"></i>
                    @endif
                    <span class="text-primary">
                        <i class="ki-filled ki-two-credit-cart fs-6"></i>
                    </span>
                    Pembayaran
                </div>
                
                <div class="d-none d-lg-block w-12 border-top border-dashed border-secondary border-opacity-30"></div>
                
                <div class="text-sm lh-1 position-relative d-flex align-items-center gap-2 px-3 py-2 rounded-pill border border-secondary text-body">
                    @if($this->transaction->payment && $this->transaction->payment->transaction_status === 'settlement')
                    <i class="ki-solid ki-check-circle text-success position-absolute top-0 end-0 translate-middle fs-6"></i>
                    @endif
                    <span class="text-{{ $this->transaction->payment && $this->transaction->payment->transaction_status === 'settlement' ? 'text-body-secondary' : 'primary' }}">
                        <i class="ki-filled ki-cheque fs-6"></i>
                    </span>
                    Pesanan Dibuat
                </div>
            </div>
        </div>
        <div class="d-flex flex-column flex-lg-row mb-10">
            <div class="flex-lg-row-fluid mb-10 mb-lg-0 me-lg-7 me-xl-10">
                <div class="card">
                    <div class="card-body p-12">
                        <div class="d-flex flex-column align-items-start flex-xxl-row">
                            <div class="d-flex align-items-center flex-equal fw-row me-4 order-2" data-bs-toggle="tooltip" data-bs-trigger="hover" title="Tanggal Ambil">
                                <div class="fs-6 fw-bold text-gray-700 text-nowrap">Tanggal Ambil:</div>
                                <div class="position-relative d-flex align-items-center w-150px">
                                    {{ $this->transaction->start_date->format('Y-m-d') }}
                                </div>
                            </div>
                            <div class="d-flex flex-center flex-equal fw-row text-nowrap order-1 order-xxl-2 me-4">
                                <span class="fs-2x fw-bold text-gray-800">INVOICE #{{ $this->transaction->code }}</span>
                            </div>
                            <div class="d-flex align-items-center justify-content-end flex-equal order-3 fw-row" data-bs-toggle="tooltip" data-bs-trigger="hover" title="Jam Ambil">
                                <div class="fs-6 fw-bold text-gray-700 text-nowrap">Jam Ambil:</div>
                                <div class="position-relative d-flex align-items-center w-150px">
                                    {{ $this->transaction->start_time->format('H:i') }}
                                </div>
                            </div>
                        </div>
                        <div class="separator separator-dashed my-10"></div>
                        <div class="mb-0">
                            <div class="row gx-10 mb-5">
                                <div class="col-lg-6">
                                    <label class="form-label fs-6 fw-bold text-gray-700 mb-3">Penyedia</label>
                                    <div class="mb-0">
                                        {{ config('app.name') }} {{ $this->transaction->branch->name }}
                                    </div>
                                    <div class="mb-5">
                                        {{ $this->transaction->branch->address }}
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <label class="form-label fs-6 fw-bold text-gray-700 mb-3">Penyewa</label>
                                    <div class="mb-0">
                                        {{ auth()->user()->name }}
                                    </div>
                                    <div class="mb-0">
                                        {{ auth()->user()->email }}
                                    </div>
                                    <div class="mb-5">
                                        +62{{ auth()->user()->phone }}
                                    </div>
                                    <div class="mb-5">
                                        {{ auth()->user()->userAddress->address ?? '' }}
                                    </div>
                                </div>
                            </div>
                            <div class="table-responsive mb-10">
                                <table class="table g-5 gs-0 mb-0 fw-bold text-gray-700" data-kt-element="items">
                                    <thead>
                                        <tr class="border-bottom fs-7 fw-bold text-gray-700 text-uppercase">
                                            <th class="min-w-300px w-350px">Item</th>
                                            <th class="min-w-100px w-125px text-end">Jumlah</th>
                                            <th class="min-w-150px w-150px text-end">Harga</th>
                                            <th class="min-w-100px w-150px text-end">Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($this->items as $item)
                                        <tr class="border-bottom border-bottom-dashed" data-kt-element="item">
                                            <td class="pe-7">
                                                <div class="d-flex align-items-center">
                                                    <div class="symbol symbol-50px me-4">
                                                        <img src="{{ $item->productBranch->product->image }}" alt="{{ $item->productBranch->product->name }}" />
                                                    </div>
                                                    <div>
                                                        <div class="fw-bold">{{ $item->productBranch->product->name }}</div>
                                                        <div class="text-muted">{!! $item->productBranch->desc !!}</div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="pt-8 ps-7 d-flex align-items-center">
                                                <span class="me-3">
                                                    {{ number_format($this->total_days) }} Hari
                                                </span>
                                            </td>
                                            <td class="pt-8 text-end text-nowrap">
                                                Rp. <span>{{ number_format($item->productBranch->rent_price) }}</span>
                                            </td>
                                            <td class="pt-8 text-end text-nowrap">
                                                Rp. <span>{{ number_format($item->subtotal) }}</span>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            @if($this->transaction->notes)
                            <div class="mb-0">
                                <label class="form-label fs-6 fw-bold text-gray-700">Catatan</label>
                                {{ $this->transaction->notes }}
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            <div class="flex-lg-auto min-w-lg-300px">
                <div class="card" data-kt-sticky="false" data-kt-sticky-name="invoice">
                    <div class="card-body p-10">
                        <div class="mb-8">
                            <h4 class="mb-4">Gunakan Kupon</h4>
                            <div class="input-group mb-3">
                                <input type="text" wire:model="kode_kupon" class="form-control" 
                                       placeholder="Masukkan kode kupon" wire:loading.attr="disabled"
                                       @if($this->transaction->payment && in_array($this->transaction->payment->transaction_status, ['settlement', 'capture'])) disabled @endif>
                                <button wire:click="terapkanKupon" class="btn btn-primary" 
                                        type="button" wire:loading.attr="disabled"
                                        @if($this->transaction->payment && in_array($this->transaction->payment->transaction_status, ['settlement', 'capture'])) disabled @endif>
                                    <span wire:loading.remove>Terapkan</span>
                                    <span wire:loading>Loading...</span>
                                </button>
                                @if($kupon_terpakai)
                                <button wire:click="resetCoupon" class="btn btn-danger ms-2" 
                                        type="button" wire:loading.attr="disabled"
                                        @if($this->transaction->payment && in_array($this->transaction->payment->transaction_status, ['settlement', 'capture'])) disabled @endif>
                                    <span wire:loading.remove>Hapus</span>
                                    <span wire:loading>Loading...</span>
                                </button>
                                @endif
                            </div>
                            @if($kupon_terpakai)
                                <div class="alert alert-success d-flex align-items-center p-3">
                                    <i class="ki-outline ki-tag fs-2hx me-4 text-success"></i>
                                    <div>
                                        <h5 class="mb-1">Kupon Terpakai!</h5>
                                        <span>Anda hemat Rp {{ number_format($diskon) }}</span>
                                    </div>
                                </div>
                            @endif
                        </div>
                        <div class="separator separator-dashed mb-8"></div>
                        <div class="text-center mb-8">
                            @if($this->transaction->payment && in_array($this->transaction->payment->transaction_status, ['settlement', 'capture']))
                            <div class="symbol symbol-100px symbol-circle mb-5">
                                <div class="symbol-label bg-light-success">
                                    <i class="ki-outline ki-check fs-1 text-success"></i>
                                </div>
                            </div>
                            <h3 class="text-success mb-2">Pembayaran Berhasil!</h3>
                            <p class="text-muted">Terima kasih telah melakukan pembayaran</p>
                            @elseif($this->transaction->payment && $this->transaction->payment->transaction_status === 'pending')
                            <div class="symbol symbol-100px symbol-circle mb-5">
                                <div class="symbol-label bg-light-warning">
                                    <i class="ki-outline ki-time fs-1 text-warning"></i>
                                </div>
                            </div>
                            <h3 class="text-warning mb-2">Menunggu Pembayaran</h3>
                            <p class="text-muted">Silakan selesaikan pembayaran Anda</p>
                            @else
                            <div class="symbol symbol-100px symbol-circle mb-5">
                                <div class="symbol-label bg-light-danger">
                                    <i class="ki-outline ki-cross fs-1 text-danger"></i>
                                </div>
                            </div>
                            <h3 class="text-danger mb-2">Belum Dibayar</h3>
                            <p class="text-muted">Silakan lakukan pembayaran untuk melanjutkan</p>
                            @endif
                        </div>
                        <div class="mb-8">
                            <div class="d-flex flex-column gap-3">
                                <div class="d-flex justify-content-between">
                                    <span>Subtotal:</span>
                                    <span class="fw-bold">Rp {{ number_format($subtotal) }}</span>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <span>Diskon:</span>
                                    <span class="fw-bold text-success">- Rp {{ number_format($diskon) }}</span>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <span>Biaya Layanan:</span>
                                    <span>Rp {{ number_format($biaya_layanan) }}</span>
                                </div>
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="d-flex align-items-center">
                                        <span class="text-gray-700 me-2">Deposit</span>
                                        <i class="ki-outline ki-information-5 text-gray-500 fs-6" 
                                           data-bs-toggle="tooltip" 
                                           title="Biaya deposit hanya untuk penyewa yang diluar domisili cabang pilihan"></i>
                                    </div>
                                    <span class="fw-bold text-{{ $deposit > 0 ? 'success' : 'danger' }}">
                                        Rp {{ number_format($deposit) }}
                                    </span>
                                </div>
                                <div class="d-flex justify-content-between border-top border-gray-300 pt-3 mt-2">
                                    <h4 class="m-0">Total:</h4>
                                    <h3 class="m-0 text-primary">Rp {{ number_format($grandtotal) }}</h3>
                                </div>
                            </div>
                        </div>
                        @if($this->transaction->payment && $this->transaction->payment->transaction_status === 'pending')
                        <button id="pay-button" class="btn btn-primary w-100 py-3 fw-boldest btn-lg" wire:loading.attr="disabled">
                            <span wire:loading.remove>
                                <i class="ki-outline ki-basket-ok fs-2 me-2"></i> 
                                Bayar Sekarang
                            </span>
                            <span wire:loading>Loading...</span>
                        </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    @section('custom_js')
        @if($this->transaction->payment && $this->transaction->payment->transaction_status === "pending")
        <script data-navigate-once src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ env('MIDTRANS_CLIENT_KEY') }}"></script>
        <script data-navigate-once>
            document.getElementById('pay-button').onclick = function(){
                snap.pay('{{ $this->transaction->payment ? $this->transaction->payment->snap_token : $this->transaction->paymentSale->snap_token }}', {
                    onSuccess: function(result){
                        window.location.href = "{{ route('consumer.transaction.view', ['code' => $this->transaction->code]) }}";
                    },
                    onPending: function(result){
                        window.location.href = "{{ route('consumer.transaction.view', ['code' => $this->transaction->code]) }}";
                    },
                    onError: function(result){
                        window.location.href = "{{ route('consumer.transaction.failed', ['code' => $this->transaction->code, 'payment' => $this->transaction->payment]) }}";
                    }
                });
            };
        </script>
        @endif
    @endsection
    @endvolt
</x-app>