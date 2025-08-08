<?php
use App\Models\Transaction\Rent;
use App\Models\Transaction\Sale;
use function Laravel\Folio\name;
use App\Services\MidtransService;
use App\Models\Transaction\Payment;
use Spatie\Activitylog\Facades\Activity;
use function Livewire\Volt\{mount, state};

name('consumer.transaction.sign');

state(['code', 'transaction']);
mount(function ($code) {
    $this->transaction = Rent::with(['user', 'items.productBranch', 'payment'])
        ->where('code', $code)
        ->first() 
        ?? Sale::with(['user', 'items.productBranch', 'payment'])
            ->where('code', $code)
            ->firstOrFail();

    if ($this->transaction->user_id !== auth()->id()) {
        abort(403, 'Unauthorized');
    }
});

$bayar = function(Rent $rent) {
    if ($this->transaction->user_id !== auth()->id()) {
        $this->dispatch('toast-error', message: "Anda tidak dapat membayar sewa yang bukan milik Anda");
        Activity::event('payment_unauthorized_attempt')
            ->log('Percobaan akses tagihan oleh user tidak berhak');
        return;
    }

    // Cek status pembayaran
    if ($this->transaction->payment && in_array($this->transaction->payment->transaction_status, ['settlement', 'capture'])) {
        $this->dispatch('toast-info', message: 'Tagihan sudah dibayar');
        return redirect()->route('consumer.transaction.view', ['code' => $this->transaction->code]);
    }

    if ($this->transaction->payment && $this->transaction->payment->transaction_status === 'pending') {
        return $this->redirect(route('consumer.transaction.pay', [
            'code' => $this->transaction->code
        ]), navigate: true);
    }

    try {
        $midtransService = app(MidtransService::class);
        $orderId = 'RENT-' . $this->transaction->id . '-' . now()->timestamp;
        $snapToken = $midtransService->createSnapToken($this->transaction);

        $payment = Payment::castAndCreate([
            'transaction_applicable' => get_class($this->transaction),
            'transaction_id' => $this->transaction->id,
            'user_id' => auth()->id(),
            'midtrans_order_id' => $orderId,
            'gross_amount' => $this->transaction->total_price,
            'transaction_status' => 'pending',
            'snap_token' => $snapToken
        ]);

        Activity::performedOn($payment)
            ->event('payment_created')
            ->log('Pembayaran baru dibuat');

        return $this->redirect(route('consumer.transaction.pay', [
            'code' => $this->transaction->code
        ]), navigate: true);

    } catch (\Exception $e) {
        Activity::event('payment_init_failed')
            ->withProperties(['error' => $e->getMessage()])
            ->log('Gagal inisialisasi pembayaran');
        $this->dispatch('toast-error', message: 'Gagal memulai pembayaran: ' . $e->getMessage());
    }
};
?>

<x-app>
    @volt
    <div id="kt_app_content" class="app-content flex-column-fluid">
        <div class="container">
            <div class="d-flex align-items-center justify-content-center flex-wrap flex-lg-nowrap gap-3 gap-lg-2 pt-3 mb-5">
                <!-- Step Button (Completed) -->
                <div class="text-sm lh-1 position-relative d-flex align-items-center gap-2 px-3 py-2 rounded-pill border border-secondary text-body bg-secondary bg-opacity-10">
                    <i class="ki-solid ki-check-circle text-success position-absolute top-0 end-0 translate-middle fs-6"></i>
                    <span class="text-body-secondary">
                        <i class="ki-filled ki-subtitle fs-6"></i>
                    </span>
                    Booking
                </div>
                
                <div class="d-none d-lg-block w-12 border-top border-dashed border-secondary border-opacity-30"></div>
                
                <!-- Step Button (Active) -->
                <div class="text-sm lh-1 position-relative d-flex align-items-center gap-2 px-3 py-2 rounded-pill border font-medium border-primary bg-primary bg-opacity-10 text-primary">
                    @if($this->transaction->hasBeenSigned())
                    <i class="ki-solid ki-check-circle text-success position-absolute top-0 end-0 translate-middle fs-6"></i>
                    @endif
                    <span class="text-{{ $this->transaction->hasBeenSigned() ? 'text-body-secondary' : 'primary' }}">
                        <i class="ki-filled ki-notepad-edit fs-6"></i>
                    </span>
                    Tanda Tangan
                </div>
                
                <div class="d-none d-lg-block w-12 border-top border-dashed border-secondary border-opacity-30"></div>
                
                <!-- Step Button -->
                <div class="text-sm lh-1 position-relative d-flex align-items-center gap-2 px-3 py-2 rounded-pill border border-secondary text-body">
                    @if($this->transaction->payment && in_array($this->transaction->payment->transaction_status, ['settlement', 'capture']))
                    <i class="ki-solid ki-check-circle text-success position-absolute top-0 end-0 translate-middle fs-6"></i>
                    @endif
                    <span class="text-{{ $this->transaction->payment ? 'text-body-secondary' : 'primary' }}">
                        <i class="ki-filled ki-two-credit-cart fs-6"></i>
                    </span>
                    Pembayaran
                </div>
                
                <div class="d-none d-lg-block w-12 border-top border-dashed border-secondary border-opacity-30"></div>
                
                <!-- Step Button -->
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
                        <div id="printable">
                            <div class="d-flex flex-column align-items-center flex-xxl-row">
                                <div class="d-flex align-items-center flex-equal fw-row me-4 order-2" data-bs-toggle="tooltip" data-bs-trigger="hover" title="Kode Sewa">
                                    <div class="fs-6 fw-bold text-gray-700 text-nowrap">
                                        Nomor : {{ $this->transaction->code }}
                                    </div>
                                </div>
                                <div class="d-flex flex-center flex-equal fw-row text-nowrap order-1 order-xxl-2 me-4">
                                    <span class="fs-2x fw-bold text-gray-800">Surat Perjanjian Sewa</span>
                                </div>
                            </div>
                            <div class="separator separator-dashed my-10"></div>
                            <div class="mb-5">
                                <p class="text-justify">Pada hari ini, {{ \Carbon\Carbon::parse($this->transaction->created_at)->isoFormat('dddd') }} tanggal {{ \Carbon\Carbon::parse($this->transaction->created_at)->isoFormat('D MMMM Y') }}, bertempat di {{ $this->transaction->branch->address }}, telah dibuat perjanjian sewa menyewa antara:</p>
                            </div>
                            <div class="mb-0">
                                <div class="row gx-10 mb-5">
                                    <div class="col-12 col-md-6">
                                        <div class="card-xl-stretch mb-8 border border-gray-300 border-dashed rounded min-w-125px py-3 px-4 me-6 mb-5">
                                            <div class="text-center fw-semibold fs-6 text-gray-500">PIHAK PERTAMA (PENYEDIA)</div>
                                            <div class="align-items-center">
                                                <strong>Nama Perusahaan:</strong> {{ config('app.name') }} {{ $this->transaction->branch->name }}
                                                <br/>
                                                <strong>Alamat:</strong> {{ $this->transaction->branch->address }}
                                                <br/>
                                                <strong>Nomor Telepon:</strong> +6287765346368
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <div class="card-xl-stretch mb-8 border border-gray-300 border-dashed rounded min-w-125px py-3 px-4 me-6 mb-5">
                                            <div class="text-center fw-semibold fs-6 text-gray-500">PIHAK KEDUA (PENYEWA)</div>
                                            <div class="align-items-center">
                                                <strong>Nama : </strong>{{ auth()->user()->name }}
                                                <br/>
                                                <strong>Alamat : </strong>{{ auth()->user()->userAddress->address ?? '' }}, Kel. {{ auth()->user()->userAddress->village->name }}, Kec. {{ auth()->user()->userAddress->subdistrict->name }},  {{ auth()->user()->userAddress->city->type }} {{ auth()->user()->userAddress->city->name }}, {{ auth()->user()->userAddress->state->name }} {{ auth()->user()->userAddress->village->poscode }}
                                                <br/>
                                                <strong>Nomor Telepon : </strong>+62{{ auth()->user()->phone }}
                                                <br/>
                                                <strong>Email : </strong>{{ auth()->user()->email }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <h3 class="section-title">PASAL 1 - OBJEK PERJANJIAN</h3>
                                <p>Pihak Pertama menyewakan kepada Pihak Kedua dan Pihak Kedua menyewa dari Pihak Pertama barang sebagai berikut:</p>
                                <div class="table-responsive">
                                    <table class="table">
                                        <thead>
                                            <tr class="fw-semibold fs-6 text-gray-800 border-bottom border-gray-200">
                                                <th>No</th>
                                                <th>Nama Barang</th>
                                                <th>Jumlah Hari</th>
                                                <th>Harga Sewa</th>
                                                <th>Subtotal</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($this->transaction->rentItems as $index => $item)
                                            <tr class="border-bottom border-bottom-dashed">
                                                <td>{{ $index + 1 }}</td>
                                                <td>
                                                    <div class="mb-2">
                                                        {{ $item->productBranch->product->name }} {{ $item->qty }} Unit
                                                    </div>
                                                    <div class="mb-2">
                                                        {!! $item->productBranch->desc !!}
                                                    </div>
                                                </td>
                                                <td>
                                                    {{ number_format($this->transaction->total_days) }} Hari
                                                </td>
                                                <td>
                                                    Rp.{{ number_format($item->productBranch->rent_price) }}
                                                </td>
                                                <td>
                                                    Rp. {{ number_format($item->subtotal) }}
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                <h3 class="section-title mt-5">PASAL 2 - WAKTU DAN TEMPAT PENGAMBILAN</h3>
                                <p>Barang akan diambil pada:</p>
                                <p>
                                    <strong>Tanggal:</strong> {{ \Carbon\Carbon::parse($this->transaction->start_at)->isoFormat('D MMMM Y') }}
                                    <br/>
                                    <strong>Jam:</strong> {{ $this->transaction->start_time->format("H:i") }}
                                    <br/>
                                    <strong>Tempat:</strong> {{ $this->transaction->branch->address }}
                                </p>
                                <h3 class="section-title mt-4">PASAL 3 - PEMBAYARAN</h3>
                                <p>Total biaya sewa sebesar <strong>Rp {{ number_format($this->transaction->total_price) }}</strong> dengan rincian:</p>
                                <ul class="payment-details">
                                    <li>Subtotal: Rp {{ number_format($this->transaction->items->sum('subtotal')) }}</li>
                                    @if($this->transaction->discount_amount > 0)
                                    <li>Diskon: Rp {{ number_format($this->transaction->discount_amount) }}</li>
                                    @endif
                                    @if($this->transaction->deposit > 0)
                                    <li>Deposit: Rp {{ number_format($this->transaction->deposit) }}</li>
                                    @endif
                                    <li>Biaya Layanan: Rp {{ number_format(4000) }}</li>
                                    <li><strong>Grand Total: Rp {{ number_format($this->transaction->total_price) }}</strong></li>
                                </ul>
        
                                <h3 class="section-title mt-4">PASAL 4 - KETENTUAN LAIN-LAIN</h3>
                                <ol>
                                    <li>Pihak Kedua bertanggung jawab penuh atas barang yang disewa selama masa sewa.</li>
                                    <li>Kerusakan atau kehilangan barang menjadi tanggung jawab Pihak Kedua.</li>
                                    <li>Perjanjian ini dapat dibatalkan dengan pemberitahuan tertulis minimal 1x24 jam sebelum waktu pengambilan.</li>
                                    <li>Jika 3x24 jam dari masa sewa berakhir dan tidak melakukan pembayaran, maka transaksi dianggap batal.</li>
                                </ol>
                            </div>
                            <div class="section mt-10">
                                <div class="row">
                                    <div class="col-12 col-md-6">
                                        <h5 class="section-title text-center">PIHAK PERTAMA</h5>
                                        <p class="text-center">({{ config('app.name') }} {{ $this->transaction->branch->name }})</p>
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <h5 class="section-title text-center">PIHAK KEDUA</h5>
                                        @if (!$this->transaction->hasBeenSigned())
                                            <form action="{{ $this->transaction->getSignatureRoute() }}" method="POST">
                                                @csrf
                                                <div style="text-align: center">
                                                    <x-creagia-signature-pad button-classes="btn btn-sm btn-primary mb-5 mt-5 me-5" clear-name="Hapus Tanda Tangan" submit-name="Simpan Tanda Tangan" :disabled-without-signature="true"/>
                                                </div>
                                            </form>
                                        @else
                                        <div class="text-center">
                                            <img src="{{ asset('storage/'.$this->transaction->signature->getSignatureImagePath()) }}" alt="Tanda tangan" class="img-fluid" style="max-height: 150px;">
                                            @php
                                            $signedAt = \App\Models\TandaTangan::where('model_type', 'App\Models\Transaction\Rent')->where('model_id', $this->transaction->id)->first()->created_at->format('d/m/Y H:i');
                                            @endphp
                                            <p class="mt-2">Tanda tangan telah disimpan pada {{ $signedAt }}</p>
                                        </div>
                                        @endif
                                        <p class="text-center">({{ auth()->user()->name }})</p>
                                    </div>
                                </div>
                                @if($this->transaction->hasBeenSigned() && !$this->transaction->payment)
                                    <div class="text-center mt-5">
                                        <button wire:click="bayar({{ $this->transaction->id }})" class="btn btn-primary">Lanjut ke Pembayaran</button>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
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
    @section('custom_js')
        <script data-navigate-once src="{{ asset('vendor/sign-pad/sign-pad.min.js') }}"></script>
    @endsection
</x-app>