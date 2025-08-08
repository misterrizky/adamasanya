<?php
use App\Models\Transaction\Rent;
use App\Models\Transaction\Sale;
use function Laravel\Folio\name;
use function Livewire\Volt\{mount, state};

name('consumer.transaction.invoice');

state(['code', 'transaction', 'type']);

mount(function ($code) {
    // Fetch transaction (Rent or Sale) with related data
    $this->transaction = Rent::with(['user', 'rentItems.productBranch.product'])
        ->where('code', $code)
        ->where('user_id', auth()->id())
        ->first();

    if ($this->transaction) {
        $this->type = 'rent';
    } else {
        $this->transaction = Sale::with(['user', 'saleItems.productBranch.product'])
            ->where('code', $code)
            ->where('user_id', auth()->id())
            ->firstOrFail();
        $this->type = 'sale';
    }

    // Fetch branch details
    $this->transaction->branch = $this->transaction->branch;
});

$download = function() {
    $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.invoice', [
        'transaction' => $this->transaction,
        'type' => $this->type,
        'branch' => $this->transaction->branch,
    ]);
    return response()->streamDownload(function () use ($pdf) {
        echo $pdf->output();
    }, 'invoice-' . $this->transaction->code . '.pdf');
};
?>

<x-app>
    @volt
    <div id="kt_app_content" class="app-content flex-column-fluid">
        <div class="card shadow-sm">
            <!-- begin::Body-->
            <div class="card-body py-10 py-md-20">
                <!-- begin::Wrapper-->
                <div class="mw-lg-950px mx-auto w-100">
                    <!-- begin::Header-->
                    <div class="d-flex justify-content-between flex-column flex-sm-row mb-10 mb-md-19">
                        <div>
                            <h4 class="fw-bolder text-gray-800 fs-2qx pe-5 pb-7">INVOICE</h4>
                            <span class="badge bg-{{ $this->type === 'rent' ? 'info' : 'success' }} bg-opacity-10 text-{{ $this->type === 'rent' ? 'info' : 'success' }} mb-2">
                                {{ $this->type === 'rent' ? 'Sewa' : 'Pembelian' }}
                            </span>
                        </div>
                        <div class="text-sm-end">
                            <!-- begin::Logo-->
                            <a href="#" class="d-block mw-150px ms-sm-auto">
                                <img alt="Logo" src="{{ asset('media/icons/logo.png') }}" class="w-100" />
                            </a>
                            <!-- end::Logo-->
                            <div class="text-sm-end fw-semibold fs-6 text-muted mt-7">
                                <div>{{ $this->transaction->branch->name }}</div>
                                <div>{{ $this->transaction->branch->address }}</div>
                                <div>{{ $this->transaction->branch->city->name }}, {{ $this->transaction->branch->state->name }}</div>
                            </div>
                        </div>
                    </div>
                    <!-- end::Header-->
                    <!-- begin::Body-->
                    <div class="pb-12">
                        <!-- begin::Wrapper-->
                        <div class="d-flex flex-column gap-7 gap-md-10">
                            <!-- begin::Message-->
                            <div class="fw-bold fs-2">
                                Dear {{ $this->transaction->user->name }}
                                <span class="fs-6">({{ $this->transaction->user->email }})</span>,
                                <br />
                                <span class="text-muted fs-5">Berikut detail {{ $this->type === 'rent' ? 'rental' : 'sale' }} Anda. Terima kasih atas transaksi Anda.</span>
                            </div>
                            <!-- end::Message-->
                            <!-- begin::Separator-->
                            <div class="separator border-gray-200"></div>
                            <!-- end::Separator-->
                            <!-- begin::Order details-->
                            <div class="d-flex flex-column flex-sm-row gap-7 gap-md-10 fw-bold">
                                <div class="flex-root d-flex flex-column">
                                    <span class="text-muted">ID Transaksi</span>
                                    <span class="fs-5">{{ $this->transaction->code }}</span>
                                </div>
                                <div class="flex-root d-flex flex-column">
                                    <span class="text-muted">Tanggal Transaksi</span>
                                    <span class="fs-5">
                                        @if($this->type === 'rent')
                                            {{ $this->transaction->start_date->format('d M Y') }} - {{ $this->transaction->end_date->format('d M Y') }}
                                        @else
                                            {{ $this->transaction->sale_date->format('d M Y') }}
                                        @endif
                                    </span>
                                </div>
                                <div class="flex-root d-flex flex-column">
                                    <span class="text-muted">Status</span>
                                    <span class="badge {{ $this->transaction->status['class'] }} fs-7">
                                        {{ $this->transaction->status['text'] }}
                                    </span>
                                </div>
                                <div class="flex-root d-flex flex-column">
                                    <span class="text-muted">Cabang</span>
                                    <span class="fs-5">{{ $this->transaction->branch->name }}</span>
                                </div>
                            </div>
                            <!-- end::Order details-->
                            <!-- begin::Billing & shipping-->
                            <div class="d-flex flex-column flex-sm-row gap-7 gap-md-10 fw-bold">
                                <div class="flex-root d-flex flex-column">
                                    <span class="text-muted">Alamat Penagihan</span>
                                    <span class="fs-6">{{ $this->transaction->user->userAddress->address ?? 'N/A' }}</span>
                                </div>
                                @if($this->type === 'sale')
                                <div class="flex-root d-flex flex-column">
                                    <span class="text-muted">Alamat pengiriman</span>
                                    <span class="fs-6">{{ $this->transaction->user->userAddress->address ?? 'N/A' }}</span>
                                </div>
                                @endif
                            </div>
                            <!-- end::Billing & shipping-->
                            <!-- begin::Order summary-->
                            <div class="d-flex justify-content-between flex-column">
                                <div class="table-responsive border-bottom mb-9">
                                    <table class="table align-middle table-row-dashed fs-6 gy-5 mb-0">
                                        <thead>
                                            <tr class="border-bottom fs-6 fw-bold text-muted">
                                                <th class="min-w-175px pb-2">Produk</th>
                                                <th class="min-w-100px text-end pb-2">Harga</th>
                                                <th class="min-w-80px text-end pb-2">Qty</th>
                                                <th class="min-w-100px text-end pb-2">Total</th>
                                            </tr>
                                        </thead>
                                        <tbody class="fw-semibold text-gray-600">
                                            @foreach($this->type === 'rent' ? $this->transaction->rentItems : $this->transaction->saleItems as $item)
                                            <tr>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <!-- begin::Thumbnail-->
                                                        <div class="symbol symbol-50px me-3">
                                                            <img src="{{ asset('storage/' . $item->productBranch->product->thumbnail) }}"
                                                                 alt="{{ $item->productBranch->product->name }}"
                                                                 class="w-100 object-fit-cover"
                                                                 onerror="this.src='https://placehold.co/600?text=Produk'" />
                                                        </div>
                                                        <!-- end::Thumbnail-->
                                                        <!-- begin::Title-->
                                                        <div class="ms-5">
                                                            <div class="fw-bold">{{ $item->productBranch->product->name }}</div>
                                                            <div class="fs-7 text-muted">
                                                                @if($this->type === 'rent')
                                                                    Masa Sewa: {{ $this->transaction->total_days }} hari
                                                                @else
                                                                    Tanggal Pembelian: {{ $this->transaction->sale_date->format('d M Y') }}
                                                                @endif
                                                            </div>
                                                        </div>
                                                        <!-- end::Title-->
                                                    </div>
                                                </td>
                                                <td class="text-end">
                                                    Rp {{ number_format($this->type === 'rent' ? $item->productBranch->rent_price : $item->productBranch->sale_price, 0, ',', '.') }}
                                                    @if($this->type === 'rent') /hari @endif
                                                </td>
                                                <td class="text-end">{{ $item->qty }}</td>
                                                <td class="text-end">
                                                    Rp {{ number_format(($this->type === 'rent' ? $item->productBranch->rent_price * $this->transaction->total_days : $item->productBranch->sale_price) * $item->qty, 0, ',', '.') }}
                                                </td>
                                            </tr>
                                            @endforeach
                                            <tr>
                                                <td colspan="3" class="text-end">Subtotal</td>
                                                <td class="text-end">Rp {{ number_format($this->transaction->total_price - ($this->type === 'sale' ? $this->transaction->shipping_price : 0), 0, ',', '.') }}</td>
                                            </tr>
                                            @if($this->type === 'sale')
                                            <tr>
                                                <td colspan="3" class="text-end">Tarif Pengiriman</td>
                                                <td class="text-end">Rp {{ number_format($this->transaction->shipping_price ?? 0, 0, ',', '.') }}</td>
                                            </tr>
                                            @endif
                                            @if($this->transaction->discount_amount > 0)
                                            <tr>
                                                <td colspan="3" class="text-end">Diskon</td>
                                                <td class="text-end">- Rp {{ number_format($this->transaction->discount_amount, 0, ',', '.') }}</td>
                                            </tr>
                                            @endif
                                            @if($this->type === 'rent' && $this->transaction->deposit_amount > 0)
                                            <tr>
                                                <td colspan="3" class="text-end">Deposit</td>
                                                <td class="text-end">Rp {{ number_format($this->transaction->deposit_amount, 0, ',', '.') }}</td>
                                            </tr>
                                            @endif
                                            <tr>
                                                <td colspan="3" class="fs-3 text-gray-900 fw-bold text-end">Grand Total</td>
                                                <td class="text-gray-900 fs-3 fw-bolder text-end">Rp {{ number_format($this->transaction->total_price, 0, ',', '.') }}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <!-- end::Order summary-->
                        </div>
                        <!-- end::Wrapper-->
                    </div>
                    <!-- end::Body-->
                    <!-- begin::Footer-->
                    <div class="d-flex flex-stack flex-wrap mt-lg-20 pt-13">
                        <!-- begin::Actions-->
                        <div class="my-1 me-5">
                            <button type="button" wire:click="download" class="btn btn-light-success my-1">Unduh PDF</button>
                        </div>
                        <!-- end::Actions-->
                    </div>
                    <!-- end::Footer-->
                </div>
                <!-- end::Wrapper-->
            </div>
            <!-- end::Body-->
        </div>
        <div class="row mt-4">
            <div class="col-12">
                <a href="{{ route('consumer.transaction') }}" wire:navigate class="btn btn-light btn-sm">
                    <i class="bi bi-arrow-left me-1"></i> Kembali ke Daftar Transaksi
                </a>
            </div>
        </div>
    </div>
    @endvolt
</x-app>