<?php

use App\Models\Master\Branch;
use function Laravel\Folio\name;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\Master\BranchSchedule;
use function Livewire\Volt\{mount, rules, state, usesFileUploads};

name('admin.branch-schedule');

state([
    'branches' => fn() => auth()->user()->hasAnyRole(['Super Admin', 'Owner']) 
                            ? Branch::all() 
                            : Branch::where('id', Auth::user()->branch_id)->get(),
    'selectedBranch' => '',
    'schedules' => '',
    'days' => [
        '0', '1', '2', '3', 
        '4', '5', '6'
    ],
    'dayNames' => [
        '0' => 'minggu',
        '1' => 'senin',
        '2' => 'selasa',
        '3' => 'rabu',
        '4' => 'kamis',
        '5' => 'jumat',
        '6' => 'sabtu'
    ]
]);

mount(function () {
    // Untuk non-Super Admin/Owner, langsung set cabang sesuai user
    if (!auth()->user()->hasAnyRole(['Super Admin', 'Owner'])) {
        $this->selectedBranch = Auth::user()->branch_id;
    } else {
        $this->selectedBranch = $this->branches->first()->id;
    }
    $this->loadSchedules();
});

$loadSchedules = function() {
    $this->schedules = [];
    
    foreach ($this->days as $day) {
        // Ambil jadwal terbaru untuk cabang dan hari ini
        $schedule = BranchSchedule::where('branch_id', $this->selectedBranch)
            ->where('day_of_week', $day)
            ->latest()
            ->first();
        $isOpen = $schedule ? (bool)$schedule->is_open : true;
        $this->schedules[$day] = [
            'is_open' => $isOpen, // Pastikan boolean
            'open_time' => $schedule->open_time ? substr($schedule->open_time, 0, 5) : '08:00',
            'close_time' => $schedule->close_time ? substr($schedule->close_time, 0, 5) : '17:00',
        ];
    }
};

$updatedSelectedBranch = function() {
    $this->loadSchedules();
};

$save = function() {
    DB::transaction(function () {
        foreach ($this->days as $day) {
            BranchSchedule::updateOrCreate(
                [
                    'branch_id' => $this->selectedBranch,
                    'day_of_week' => $day
                ],
                [
                    'is_open' => $this->schedules[$day]['is_open'],
                    'open_time' => $this->schedules[$day]['open_time'],
                    'close_time' => $this->schedules[$day]['close_time'],
                ]
            );
        }
    });

    $this->dispatch('toast-success', message: 'Jadwal berhasil disimpan');
};

$resetToDefault = function() {
    // Ambil semua jadwal default (dimana branch_id = null atau branch_id = 0)
    $defaultSchedules = BranchSchedule::where('branch_id', $this->selectedBranch)
        ->latest()
        ->get()
        ->keyBy('day_of_week');

    foreach ($this->days as $day) {
        // Cari jadwal default untuk hari ini
        $defaultSchedule = $defaultSchedules->get($day);

        $this->schedules[$day] = [
            'is_open' => $defaultSchedule ? (bool)$defaultSchedule->is_open : true,
            'open_time' => $defaultSchedule && $defaultSchedule->open_time 
                ? substr($defaultSchedule->open_time, 0, 5) 
                : '08:00',
            'close_time' => $defaultSchedule && $defaultSchedule->close_time 
                ? substr($defaultSchedule->close_time, 0, 5) 
                : '17:00',
        ];
    }
};

?>
<x-app>
    <style>
        .badge-open {
            background-color: rgba(26, 160, 83, 0.1);
            color: var(--success);
        }
        
        .badge-closed {
            background-color: rgba(192, 50, 33, 0.1);
            color: var(--danger);
        }
        
        .branch-selector {
            position: relative;
        }
        
        .branch-selector .dropdown-menu {
            width: 100%;
            border-radius: 12px;
            border: 1px solid var(--border);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            padding: 0.5rem;
            margin-top: 0.5rem;
        }
        
        .branch-selector .dropdown-item {
            padding: 0.75rem 1rem;
            border-radius: 8px;
            font-weight: 500;
        }
        
        .branch-selector .dropdown-item:hover, .branch-selector .dropdown-item.active {
            background-color: var(--primary-light);
            color: var(--primary);
        }
        
        .time-input-group {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .time-input-group .form-control {
            flex: 1;
        }
        
        .day-header {
            background-color: var(--primary-light);
            color: var(--primary);
            font-weight: 600;
            padding: 0.75rem;
            border-radius: 8px;
            margin-bottom: 0.5rem;
        }
        
        .mobile-schedule-card {
            border: 1px solid var(--border);
            border-radius: 12px;
            margin-bottom: 1rem;
            padding: 1rem;
        }
        
        .status-toggle {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .status-toggle-label {
            font-weight: 500;
        }
        
        .day-badge {
            background-color: var(--primary);
            color: white;
            width: 30px;
            height: 30px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-right: 0.75rem;
            font-weight: 600;
        }
        
        .action-buttons {
            display: flex;
            gap: 0.75rem;
            margin-top: 1.5rem;
            flex-wrap: wrap;
        }
        
        .empty-state {
            text-align: center;
            padding: 2rem;
            color: var(--secondary);
        }
        
        .empty-state i {
            font-size: 3rem;
            color: #D1D5E0;
            margin-bottom: 1rem;
        }
        
        .empty-state h5 {
            font-weight: 600;
            margin-bottom: 0.5rem;
        }
        
        .empty-state p {
            margin-bottom: 1.5rem;
        }
        
        /* Responsive adjustments */
        @media (max-width: 767.98px) {
            .desktop-view {
                display: none;
            }
            
            .mobile-view {
                display: block;
            }
        }
        
        @media (min-width: 768px) {
            .desktop-view {
                display: block;
            }
            
            .mobile-view {
                display: none;
            }
        }
    </style>
    <x-toolbar-mobile 
        :breadcrumbs="[
            ['icon' => 'arrow-left', 'url' => route('admin.profile')],
            ['text' => 'Jam Operasional', 'active' => true]
        ]"
    />
    @volt
    <div id="kt_app_content" class="app-content flex-column-fluid py-10">
        <!-- Pemilihan Cabang -->
        @role('Super Admin|Owner')
        <div class="row mb-4">
            <div class="col">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-store me-3 text-primary"></i>
                            <h5 class="mb-0">Pemilihan Cabang</h5>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="branch-selector">
                            <select class="form-select" wire:model="selectedBranch" wire:change="updatedSelectedBranch">
                                @foreach($branches as $branch)
                                    <option value="{{ $branch->id }}">
                                        @if($branch->is_headquarter)
                                            <span class="badge bg-primary me-2">HQ</span>
                                        @else
                                            <span class="badge bg-secondary me-2">{{ str_pad($branch->id, 2, '0', STR_PAD_LEFT) }}</span>
                                        @endif
                                        {{ $branch->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endrole
        
        <!-- Tampilan Desktop -->
        <div class="desktop-view">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-calendar-alt me-3 text-primary"></i>
                        <h5 class="mb-0">Pengaturan Jadwal</h5>
                    </div>
                    <div class="text-muted">
                        Zona Waktu: Asia/Jakarta (GMT+7)
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead>
                                <tr>
                                    <th>Hari</th>
                                    <th>Status</th>
                                    <th>Jam Buka</th>
                                    <th>Jam Tutup</th>
                                    <th>Durasi Buka</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($days as $day)
                                @php
                                    $dayName = ucfirst($dayNames[$day]);
                                    $dayInitial = strtoupper(substr($dayNames[$day], 0, 1));
                                @endphp
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <span class="day-badge">{{ $dayInitial }}</span>
                                            <div>{{ $dayName }}</div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" 
                                                wire:model="schedules.{{ $day }}.is_open" 
                                                id="{{ $dayNames[$day] }}Status">
                                            <label class="form-check-label" for="{{ $dayNames[$day] }}Status">
                                                <span class="badge {{ $schedules[$day]['is_open'] ? 'badge-open' : 'badge-closed' }}">
                                                    {{ $schedules[$day]['is_open'] ? 'Buka' : 'Tutup' }}
                                                </span>
                                            </label>
                                        </div>
                                    </td>
                                    <td>
                                        <input type="time" class="form-control" 
                                            wire:model="schedules.{{ $day }}.open_time"
                                            {{ !$schedules[$day]['is_open'] ? 'disabled' : '' }}>
                                    </td>
                                    <td>
                                        <input type="time" class="form-control" 
                                            wire:model="schedules.{{ $day }}.close_time"
                                            {{ !$schedules[$day]['is_open'] ? 'disabled' : '' }}>
                                    </td>
                                    <td class="text-muted">
                                        @if($schedules[$day]['is_open'])
                                            @php
                                                $open = \Carbon\Carbon::parse($schedules[$day]['open_time']);
                                                $close = \Carbon\Carbon::parse($schedules[$day]['close_time']);
                                                $hours = $open->diffInHours($close);
                                            @endphp
                                            {{ $hours }} jam
                                        @else
                                            Tutup
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Tampilan Mobile -->
        <div class="mobile-view">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    {{-- <div class="d-flex align-items-center">
                        <i class="fas fa-calendar-alt me-3 text-primary"></i>
                        <h5 class="mb-0">Pengaturan Jadwal</h5>
                    </div> --}}
                    <div class="text-muted small">
                        Asia/Jakarta (GMT+7)
                    </div>
                </div>
                <div class="card-body">
                    @foreach($days as $day)
                    @php
                        $dayName = ucfirst($dayNames[$day]);
                        $dayInitial = strtoupper(substr($dayNames[$day], 0, 1));
                    @endphp
                    <div class="mobile-schedule-card">
                        <div class="day-header">
                            <span class="day-badge">{{ $dayInitial }}</span> {{ $dayName }}
                        </div>
                        <div class="status-toggle mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" 
                                    wire:model="schedules.{{ $day }}.is_open" 
                                    id="mobile{{ ucfirst($dayNames[$day]) }}Status">
                            </div>
                            <div class="status-toggle-label">
                                <span class="badge {{ $schedules[$day]['is_open'] ? 'badge-open' : 'badge-closed' }}">
                                    {{ $schedules[$day]['is_open'] ? 'Buka' : 'Tutup' }}
                                </span>
                            </div>
                        </div>
                        <div class="time-input-group mb-2">
                            <label class="form-label w-100">Jam Buka</label>
                            <input type="time" class="form-control" 
                                wire:model="schedules.{{ $day }}.open_time"
                                {{ !$schedules[$day]['is_open'] ? 'disabled' : '' }}>
                        </div>
                        <div class="time-input-group">
                            <label class="form-label w-100">Jam Tutup</label>
                            <input type="time" class="form-control" 
                                wire:model="schedules.{{ $day }}.close_time"
                                {{ !$schedules[$day]['is_open'] ? 'disabled' : '' }}>
                        </div>
                        <div class="mt-2 text-muted small">
                            <i class="far fa-clock me-1"></i>
                            @if($schedules[$day]['is_open'])
                                @php
                                    $open = \Carbon\Carbon::parse($schedules[$day]['open_time']);
                                    $close = \Carbon\Carbon::parse($schedules[$day]['close_time']);
                                    $hours = $open->diffInHours($close);
                                @endphp
                                Buka selama {{ $hours }} jam
                            @else
                                Tutup
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        
        <div class="action-buttons">
            <button class="btn btn-primary" wire:click="save" wire:loading.attr="disabled">
                <span wire:loading.remove><i class="fas fa-save me-2"></i>Simpan Perubahan</span>
                <span wire:loading>Menyimpan...</span>
            </button>
            <button class="btn btn-outline-primary" wire:click="resetToDefault">
                <i class="fas fa-sync-alt me-2"></i>Reset ke Default
            </button>
        </div>
        
        @section('custom_js')
        <script data-navigate-once>
            // Toggle input waktu berdasarkan status buka/tutup
            document.querySelectorAll('.form-check-input').forEach(checkbox => {
                checkbox.addEventListener('change', function() {
                    const row = this.closest('tr');
                    const timeInputs = row ? 
                        row.querySelectorAll('input[type="time"]') : 
                        this.closest('.mobile-schedule-card').querySelectorAll('input[type="time"]');
                    
                    timeInputs.forEach(input => {
                        input.disabled = !this.checked;
                    });
                    
                    // Perbarui badge status
                    const badge = this.closest('.form-check')?.querySelector('.badge') || 
                                this.closest('.status-toggle')?.querySelector('.badge');
                    if (badge) {
                        if (this.checked) {
                            badge.classList.remove('badge-closed');
                            badge.classList.add('badge-open');
                            badge.textContent = 'Buka';
                        } else {
                            badge.classList.remove('badge-open');
                            badge.classList.add('badge-closed');
                            badge.textContent = 'Tutup';
                        }
                    }
                });
            });
            
            // Inisialisasi semua input waktu berdasarkan status awal
            document.querySelectorAll('.form-check-input').forEach(checkbox => {
                const event = new Event('change');
                checkbox.dispatchEvent(event);
            });
        </script>
        @endsection
    </div>
    @endvolt
</x-app>