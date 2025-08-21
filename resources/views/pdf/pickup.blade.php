<!DOCTYPE html>
<html>
<head>
    <title>Bukti Pengambilan Rental</title>
    <style>
        body { font-family: 'DejaVu Sans', sans-serif; }
        .signature { width: 150px; }
        .ematerai { width: 100px; }
    </style>
</head>
<body>
    <h2>Bukti Pengambilan Rental</h2>
    <p>Kode: {{ $rent->code }}</p>
    <p>Pelanggan: {{ $rent->user->name }}</p>
    <p>Cabang: {{ $rent->branch->name }}</p>
    <p>Produk: @foreach ($rent->rentItems as $item) {{ $item->productBranch->product->name }} ({{ $item->quantity }}) @endforeach</p>
    <p>Tanda Tangan: <img src="{{ $signature }}" class="signature"></p>
    <p>E-Materai: <img src="{{ $ematerai }}" class="ematerai"></p>
    <p>Tanggal: {{ now()->format('d-m-Y H:i') }}</p>
</body>
</html>