<?php

use function Laravel\Folio\name;
use Livewire\Volt\Component;
use App\Services\MidtransService;
use App\Models\Transaction\Rent;
use App\Models\Transaction\Payment;

name('consumer.transaction.repayment');

new class extends Component {
    public Rent $rent;
    public float $jumlah_bayar; // Akan set ke remaining

    public function mount(string $code)
    {
        $this->rent = Rent::where('code', $code)->firstOrFail();
        $this->authorize('view', $this->rent); // Pakai policy jika ada
        $this->jumlah_bayar = $this->rent->remaining_amount;

        if ($this->jumlah_bayar <= 0) {
            $this->redirect(route('consumer.transaction.view', $this->rent->code));
        }
    }

    public function bayarPelunasan()
    {
        $this->validate(['jumlah_bayar' => 'required|numeric|eq:' . $this->rent->remaining_amount]); // Harus full sisa

        $payment = Payment::create([
            'payable_type' => Rent::class,
            'payable_id' => $this->rent->id,
            'order_id' => app(MidtransService::class)->generateOrderId($this->rent),
            'amount' => $this->jumlah_bayar,
            'transaction_status' => 'pending',
            // Lainnya: payment_type dll nanti dari Midtrans
        ]);

        $token = app(MidtransService::class)->createSnapToken($this->rent, $this->jumlah_bayar);

        $this->dispatch('show-snap', ['token' => $token, 'rentCode' => $this->rent->code]);
    }
};
?>
<x-app>
    @volt
    <!-- Blade View (integrasi Metronic) -->
    <div class="card card-flush">
        <div class="card-header">
            <h3 class="card-title">Pelunasan Transaksi #{{ $rent->code }}</h3>
        </div>
        <div class="card-body">
            <p>Sisa pembayaran: <strong>Rp {{ number_format($jumlah_bayar, 0, ',', '.') }}</strong></p>
            <p>Bayar sekarang untuk konfirmasi rental.</p>
            
            <button wire:click="bayarPelunasan" class="btn btn-primary">Bayar Pelunasan</button>
        </div>
    </div>
    
    <!-- JS sama seperti di product.show: Handle show-snap event untuk snap.pay -->
    @section('custom_js')
    <script data-navigate-once src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ env('MIDTRANS_CLIENT_KEY') }}"></script>
        <script data-navigate-once>
            function initProductPage() {
                const hariInput = document.querySelector('[wire\\:model="jumlah_hari"]');
                if (hariInput) {
                    const decreaseBtn = hariInput.previousElementSibling;
                    const increaseBtn = hariInput.nextElementSibling;

                    decreaseBtn.addEventListener('click', () => {
                        const current = parseInt(hariInput.value);
                        if (current > 1) {
                            hariInput.value = current - 1;
                            hariInput.dispatchEvent(new Event('input'));
                        }
                    });

                    increaseBtn.addEventListener('click', () => {
                        const current = parseInt(hariInput.value);
                        hariInput.value = current + 1;
                        hariInput.dispatchEvent(new Event('input'));
                    });
                }

                const pickupDate = document.getElementById('pickupDate');
                if (pickupDate) {
                    pickupDate.min = new Date().toISOString().split('T')[0];
                }

                const pickupTime = document.getElementById('pickupTime');
                if (pickupTime && !pickupTime.value) {
                    const nextHour = new Date(new Date().getTime() + 60 * 60 * 1000);
                    pickupTime.value = nextHour.toTimeString().substring(0, 5);
                }

                window.addEventListener('scroll', () => {
                    const actionBar = document.getElementById('stickyActionBar');
                    if (actionBar) {
                        actionBar.classList.toggle('show', window.scrollY > 100);
                    }
                });
            }

            if (document.readyState === 'complete') {
                initProductPage();
            } else {
                document.addEventListener('DOMContentLoaded', initProductPage);
            }

            document.addEventListener('livewire:navigated', initProductPage);
            window.addEventListener('show-snap', (event) => {
                // Perbaikan: Ambil payload dari array event.detail
                const payload = event.detail[0];
                
                if (!payload) {
                    console.error('Payload is missing', event.detail);
                    return;
                }
                
                const { token, rentCode } = payload;
                
                if (!token) {
                    console.error('Snap token is missing in payload', payload);
                    alert('Terjadi kesalahan pada pembayaran. Silakan coba lagi.');
                    return;
                }
                
                snap.pay(token, {
                    onSuccess: function(result) {
                        window.location.href = `/consumer/transaction/${rentCode}`;
                    },
                    onPending: function(result) {
                        window.location.href = `/consumer/transaction/${rentCode}`;
                    },
                    onError: function(error) {
                        alert('Pembayaran gagal: ' + error.status_message);
                    },
                    onClose: function() {
                        console.log('Payment modal ditutup');
                    }
                });
            });
        </script>
    @endsection
    @endvolt
</x-app>