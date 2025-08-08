<?php
use App\Models\Master\Branch;
use App\Models\Master\BranchSchedule;
use function Livewire\Volt\{state, computed};
use function Laravel\Folio\{name};
name('branch');

state(['search' => '', 'sortColumn' => 'id', 'sortDirection' => 'asc']);

$sort = function($columnName) {
    $this->sortColumn = $columnName;
    $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
};

$branches = computed(function() {
    $branches = Branch::where('st', 'a')
        ->when($this->search, function($query) {
            $query->where('name', 'like', '%'.$this->search.'%')
                  ->orWhere('address', 'like', '%'.$this->search.'%')
                  ->orWhere('city', 'like', '%'.$this->search.'%');
        })
        ->orderBy($this->sortColumn, $this->sortDirection)
        ->get();

    // Add today's schedule information to each branch
    $today = date('w'); // 0 (Sunday) to 6 (Saturday)
    $currentTime = date('H:i:s');
    
    return $branches->map(function($branch) use ($today, $currentTime) {
        $schedule = BranchSchedule::where('branch_id', $branch->id)
            ->where('day_of_week', $today)
            ->first();
            
        if ($schedule) {
            $branch->is_open_today = $schedule->is_open;
            $branch->open_time = $schedule->open_time;
            $branch->close_time = $schedule->close_time;
            
            // Calculate time differences
            $timeToClose = strtotime($schedule->close_time) - strtotime($currentTime);
            $isClosingSoon = $timeToClose > 0 && $timeToClose <= 3600; // Within 1 hour
            
            // Determine status
            $branch->is_currently_open = $schedule->is_open && 
                $currentTime >= $schedule->open_time && 
                $currentTime <= $schedule->close_time;
                
            $branch->is_closing_soon = $isClosingSoon;
            $branch->is_closed_for_today = $schedule->is_open && 
                $currentTime > $schedule->close_time;
                
            // Format operating hours
            $branch->operating_hours = $schedule->is_open 
                ? date('H:i', strtotime($schedule->open_time)).' - '.date('H:i', strtotime($schedule->close_time))
                : 'Tutup';
                
            // Determine status text and styling
            if (!$schedule->is_open) {
                $branch->status_text = 'Tutup';
                $branch->status_class = 'danger';
                $branch->status_icon = 'cross-circle';
            } elseif ($branch->is_currently_open) {
                if ($branch->is_closing_soon) {
                    $branch->status_text = 'Segera Tutup';
                    $branch->status_class = 'warning';
                    $branch->status_icon = 'clock';
                } else {
                    $branch->status_text = 'Buka';
                    $branch->status_class = 'success';
                    $branch->status_icon = 'check-circle';
                }
            } elseif ($branch->is_closed_for_today) {
                $branch->status_text = 'Tutup';
                $branch->status_class = 'danger';
                $branch->status_icon = 'cross-circle';
            } else {
                // Will open later today
                $branch->status_text = 'Akan Buka';
                $branch->status_class = 'info';
                $branch->status_icon = 'clock';
            }
        } else {
            $branch->is_open_today = false;
            $branch->status_text = 'Tutup';
            $branch->status_class = 'danger';
            $branch->status_icon = 'cross-circle';
            $branch->operating_hours = 'Tutup';
        }
        
        return $branch;
    });
});
?>

<x-app>
    @volt
    <div class="d-flex flex-column flex-column-fluid">
        <!-- Main Content -->
        <div id="kt_app_content" class="app-content flex-column-fluid">
            <div class="container">
                <!-- Search and Filter -->
                <div class="d-flex flex-wrap justify-content-between align-items-center mb-10">
                    <h2 class="fw-bold fs-2hx text-gray-900 mb-5">Cabang Kami</h2>
                    
                    <div class="d-flex flex-wrap gap-5">
                        <div class="position-relative w-250px">
                            <i class="ki-outline ki-magnifier position-absolute top-50 translate-middle-y ms-4">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>
                            <input type="text" wire:model.live="search" class="form-control form-control-solid ps-12" placeholder="Cari cabang...">
                        </div>
                    </div>
                </div>

                <!-- Branches List -->
                <div class="row g-10">
                    @foreach($this->branches as $branch)
                    <div class="col-md-6 col-lg-4">
                        <div class="card card-flush h-100 branch-card">
                            <div class="card-body p-9">
                                <div class="d-flex flex-column h-100">
                                    <!-- Branch Image -->
                                    <div class="mb-7 text-center">
                                        <div class="symbol symbol-100px symbol-circle mb-5">
                                            <img src="{{ asset('media/icons/logo.png') }}" alt="{{ $branch->name }}" class="rounded-3">
                                        </div>
                                        
                                        <!-- Branch Name -->
                                        <h3 class="fs-2 text-gray-900 fw-bold mb-3">{{ $branch->name }}</h3>
                                        <!-- Branch Status Badge -->
                                        <div class="d-flex justify-content-center mb-5">
                                            <span class="badge badge-light-{{ $branch->status_class }}">
                                                <i class="ki-outline ki-{{ $branch->status_icon }} fs-2 text-{{ $branch->status_class }} me-2"></i>
                                                {{ $branch->status_text }}
                                                @if($branch->is_closing_soon)
                                                    ({{ date('H:i', strtotime($branch->close_time)) }})
                                                @endif
                                            </span>
                                        </div>
                                        <!-- Branch Location -->
                                        <div class="mb-5">
                                            <span class="text-gray-600 fw-semibold d-block">
                                                <i class="ki-outline ki-geolocation fs-4 me-2"></i>
                                                {{ $branch->address }}, Kel. {{ $branch->village->name }}, Kec. {{ $branch->subdistrict->name }}, {{ $branch->city->name }}, {{ $branch->state->name }}, {{ $branch->country->name }} {{ $branch->village->poscode }}
                                            </span>
                                        </div>
                                    </div>
                                    
                                    <!-- Branch Info -->
                                    <div class="mb-7">
                                        <div class="d-flex justify-content-between mb-2">
                                            <span class="fw-semibold text-gray-600">Jam Operasional:</span>
                                            <span class="fw-bold text-gray-700">
                                                {{ $branch->operating_hours }}
                                            </span>
                                        </div>
                                        <div class="d-flex justify-content-between">
                                            <span class="fw-semibold text-gray-600">Status:</span>
                                            <span class="fw-bold text-gray-700">
                                                @if($branch->is_currently_open)
                                                    <span class="text-success">Buka</span> (Tutup pukul {{ date('H:i', strtotime($branch->close_time)) }})
                                                @elseif($branch->is_open_today)
                                                    <span class="text-warning">Akan buka</span> pukul {{ date('H:i', strtotime($branch->open_time)) }}
                                                @else
                                                    <span class="text-danger">Tutup</span>
                                                @endif
                                            </span>
                                        </div>
                                    </div>
                                    
                                    <!-- Action Button -->
                                    <div class="mt-auto">
                                        <a href="{{ route('branch.show', ['branch' => $branch]) }}" wire:navigate class="btn btn-primary w-100">
                                            <i class="ki-outline ki-information fs-2 me-2"></i> Lihat Detail
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>

                @if($this->branches->isEmpty())
                <div class="text-center py-15">
                    <img src="{{ asset('media/illustrations/sigma-1/18.png') }}" class="w-200px mb-5" alt="No Branches">
                    <h3 class="text-gray-600">Cabang tidak ditemukan</h3>
                    <p class="text-muted">Silakan cari dengan kata kunci lain</p>
                </div>
                @endif
            </div>
        </div>
    </div>
    @endvolt
</x-app>