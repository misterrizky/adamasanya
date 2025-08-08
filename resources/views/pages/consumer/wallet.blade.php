<?php
use function Laravel\Folio\name;
use function Livewire\Volt\{computed, state};

name('consumer.wallet');

state([
    'amount' => null,
    'bankAccount' => null,
]);

$wallets = computed(function () {
    return auth()->user()->wallets()->get();
});

$totalBalance = computed(function () {
    return auth()->user()->wallets()->sum('balance');
});

$refundBalance = computed(function () {
    return auth()->user()->wallets()->where('slug', 'refund')->sum('balance');
});

$earningsBalance = computed(function () {
    return auth()->user()->wallets()->where('slug', 'earnings')->sum('balance');
});

$transactions = computed(function () {
    return auth()->user()->wallet->transactions()
        ->whereBetween('created_at', [now()->startOfMonth(), now()])
        ->get();
});

$withdraw = function () {
    $this->validate([
        'amount' => ['required', 'numeric', 'min:10000', 'max:' . $this->totalBalance],
        'bankAccount' => ['required', 'in:bca,mandiri'],
    ]);

    $wallet = auth()->user()->wallet; // Default wallet
    $wallet->withdraw($this->amount, ['bank_account' => $this->bankAccount]);

    session()->flash('message', 'Withdrawal request submitted successfully.');
    $this->reset(['amount', 'bankAccount']);
};
?>

<x-app>
    <x-toolbar-mobile 
        :breadcrumbs="[
            ['icon' => 'arrow-left', 'url' => route('profile')],
            ['text' => 'Saldo Kamu', 'active' => true]
        ]"
    />
    @volt
    <div id="kt_app_content" class="app-content flex-column-fluid py-5">
        <div class="container">
            <!-- Balance Overview -->
            <div class="card bg-light border-0 mb-5 rounded-3 shadow-sm position-relative overflow-hidden">
                <div class="card-body p-4 p-md-5 text-center">
                    <h4 class="card-title mb-2">Total Saldo {{ config('app.name') }}</h4>
                    <h2 class="display-5 mb-3">Rp{{ number_format($this->totalBalance, 0, ',', '.') }}</h2>
                    <div class="row g-3 justify-content-center">
                        <div class="col-6 col-md-4">
                            <div class="card h-100 border-0 bg-white shadow-sm">
                                <div class="card-body p-2">
                                    <h6 class="card-subtitle text-muted mb-1">Saldo Refund <i class="fas fa-info-circle" data-bs-toggle="tooltip" title="Saldo dari pengembalian dana"></i></h6>
                                    <p class="card-text mb-0">Rp{{ number_format($this->refundBalance, 0, ',', '.') }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-6 col-md-4">
                            <div class="card h-100 border-0 bg-white shadow-sm">
                                <div class="card-body p-2">
                                    <h6 class="card-subtitle text-muted mb-1">Saldo Penghasilan <i class="fas fa-info-circle" data-bs-toggle="tooltip" title="Saldo dari pendapatan"></i></h6>
                                    <p class="card-text mb-0">Rp{{ number_format($this->earningsBalance, 0, ',', '.') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <button class="btn btn-outline-primary mt-4" data-bs-toggle="modal" data-bs-target="#withdrawModal">Tarik Saldo</button>
                </div>
            </div>

            <!-- Transaction History -->
            <div class="card shadow-sm border-0 rounded-3">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Riwayat Transaksi</h5>
                    <div class="input-group w-auto">
                        <input type="text" class="form-control" id="dateRange" value="{{ now()->startOfMonth()->format('d M Y') }} - {{ now()->format('d M Y') }}" readonly>
                        <button class="btn btn-outline-secondary" type="button" id="datePickerToggle"><i class="fas fa-calendar"></i></button>
                    </div>
                </div>
                <div class="card-body p-0">
                    <ul class="nav nav-tabs nav-justified" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active text-success" id="all-tab" data-bs-toggle="tab" data-bs-target="#all" type="button" role="tab" aria-controls="all" aria-selected="true">Semua</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link text-success" id="refund-tab" data-bs-toggle="tab" data-bs-target="#refund" type="button" role="tab" aria-controls="refund" aria-selected="false">Saldo Refund</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link text-success" id="earnings-tab" data-bs-toggle="tab" data-bs-target="#earnings" type="button" role="tab" aria-controls="earnings" aria-selected="false">Saldo Penghasilan</button>
                        </li>
                    </ul>
                    <div class="tab-content p-3">
                        <div class="tab-pane fade show active" id="all" role="tabpanel" aria-labelledby="all-tab">
                            @if ($this->transactions->isEmpty())
                                <div class="text-center py-5">
                                    <img src="/images/mascot-transaction.png" alt="Transaction Mascot" class="img-fluid mb-3" style="max-width: 150px;" loading="lazy">
                                    <p class="text-muted">Tidak ada transaksi pada rentang waktu ini</p>
                                </div>
                            @else
                                <ul class="list-group list-group-flush">
                                    @foreach ($this->transactions as $transaction)
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            <span>{{ $transaction->meta['description'] ?? 'Transaction' }}</span>
                                            <span class="text-{{ $transaction->type === 'deposit' ? 'success' : 'danger' }}">Rp{{ number_format($transaction->amount, 0, ',', '.') }}</span>
                                        </li>
                                    @endforeach
                                </ul>
                            @endif
                        </div>
                        <div class="tab-pane fade" id="refund" role="tabpanel" aria-labelledby="refund-tab">
                            @if ($this->transactions->where('meta.type', 'refund')->isEmpty())
                                <div class="text-center py-5">
                                    <p class="text-muted">Tidak ada transaksi refund.</p>
                                </div>
                            @else
                                <ul class="list-group list-group-flush">
                                    @foreach ($this->transactions->where('meta.type', 'refund') as $transaction)
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            <span>{{ $transaction->meta['description'] ?? 'Refund' }}</span>
                                            <span class="text-success">Rp{{ number_format($transaction->amount, 0, ',', '.') }}</span>
                                        </li>
                                    @endforeach
                                </ul>
                            @endif
                        </div>
                        <div class="tab-pane fade" id="earnings" role="tabpanel" aria-labelledby="earnings-tab">
                            @if ($this->transactions->where('meta.type', 'earnings')->isEmpty())
                                <div class="text-center py-5">
                                    <p class="text-muted">Tidak ada transaksi penghasilan.</p>
                                </div>
                            @else
                                <ul class="list-group list-group-flush">
                                    @foreach ($this->transactions->where('meta.type', 'earnings') as $transaction)
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            <span>{{ $transaction->meta['description'] ?? 'Earnings' }}</span>
                                            <span class="text-success">Rp{{ number_format($transaction->amount, 0, ',', '.') }}</span>
                                        </li>
                                    @endforeach
                                </ul>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Withdraw Modal -->
        <div class="modal fade" id="withdrawModal" tabindex="-1" aria-labelledby="withdrawModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title" id="withdrawModalLabel">Tarik Saldo</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form wire:submit.prevent="withdraw">
                        @error('amount') <span class="text-danger">{{ $message }}</span> @enderror
                        @error('bankAccount') <span class="text-danger">{{ $message }}</span> @enderror
                            <div class="mb-3">
                                <label for="amount" class="form-label">Jumlah (Rp)</label>
                                <input type="number" class="form-control" id="amount" wire:model="amount" min="10000" required>
                            </div>
                            <div class="mb-3">
                                <label for="bankAccount" class="form-label">Rekening Bank</label>
                                <select class="form-select" id="bankAccount" wire:model="bankAccount" required>
                                    <option value="">Pilih Rekening</option>
                                    <option value="bca">BCA - 1234567890</option>
                                    <option value="mandiri">Mandiri - 0987654321</option>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-primary">Tarik Sekarang</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Custom CSS -->
        <style>
            .card-img-top {
                height: 100px;
                object-fit: cover;
            }
            .nav-tabs .nav-link {
                border-radius: 0.5rem;
                margin-right: 0.5rem;
                padding: 0.75rem 1.5rem;
                font-weight: 500;
            }
            .nav-tabs .nav-link.active {
                background-color: #e9f7ef;
                border-color: #28a745;
            }
            @media (max-width: 576px) {
                .card-body {
                    padding: 1rem;
                }
                .card-title {
                    font-size: 1.2rem;
                }
                .display-5 {
                    font-size: 1.5rem;
                }
                .input-group {
                    width: 100%;
                }
            }
        </style>

        <!-- JavaScript for Date Picker -->
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const dateRange = document.getElementById('dateRange');
                const datePickerToggle = document.getElementById('datePickerToggle');
                datePickerToggle.addEventListener('click', () => {
                    // Integrate with a date picker library like Flatpickr
                    alert('Pilih rentang tanggal (contoh: 01 Agu 2025 - 08 Agu 2025)');
                });
            });
        </script>
    </div>
    @endvolt
</x-app>