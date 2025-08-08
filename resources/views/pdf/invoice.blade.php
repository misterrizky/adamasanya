<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice {{ $transaction->code }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            color: #333;
            margin: 40px;
        }
        .container {
            max-width: 950px;
            margin: 0 auto;
        }
        .header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 40px;
        }
        .header h1 {
            font-size: 36px;
            font-weight: bold;
            color: #1a1a1a;
            margin: 0;
        }
        .badge {
            display: inline-block;
            padding: 5px 10px;
            font-size: 12px;
            font-weight: bold;
            border-radius: 4px;
            margin-bottom: 10px;
        }
        .badge-info {
            background-color: rgba(0, 123, 255, 0.1);
            color: #007bff;
        }
        .badge-success {
            background-color: rgba(40, 167, 69, 0.1);
            color: #28a745;
        }
        .text-muted {
            color: #6c757d;
        }
        .text-right {
            text-align: right;
        }
        .fs-2 {
            font-size: 24px;
            font-weight: bold;
        }
        .fs-5 {
            font-size: 16px;
        }
        .fs-6 {
            font-size: 14px;
        }
        .fs-7 {
            font-size: 12px;
        }
        .fw-bold {
            font-weight: bold;
        }
        .separator {
            border-bottom: 1px solid #dee2e6;
            margin: 20px 0;
        }
        .order-details {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
        }
        .order-details div {
            flex: 1;
            min-width: 150px;
        }
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .table th, .table td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px dashed #dee2e6;
        }
        .table th {
            font-weight: bold;
            color: #6c757d;
        }
        .table td.text-end {
            text-align: right;
        }
        .table .grand-total {
            font-size: 18px;
            font-weight: bold;
            color: #1a1a1a;
        }
        .product-item {
            display: flex;
            align-items: center;
        }
        .product-item img {
            width: 50px;
            height: 50px;
            object-fit: cover;
            margin-right: 10px;
            border-radius: 4px;
        }
        .footer {
            margin-top: 40px;
            text-align: center;
            color: #6c757d;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <div>
                <h1>INVOICE</h1>
                <span class="badge {{ $type === 'rent' ? 'badge-info' : 'badge-success' }}">
                    {{ $type === 'rent' ? 'Sewa' : 'Pembelian' }}
                </span>
            </div>
            <div class="text-right">
                <img src="{{ public_path('media/icons/logo.png') }}" alt="Logo" style="max-width: 150px;" />
                <div class="fs-6 text-muted" style="margin-top: 20px;">
                    <div>{{ $branch->name }}</div>
                    <div>{{ $branch->address }}</div>
                    <div>{{ $branch->city->name }}, {{ $branch->state->name }}</div>
                </div>
            </div>
        </div>

        <!-- Body -->
        <div>
            <!-- Message -->
            <div class="fs-2">
                Dear {{ $transaction->user->name }}
                <span class="fs-6">({{ $transaction->user->email }})</span>,
                <br />
                <span class="text-muted fs-5">Berikut detail {{ $type === 'rent' ? 'rental' : 'sale' }} Anda. Terima kasih atas transaksi Anda.</span>
            </div>

            <!-- Separator -->
            <div class="separator"></div>

            <!-- Order Details -->
            <div class="order-details">
                <div>
                    <span class="text-muted">ID Transaksi</span><br />
                    <span class="fs-5">{{ $transaction->code }}</span>
                </div>
                <div>
                    <span class="text-muted">Tanggal Transaksi</span><br />
                    <span class="fs-5">
                        @if($type === 'rent')
                            {{ $transaction->start_date->format('d M Y') }} - {{ $transaction->end_date->format('d M Y') }}
                        @else
                            {{ $transaction->sale_date->format('d M Y') }}
                        @endif
                    </span>
                </div>
                <div>
                    <span class="text-muted">Status</span><br />
                    <span class="badge {{ $transaction->status['class'] }} fs-7">
                        {{ $transaction->status['text'] }}
                    </span>
                </div>
                <div>
                    <span class="text-muted">Cabang</span><br />
                    <span class="fs-5">{{ $branch->name }}</span>
                </div>
            </div>

            <!-- Billing & Shipping -->
            <div class="order-details" style="margin-top: 20px;">
                <div>
                    <span class="text-muted">Alamat Penagihan</span><br />
                    <span class="fs-6">{{ $transaction->user->userAddress->address ?? 'N/A' }}</span>
                </div>
                @if($type === 'sale')
                <div>
                    <span class="text-muted">Alamat Pengiriman</span><br />
                    <span class="fs-6">{{ $transaction->user->userAddress->address ?? 'N/A' }}</span>
                </div>
                @endif
            </div>

            <!-- Order Summary -->
            <div style="margin-top: 20px;">
                <table class="table">
                    <thead>
                        <tr>
                            <th style="min-width: 175px;">Produk</th>
                            <th class="text-end">Harga</th>
                            <th class="text-end">Qty</th>
                            <th class="text-end">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($type === 'rent' ? $transaction->rentItems : $transaction->saleItems as $item)
                        <tr>
                            <td>
                                <div class="product-item">
                                    <img src="{{ public_path('storage/' . $item->productBranch->product->thumbnail) }}"
                                         alt="{{ $item->productBranch->product->name }}"
                                         onerror="this.src='https://placehold.co/600?text=Produk'" />
                                    <div>
                                        <div class="fw-bold">{{ $item->productBranch->product->name }}</div>
                                        <div class="fs-7 text-muted">
                                            @if($type === 'rent')
                                                Masa Sewa: {{ $transaction->total_days }} hari
                                            @else
                                                Tanggal Pembelian: {{ $transaction->sale_date->format('d M Y') }}
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="text-end">
                                Rp {{ number_format($type === 'rent' ? $item->productBranch->rent_price : $item->productBranch->sale_price, 0, ',', '.') }}
                                @if($type === 'rent') /hari @endif
                            </td>
                            <td class="text-end">{{ $item->qty }}</td>
                            <td class="text-end">
                                Rp {{ number_format(($type === 'rent' ? $item->productBranch->rent_price * $transaction->total_days : $item->productBranch->sale_price) * $item->qty, 0, ',', '.') }}
                            </td>
                        </tr>
                        @endforeach
                        <tr>
                            <td colspan="3" class="text-end">Subtotal</td>
                            <td class="text-end">Rp {{ number_format($transaction->total_price - ($type === 'sale' ? $transaction->shipping_price : 0), 0, ',', '.') }}</td>
                        </tr>
                        @if($type === 'sale')
                        <tr>
                            <td colspan="3" class="text-end">Tarif Pengiriman</td>
                            <td class="text-end">Rp {{ number_format($transaction->shipping_price ?? 0, 0, ',', '.') }}</td>
                        </tr>
                        @endif
                        @if($transaction->discount_amount > 0)
                        <tr>
                            <td colspan="3" class="text-end">Diskon</td>
                            <td class="text-end">- Rp {{ number_format($transaction->discount_amount, 0, ',', '.') }}</td>
                        </tr>
                        @endif
                        @if($type === 'rent' && $transaction->deposit_amount > 0)
                        <tr>
                            <td colspan="3" class="text-end">Deposit</td>
                            <td class="text-end">Rp {{ number_format($transaction->deposit_amount, 0, ',', '.') }}</td>
                        </tr>
                        @endif
                        <tr>
                            <td colspan="3" class="text-end grand-total">Grand Total</td>
                            <td class="text-end grand-total">Rp {{ number_format($transaction->total_price, 0, ',', '.') }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>Terima kasih atas transaksi Anda di {{ $branch->name }}.</p>
        </div>
    </div>
</body>
</html>