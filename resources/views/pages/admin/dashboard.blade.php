<?php
use App\Models\User;
use App\Models\Product;
use App\Models\Master\Branch;
use App\Models\Transaction\Rent;
use App\Models\Transaction\Sale;
use function Laravel\Folio\name;
use App\Models\Transaction\RentItem;
use App\Models\Transaction\SaleItem;
use function Livewire\Volt\{computed, state};

name('admin.dashboard');
state(['branch_id' => '', 'period' => 'monthly']);

$branches = computed(fn () => Branch::pluck('name', 'id')->toArray());

$transactionSummary = computed(function () {
    $rentQuery = Rent::query()->selectRaw('COUNT(*) as count, SUM(total_amount) as revenue')
        ->when($this->branch_id, fn($q) => $q->where('branch_id', $this->branch_id))
        ->when(auth()->user()->hasRole('Cabang'), fn($q) => $q->where('branch_id', auth()->user()->branch_id));
    
    $saleQuery = Sale::query()->selectRaw('COUNT(*) as count, SUM(total_amount) as revenue')
        ->when($this->branch_id, fn($q) => $q->where('branch_id', $this->branch_id))
        ->when(auth()->user()->hasRole('Cabang'), fn($q) => $q->where('branch_id', auth()->user()->branch_id));
    
    return [
        'rents' => $rentQuery->first() ?? (object) ['count' => 0, 'revenue' => 0],
        'sales' => $saleQuery->first() ?? (object) ['count' => 0, 'revenue' => 0],
    ];
});

$transactionChartData = computed(function () {
    $periods = match($this->period) {
        'daily' => collect(range(6, -1, -1))->map(fn($i) => now()->subDays($i)->format('Y-m-d')),
        'weekly' => collect(range(5, -1, -1))->map(fn($i) => now()->subWeeks($i)->format('Y-W')),
        'monthly' => collect(range(5, -1, -1))->map(fn($i) => now()->subMonths($i)->format('Y-m')),
    };

    $rentData = [];
    $saleData = [];

    foreach ($periods as $period) {
        $rentQuery = Rent::query()->selectRaw('COUNT(*) as count')
            ->when($this->period === 'daily', fn($q) => $q->whereDate('created_at', $period))
            ->when($this->period === 'weekly', fn($q) => $q->whereYear('created_at', explode('-', $period)[0])
                ->whereRaw('WEEK(created_at) = ?', [explode('-', $period)[1]]))
            ->when($this->period === 'monthly', fn($q) => $q->whereYear('created_at', explode('-', $period)[0])
                ->whereMonth('created_at', explode('-', $period)[1]))
            ->when($this->branch_id, fn($q) => $q->where('branch_id', $this->branch_id))
            ->when(auth()->user()->hasRole('Cabang'), fn($q) => $q->where('branch_id', auth()->user()->branch_id));

        $saleQuery = Sale::query()->selectRaw('COUNT(*) as count')
            ->when($this->period === 'daily', fn($q) => $q->whereDate('created_at', $period))
            ->when($this->period === 'weekly', fn($q) => $q->whereYear('created_at', explode('-', $period)[0])
                ->whereRaw('WEEK(created_at) = ?', [explode('-', $period)[1]]))
            ->when($this->period === 'monthly', fn($q) => $q->whereYear('created_at', explode('-', $period)[0])
                ->whereMonth('created_at', explode('-', $period)[1]))
            ->when($this->branch_id, fn($q) => $q->where('branch_id', $this->branch_id))
            ->when(auth()->user()->hasRole('Cabang'), fn($q) => $q->where('branch_id', auth()->user()->branch_id));

        $rentData[] = $rentQuery->value('count');
        $saleData[] = $saleQuery->value('count');
    }

    return [
        'labels' => $periods->map(fn($p) => match($this->period) {
            'daily' => \Carbon\Carbon::parse($p)->format('d M'),
            'weekly' => 'Minggu ' . explode('-', $p)[1],
            'monthly' => \Carbon\Carbon::createFromFormat('Y-m', $p)->format('M Y'),
        })->toArray(),
        'rentData' => $rentData,
        'saleData' => $saleData,
    ];
});

$userChartData = computed(function () {
    $roles = ['Super Admin', 'Owner', 'Cabang', 'Konsumen'];
    $data = [];

    foreach ($roles as $role) {
        $query = User::role($role)
            ->when($this->branch_id, fn($q) => $q->where('branch_id', $this->branch_id))
            ->when(auth()->user()->hasRole('Cabang'), fn($q) => $q->where('branch_id', auth()->user()->branch_id));
        $data[] = $query->count();
    }

    return [
        'labels' => $roles,
        'data' => $data,
    ];
});

$topProducts = computed(function () {
    $branchFilter = $this->branch_id ?: (auth()->user()->hasRole('Cabang') ? auth()->user()->branch_id : null);

    $rentItems = RentItem::query()
        ->selectRaw('rent_items.product_branch_id, SUM(rent_items.quantity) as total_quantity')
        ->join('rents', 'rent_items.rent_id', '=', 'rents.id')
        ->join('product_branches', 'rent_items.product_branch_id', '=', 'product_branches.id')
        ->when($branchFilter, fn($q) => is_array($branchFilter) ? $q->whereIn('product_branches.branch_id', $branchFilter) : $q->where('product_branches.branch_id', $branchFilter))
        ->groupBy('rent_items.product_branch_id');

    $saleItems = SaleItem::query()
        ->selectRaw('sale_items.product_branch_id, SUM(sale_items.quantity) as total_quantity')
        ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
        ->join('product_branches', 'sale_items.product_branch_id', '=', 'product_branches.id')
        ->when($branchFilter, fn($q) => is_array($branchFilter) ? $q->whereIn('product_branches.branch_id', $branchFilter) : $q->where('product_branches.branch_id', $branchFilter))
        ->groupBy('sale_items.product_branch_id');

    return Product::query()
        ->select('products.id', 'products.name')
        ->leftJoinSub($rentItems, 'rent_items', fn($join) => $join->on('products.id', '=', 'rent_items.product_branch_id'))
        ->leftJoinSub($saleItems, 'sale_items', fn($join) => $join->on('products.id', '=', 'sale_items.product_branch_id'))
        ->selectRaw('COALESCE(rent_items.total_quantity, 0) + COALESCE(sale_items.total_quantity, 0) as total_quantity')
        ->orderByDesc('total_quantity')
        ->take(5)
        ->get();
});
?>

<x-app>
    @volt
    <div id="kt_app_content" class="app-content flex-column-fluid py-10">
        <div class="container-fluid p-0">
            <!-- Header -->
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-4 gap-md-6 py-3">
                <div class="d-flex flex-column">
                    <h2 class="h3 fw-bold mb-0">Dashboard</h2>
                    <p class="text-muted mb-0">Ringkasan transaksi, pengguna, dan produk</p>
                </div>
                <div class="d-flex flex-column flex-sm-row gap-3 align-items-end">
                    @if(auth()->user()->hasAnyRole(['Super Admin', 'Owner']))
                    <div class="flex-grow-1 flex-sm-grow-0 w-100 w-sm-200px">
                        <x-form-select 
                            name="branch_id" 
                            class="form-select form-select-solid fw-bold"
                            :options="['' => 'Semua Cabang'] + $this->branches"
                            wire:model.live="branch_id"
                        />
                    </div>
                    @endif
                    <div class="flex-grow-1 flex-sm-grow-0 w-100 w-sm-200px">
                        <x-form-select 
                            name="period" 
                            class="form-select form-select-solid fw-bold"
                            :options="['daily' => 'Harian', 'weekly' => 'Mingguan', 'monthly' => 'Bulanan']"
                            wire:model.live="period"
                        />
                    </div>
                </div>
            </div>

            <!-- Summary Cards -->
            <div class="row g-5 mb-5">
                <div class="col-sm-6 col-xl-3">
                    <div class="card bg-light-primary card-flush h-md-100">
                        <div class="card-body">
                            <div class="d-flex flex-column">
                                <div class="d-flex align-items-center mb-1">
                                    <span class="fs-2hx fw-bold text-primary me-2">{{ $this->transactionSummary['rents']->count }}</span>
                                </div>
                                <span class="text-gray-600">Total Sewa</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-xl-3">
                    <div class="card bg-light-success card-flush h-md-100">
                        <div class="card-body">
                            <div class="d-flex flex-column">
                                <div class="d-flex align-items-center mb-1">
                                    <span class="fs-2hx fw-bold text-success me-2">Rp {{ number_format($this->transactionSummary['rents']->revenue, 0, ',', '.') }}</span>
                                </div>
                                <span class="text-gray-600">Pendapatan Sewa</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-xl-3">
                    <div class="card bg-light-warning card-flush h-md-100">
                        <div class="card-body">
                            <div class="d-flex flex-column">
                                <div class="d-flex align-items-center mb-1">
                                    <span class="fs-2hx fw-bold text-warning me-2">{{ $this->transactionSummary['sales']->count }}</span>
                                </div>
                                <span class="text-gray-600">Total Penjualan</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-xl-3">
                    <div class="card bg-light-info card-flush h-md-100">
                        <div class="card-body">
                            <div class="d-flex flex-column">
                                <div class="d-flex align-items-center mb-1">
                                    <span class="fs-2hx fw-bold text-info me-2">Rp {{ number_format($this->transactionSummary['sales']->revenue, 0, ',', '.') }}</span>
                                </div>
                                <span class="text-gray-600">Pendapatan Penjualan</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Charts -->
            <div class="row g-5 mb-5">
                <div class="col-xl-6">
                    <div class="card card-flush h-md-100">
                        <div class="card-header">
                            <h3 class="card-title">Grafik Transaksi</h3>
                        </div>
                        <div class="card-body">
                            <canvas id="transactionChart"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-xl-6">
                    <div class="card card-flush h-md-100">
                        <div class="card-header">
                            <h3 class="card-title">Distribusi Pengguna</h3>
                        </div>
                        <div class="card-body">
                            <canvas id="userChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Top Products -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Produk Paling Laku</h3>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-row-dashed table-row-gray-300 gy-7">
                            <thead>
                                <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
                                    <th class="min-w-150px">Produk</th>
                                    <th class="min-w-100px text-center">Total Terjual/Sewa</th>
                                </tr>
                            </thead>
                            <tbody class="text-gray-600 fw-semibold">
                                @forelse ($this->topProducts as $product)
                                    <tr>
                                        <td>{{ $product->name }}</td>
                                        <td class="text-center">{{ $product->total_quantity }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="2" class="text-center py-10">
                                            <span class="text-gray-600 fs-6">Tidak ada produk ditemukan</span>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            @section('custom_js')
            <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
            <script data-navigate-once>
                const transactionChart = new Chart(document.getElementById('transactionChart'), {
                    type: 'line',
                    data: {
                        labels: @json($this->transactionChartData['labels']),
                        datasets: [
                            {
                                label: 'Sewa',
                                data: @json($this->transactionChartData['rentData']),
                                borderColor: '#1BC5BD',
                                fill: false,
                            },
                            {
                                label: 'Penjualan',
                                data: @json($this->transactionChartData['saleData']),
                                borderColor: '#3699FF',
                                fill: false,
                            },
                        ],
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: { position: 'top' },
                            title: { display: true, text: 'Tren Transaksi' },
                        },
                        scales: {
                            y: { beginAtZero: true },
                        },
                    },
                });

                const userChart = new Chart(document.getElementById('userChart'), {
                    type: 'pie',
                    data: {
                        labels: @json($this->userChartData['labels']),
                        datasets: [{
                            data: @json($this->userChartData['data']),
                            backgroundColor: ['#3699FF', '#1BC5BD', '#F64E60', '#FFA800'],
                        }],
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: { position: 'top' },
                            title: { display: true, text: 'Distribusi Pengguna' },
                        },
                    },
                });
            </script>
            @endsection
        </div>
    </div>
    @endvolt
</x-app>