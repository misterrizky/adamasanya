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

    if ($this->transaction->user_id !== Auth::id()) {
        abort(403, 'Unauthorized');
    }
});

$bayar = function($code) {
    $transaksi = Rent::with(['user', 'items.productBranch', 'payment'])
        ->where('code', $code)
        ->first() 
        ?? Sale::with(['user', 'items.productBranch', 'payment'])
            ->where('code', $code)
            ->firstOrFail();
    if ($transaksi->user_id !== Auth::id()) {
        $this->dispatch('toast-error', message: "Anda tidak dapat membayar sewa yang bukan milik Anda");
        Activity::event('payment_unauthorized_attempt')
            ->log('Percobaan akses tagihan oleh user tidak berhak');
        return;
    }

    // Cek status pembayaran
    if ($transaksi->payment && in_array($transaksi->payment->transaction_status, ['settlement', 'capture'])) {
        $this->dispatch('toast-info', message: 'Tagihan sudah dibayar');
        return redirect()->route('consumer.transaction.view', ['code' => $transaksi->code]);
    }

    if ($transaksi->payment && $transaksi->payment->transaction_status === 'pending') {
        return $this->redirect(route('consumer.transaction.pay', [
            'code' => $transaksi->code
        ]), navigate: true);
    }

    try {
        $midtransService = app(MidtransService::class);
        $orderId = $transaksi->code;
        $snapToken = $midtransService->createSnapToken($transaksi);
        $paymentData = [
            'paid_amount' => $transaksi->total_price, // Default full payment
            'remaining_amount' => 0,
            'deposit_amount' => $transaksi->deposit_amount ?? 0,
            'shipping_price' => 0,
        ];
        $payment = Payment::castAndCreate([
            'merchant_id' => env('MIDTRANS_MERCHANT_ID', 'default_merchant'),
            'order_id' => $orderId,
            'transaction_midtrans_id' => null, // Akan diupdate saat notifikasi
            'transaction_applicable' => get_class($transaksi),
            'transaction_id' => $transaksi->id,
            'user_id' => Auth::id(),
            'gross_amount' => $transaksi->total_price,
            'currency' => 'IDR',
            'transaction_status' => 'pending',
            'transaction_time' => now(),
            'payment_data' => json_encode($paymentData),
            'snap_token' => $snapToken
        ]);

        Activity::performedOn($payment)
            ->event('payment_created')
            ->log('Pembayaran baru dibuat');

        return $this->redirect(route('consumer.transaction.pay', [
            'code' => $transaksi->code
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
                <div class="text-sm lh-1 position-relative d-flex align-items-center gap-2 px-3 py-2 rounded-pill border border-primary text-body bg-primary bg-opacity-10">
                    <span class="text-primary">
                        <i class="ki-filled ki-pencil fs-6"></i>
                    </span>
                    Tanda Tangan
                </div>
                
                <div class="d-none d-lg-block w-12 border-top border-dashed border-secondary border-opacity-30"></div>
                
                <!-- Step Button (Inactive) -->
                <div class="text-sm lh-1 position-relative d-flex align-items-center gap-2 px-3 py-2 rounded-pill border border-secondary text-body bg-secondary bg-opacity-10">
                    <span class="text-body-secondary">
                        <i class="ki-filled ki-wallet fs-6"></i>
                    </span>
                    Pembayaran
                </div>
            </div>
            <div class="card">
                <div class="card-body">
                    <div class="section">
                        <h3 class="section-title text-center mb-10">SURAT PERJANJIAN SEWA</h3>
                        <p class="text-center">Nomor: {{ $this->transaction->code }}</p>
                        <p class="mb-10">Pada hari ini, {{ now()->translatedFormat('l, d F Y') }}, kami yang bertanda tangan di bawah ini:</p>
                        <ol>
                            <li>
                                <strong>{{ config('app.name') }} {{ $this->transaction->branch->name }}</strong>, selaku Pihak Pertama (Penyewa).
                            </li>
                            <li>
                                <strong>{{ Auth::user()->name }}</strong>, selaku Pihak Kedua (Penyewa).
                            </li>
                        </ol>
                        <h3 class="section-title mt-10">PASAL 1 - OBJEK SEWA</h3>
                        <ol>
                            <li>Pihak Pertama menyewakan kepada Pihak Kedua barang sebagai berikut:</li>
                            <ul class="list-unstyled ms-5">
                                @foreach ($this->transaction->items as $item)
                                <li>- {{ $item->productBranch->product->name }} ({{ $item->qty }} unit)</li>
                                @endforeach
                            </ul>
                            <li>Masa sewa: {{ $this->transaction->total_days }} hari, mulai {{ $this->transaction->start_date->format('d/m/Y') }} hingga {{ $this->transaction->end_date->format('d/m/Y') }}.</li>
                        </ol>
        
                        <h3 class="section-title mt-4">PASAL 2 - BIAYA SEWA</h3>
                        <ol>
                            <li>Total biaya sewa: Rp {{ number_format($this->transaction->total_price) }}</li>
                            <li>Deposit: Rp {{ number_format($this->transaction->deposit_amount ?? 0) }}</li>
                            <li>Pembayaran dilakukan melalui transfer bank atau metode lain yang disetujui.</li>
                        </ol>
        
                        <h3 class="section-title mt-4">PASAL 3 - HAK DAN KEWAJIBAN</h3>
                        <ol>
                            <li>Pihak Kedua wajib menjaga barang sewa dalam kondisi baik.</li>
                            <li>Pihak Pertama berhak memeriksa barang sewa kapan saja.</li>
                            <li>Pihak Kedua wajib mengembalikan barang sewa tepat waktu.</li>
                        </ol>
        
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
                                <p class="text-center">({{ Auth::user()->name }})</p>
                            </div>
                        </div>
                        @if($this->transaction->hasBeenSigned() && !$this->transaction->payment)
                            <div class="text-center mt-5">
                                <button wire:click="bayar('{{ $this->transaction->code }}')" class="btn btn-primary">Lanjut ke Pembayaran</button>
                            </div>
                        @endif
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