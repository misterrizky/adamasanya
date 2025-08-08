<?php

use App\Models\User;
use function Laravel\Folio\name;
use Cmgmyr\Messenger\Models\Thread;
use Cmgmyr\Messenger\Models\Participant;
use function Livewire\Volt\{computed, mount, state, usesPagination};
usesPagination(theme: 'bootstrap');
name('admin.consumer');
state(['search', 'layout', 'status'])->url();
state(['sortColumn' => '','sortDirection' => 'ASC']);

$sort = function($columnName){
    $this->sortColumn = $columnName;
    $this->sortDirection = $this->sortDirection == 'ASC' ? 'ASC' : 'DESC';
};
mount(function () {
    $this->layout = 'grid';
});
$totalCustomers = computed(function(){
    $query = User::select('users.*')
            ->role(['Konsumen','Onboarding'])
            ->withTrashed()
            ->withCount('rents');
    
    $role = Auth::user()->getRoleNames()[0];

    if($this->search) {
        $query->where(function($q) {
            $q->where('name', 'like', '%'.$this->search.'%')
                ->orWhere('email', 'like', '%'.$this->search.'%')
                ->orWhere('phone', 'like', '%'.$this->search.'%');
        });
    }

    if($role == "Cabang" || $role == "Pegawai") {
        // $query->where('branch_id', Auth::user()->branch_id);
    }

    return $query->count();
});
$collection = computed(function(){
    $query = User::select('users.*')
            ->role(['Konsumen','Onboarding'])
            ->withTrashed()
            ->withCount('rents');
    
    $role = Auth::user()->getRoleNames()[0];

    // Search filter
    if($this->search) {
        $query->where(function($q) {
            $q->where('name', 'like', '%'.$this->search.'%')
                ->orWhere('email', 'like', '%'.$this->search.'%')
                ->orWhere('phone', 'like', '%'.$this->search.'%');
        });
    }

    // Role filter
    if($role == "Super Admin" || $role == "Owner" || $role == "Konsumen") {
        // $query->where('', Auth::user()->profile->house_id);
    } elseif($role == "Cabang" || $role == "Pegawai") {
        // $query->where('branch_id', Auth::user()->branch_id);
    }

    // Status filter
    // $query->where('st', $this->status);

    // Default sort
    if(!$this->sortColumn) {
        $this->sortColumn = 'id';
        $this->sortDirection = 'ASC';
    }

    return $query->orderBy('id', 'DESC')
        ->paginate(9);
});
$verified = function(User $user) {
    $user->st = 'verified';
    $user->verified_at = now();
    $user->save();
    $this->dispatch('toast-success', message: "Konsumen berhasil diverifikasi.");
    return $this->redirect(route('admin.consumer'), navigate: true);
};
$unverified = function(User $user) {
    $user->st = 'unverified';
    $user->verified_at = now();
    $user->save();
    $this->dispatch('toast-success', message: "Konsumen berhasil diverifikasi.");
    return $this->redirect(route('admin.consumer'), navigate: true);
};
$suspend = function(User $user) {
    $user->st = 'suspend';
    $user->save();
    $this->dispatch('toast-success', message: "Konsumen berhasil ditangguhkan.");
    return $this->redirect(route('admin.consumer'), navigate: true);
};
$pending = function(User $user) {
    $user->st = 'pending';
    $user->verified_at = null;
    $user->save();
    $this->dispatch('toast-success', message: "Status berhasil diubah menjadi Pending.");
    return $this->redirect(route('admin.consumer'), navigate: true);
};
$ban = function(User $user) {
    $user->ban();
    $this->dispatch('toast-success', message: "Konsumen telah dijadikan DPO.");
    return $this->redirect(route('admin.consumer'), navigate: true);
};
$unban = function(User $user) {
    $user->unban();
    $this->dispatch('toast-info', message: "Status DPO berhasil ditangguhkan.");
    return $this->redirect(route('admin.consumer'), navigate: true);
};
$delete = function(User $user) {
    $user->delete();
    $this->dispatch('toast-success', message: "Konsumen berhasil dihapus.");
    return $this->redirect(route('admin.consumer'), navigate: true);
};
$restore = function($id){
    $user = User::withTrashed()->find($id);
    $user->restore();
    $this->dispatch('toast-info', message: "Konsumen berhasil dipulihkan.");
    return $this->redirect(route('admin.consumer'), navigate: true);
};
$checkExistingThread = function($userId){
    $currentUserId = Auth::id();
    $thread = Thread::whereHas('participants', function ($query) use ($currentUserId) {
        $query->where('user_id', $currentUserId);
    })->whereHas('participants', function ($query) use ($userId) {
        $query->where('user_id', $userId);
    })->first();

    return $thread;
};
$createNewThread = function($userId){
    $currentUserId = Auth::id();
    $thread = Thread::create([
        'subject' => 'Percakapan antara ' . Auth::user()->name . ' dan ' . User::find($userId)->name,
    ]);

    Participant::create([
        'thread_id' => $thread->id,
        'user_id' => $currentUserId,
    ]);

    Participant::create([
        'thread_id' => $thread->id,
        'user_id' => $userId,
    ]);

    return $thread;
};
$startChat = function($userId){
    $thread = $this->checkExistingThread($userId);
    if (!$thread) {
        $thread = $this->createNewThread($userId);
    }
}
?>
<x-app>
    @volt
    <div id="kt_app_content" class="app-content flex-column-fluid py-10">
        <x-advance-search/>
        <div class="d-flex flex-wrap flex-stack mb-5">
            <div class="d-flex flex-wrap align-items-center my-1">
                <h3 class="fw-bold me-5 my-1">
                    {{ $this->totalCustomers > 0 ? number_format($this->totalCustomers) . ' Data Ditemukan' : 'Tidak ada data ditemukan' }}
                </h3>
            </div>
            <div class="d-flex flex-wrap my-1">
                <input type="radio" class="btn-check" name="layout" wire:model.live="layout" value="grid" id="layout_grid" />
                <label class="btn btn-sm btn-icon btn-light btn-color-muted btn-active-primary me-3" for="layout_grid">
                    <i class="ki-outline ki-element-plus fs-2"></i>
                </label>
                <input type="radio" class="btn-check" name="layout" wire:model.live="layout" value="list" id="layout_list" />
                <label class="btn btn-sm btn-icon btn-light btn-color-muted btn-active-primary me-3" for="layout_list">
                    <i class="ki-outline ki-row-horizontal fs-2"></i>
                </label>
                <div class="d-flex my-0">
                    <select name="status" data-control="select2" data-hide-search="true" data-placeholder="Filter" class="form-select form-select-sm form-select-solid w-150px">
                        <option value="1">Urutkan berdasarkan A-Z</option>
                        <option value="2">Urutkan berdasarkan Z-A</option>
                    </select>
                    <button type="button" class="btn btn-icon btn-sm btn-light-primary" data-bs-toggle="modal" data-bs-target="#ModalExport">
                        <i class="ki-outline ki-exit-up fs-2"></i>
                    </button>
                </div>
            </div>
        </div>
        @if($this->layout == "grid")
        <div class="row row-cols-1 row-cols-md-2 row-cols-xl-3 g-5 g-xl-9 mb-5">
            @foreach($this->collection as $item)
            <div class="col-md-4">
                <a href="{{ route('admin.user.show', ['user' => $item]) }}" wire:navigate class="card card-flush border-hover-{{ $item->status['class'] }} h-md-100">
                    <div class="card-header border-0 pt-9">
                        <div class="card-title m-0">
                            <div class="symbol symbol-50px w-50px bg-light">
                                <img src="{{ $item->profile->image ?? $item->image }}" alt="image" class="p-3" />
                            </div>
                        </div>
                        <div class="card-toolbar">
                            <span class="badge badge-light-{{ $item->status['class'] }} fw-bold me-auto px-4 py-3">
                                {{ $item->status['text'] }}
                            </span>
                        </div>
                    </div>
                    <div class="card-body p-9">
                        <div class="fs-3 fw-bold text-gray-900">{{ $item->name }}</div>
                        <p class="text-gray-500 fw-semibold fs-5 mt-1 mb-7">
                            +62{{ $item->phone }} <br/>
                            {{ $item->email }}
                        </p>
                        <div class="d-flex flex-wrap mb-5">
                            <div class="border border-gray-300 border-dashed rounded min-w-125px py-3 px-4 me-7 mb-3">
                                <div class="fs-6 text-gray-800 fw-bold">{{ $item->created_at->format('j F Y') }}</div>
                                <div class="fw-semibold text-gray-500">Tanggal Daftar</div>
                            </div>
                            @if($item->isBanned())
                            <div class="border border-gray-300 border-dashed rounded min-w-125px py-3 px-4 mb-3">
                                <div class="fs-6 text-gray-800 fw-bold">{{ $item->banned_at ? $item->banned_at->format('j F Y') : '' }}</div>
                                <div class="fw-semibold text-gray-500">Tanggal Di Banned</div>
                            </div>
                            @elseif($item->deleted_at)
                            <div class="border border-gray-300 border-dashed rounded min-w-125px py-3 px-4 mb-3">
                                <div class="fs-6 text-gray-800 fw-bold">{{ $item->deleted_at ? $item->deleted_at->format('j F Y') : '' }}</div>
                                <div class="fw-semibold text-gray-500">Tanggal Hapus Akun</div>
                            </div>
                            @elseif($item->verified_at)
                            <div class="border border-gray-300 border-dashed rounded min-w-125px py-3 px-4 mb-3">
                                <div class="fs-6 text-gray-800 fw-bold">{{ $item->verified_at ? $item->verified_at->format('j F Y') : '' }}</div>
                                <div class="fw-semibold text-gray-500">Tanggal Verifikasi</div>
                            </div>
                            @endif
                        </div>
                    </div>
                </a>
            </div>
            @endforeach
        </div>
        {{ $this->collection->links() }}
        @elseif($this->layout == "list")
        <div class="card">
            <div class="card-body py-4">
                <div class="table-responsive">
                    <table class="table align-middle table-row-dashed fs-6 gy-5" id="table_customer">
                        <thead>
                            <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
                                <th class="min-w-100px">Nama</th>
                                <th class="text-center min-w-100px d-none d-md-table-cell">Tanggal Daftar</th>
                                <th class="text-center min-w-100px d-none d-md-table-cell">Tanggal Verifikasi</th>
                                <th class="text-center min-w-100px d-none d-md-table-cell">Status</th>
                                <th class="text-end min-w-100px">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="text-gray-600 fw-semibold">
                            @foreach ($this->collection as $item)
                                <tr class="{{ $loop->odd ? 'bg-light' : '' }}">
                                    <td class="d-flex align-items-center">
                                        <div class="symbol symbol-circle symbol-50px overflow-hidden me-3">
                                            <a href="{{ route('admin.user.show', ['user' => $item]) }}" wire:navigate>
                                                <div class="symbol-label">
                                                    <img src="{{ $item->profile->image ?? $item->image }}" alt="{{ $item->name }}" class="w-100">
                                                </div>
                                            </a>
                                        </div>
                                        <div class="d-flex flex-column">
                                            <a href="{{ route('admin.user.show', ['user' => $item]) }}" wire:navigate 
                                            class="text-gray-800 text-hover-primary mb-1">
                                                {{ $item->name }}
                                            </a>
                                            <span class="badge badge-light-{{ $item->status['class'] }} fs-8 align-self-start fw-bold py-1 px-2 mb-1 d-block d-md-none">
                                                {{ $item->status['text'] }}
                                            </span>
                                            <div class="d-flex flex-column text-muted fs-7">
                                                <span>+62{{ $item->phone }}</span>
                                                <span>{{ $item->email }}</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-center d-none d-md-table-cell">
                                        {{ $item->created_at->format('j F Y') }}
                                    </td>
                                    <td class="text-center d-none d-md-table-cell">
                                        @if($item->isBanned())
                                            {{ $item->banned_at?->format('j F Y') ?? '-' }}
                                        @elseif($item->deleted_at)
                                            {{ $item->deleted_at?->format('j F Y') ?? '-' }}
                                        @elseif($item->verified_at)
                                            {{ $item->verified_at?->format('j F Y') ?? '-' }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td class="text-center d-none d-md-table-cell">
                                        <span class="badge badge-light-{{ $item->status['class'] }} fs-7 fw-bold py-2 px-3">
                                            {{ $item->status['text'] }}
                                        </span>
                                    </td>
                                    <td class="text-end">
                                        <div class="d-flex justify-content-end align-items-center">
                                            @role('Owner|Super Admin')
                                                <div class="d-flex flex-wrap gap-2">
                                                    <a href="{{ route('admin.user.show', ['user' => $item]) }}" wire:navigate 
                                                    class="btn btn-icon btn-sm btn-light-primary d-none d-md-inline-flex"
                                                    data-bs-toggle="tooltip" data-bs-placement="top" aria-label="Lihat Data" title="Lihat Data">
                                                        <i class="ki-outline ki-magnifier fs-2"></i>
                                                    </a>
                                                    <button wire:click="startChat({{$item->id}})" class="btn btn-icon btn-sm btn-light-success" data-bs-toggle="tooltip" data-bs-placement="top" aria-label="Chat" title="Chat dengan {{ $item->name }}">
                                                        <i class="ki-outline ki-message-text fs-2"></i>
                                                    </button>
                                                    
                                                    @if($item->st == "pending")
                                                        <button wire:click="verified({{ $item }})"
                                                            class="btn btn-icon btn-sm btn-light-success"
                                                            wire:confirm="Apakah Anda yakin ingin verifikasi {{ $item->name }}?"
                                                            data-bs-toggle="tooltip" data-bs-placement="top" aria-label="Verifikasi" title="Verifikasi">
                                                            <i class="ki-outline ki-user-tick fs-2"></i>
                                                        </button>
                                                        <button wire:click="unverified({{ $item }})" 
                                                            class="btn btn-icon btn-sm btn-light-danger"
                                                            wire:confirm="Apakah Anda yakin untuk tidak verifikasi {{ $item->name }}?"
                                                            data-bs-toggle="tooltip" data-bs-placement="top" aria-label="Tidak Verifikasi" title="Tidak Verifikasi">
                                                            <i class="ki-outline ki-cross fs-2"></i>
                                                        </button>
                                                    @elseif($item->st != "pending")
                                                        @if($item->isNotBanned())
                                                            <button wire:click="suspend({{ $item }})"
                                                                class="btn btn-icon btn-sm btn-light-warning"
                                                                wire:confirm="Apakah Anda yakin ingin menangguhkan {{ $item->name }}?"
                                                                data-bs-toggle="tooltip" data-bs-placement="top" aria-label="Suspend" title="Suspend">
                                                                <i class="ki-outline ki-user-edit fs-2"></i>
                                                            </button>
                                                            <button wire:click="pending({{ $item }})" 
                                                                class="btn btn-icon btn-sm btn-light-info"
                                                                wire:confirm="Apakah Anda yakin untuk ubah status {{ $item->name }} menjadi pending?"
                                                                data-bs-toggle="tooltip" data-bs-placement="top" aria-label="Pending" title="Pending">
                                                                <i class="ki-outline ki-abstract-18 fs-2"></i>
                                                            </button>
                                                            <button wire:click="ban({{ $item }})"
                                                                class="btn btn-icon btn-sm btn-light-danger"
                                                                wire:confirm="Apakah Anda yakin ingin menjadikan {{ $item->name }} DPO?"
                                                                data-bs-toggle="tooltip" data-bs-placement="top" aria-label="DPO" title="DPO">
                                                                <i class="ki-outline ki-lock fs-2"></i>
                                                            </button>
                                                        @else
                                                            <button wire:click="unban({{ $item }})"
                                                                class="btn btn-icon btn-sm btn-light-primary"
                                                                wire:confirm="Apakah Anda yakin ingin menangguhkan status DPO {{ $item->name }}?"
                                                                data-bs-toggle="tooltip" data-bs-placement="top" aria-label="Cabut DPO" title="Cabut DPO">
                                                                <i class="ki-outline ki-user-square fs-2"></i>
                                                            </button>
                                                        @endif
                                                        
                                                        @if($item->deleted_at)
                                                            <button wire:click="restore({{ $item->id }})"
                                                                class="btn btn-icon btn-sm btn-light-success"
                                                                wire:confirm="Apakah Anda yakin ingin memulihkan {{ $item->name }}?"
                                                                data-bs-toggle="tooltip" data-bs-placement="top" aria-label="Pulihkan Data" title="Pulihkan Data">
                                                                <i class="ki-outline ki-arrows-circle fs-2"></i>
                                                            </button>
                                                        @else
                                                            <button wire:click="delete({{ $item }})"
                                                                class="btn btn-icon btn-sm btn-light-danger"
                                                                wire:confirm="Apakah Anda yakin ingin menghapus {{ $item->name }}?"
                                                                data-bs-toggle="tooltip" data-bs-placement="top" aria-label="Hapus Data" title="Hapus Data">
                                                                <i class="ki-outline ki-trash fs-2"></i>
                                                            </button>
                                                        @endif
                                                    @endif
                                                </div>
                                            @elserole('Cabang')
                                                <a href="{{ route('admin.user.show', ['user' => $item]) }}" wire:navigate 
                                                class="btn btn-icon btn-sm btn-light-warning">
                                                    <i class="ki-outline ki-notepad-edit fs-2"></i>
                                                </a>
                                                <button wire:click="startChat({{$item->id}})" class="btn btn-icon btn-sm btn-light-success ms-5" data-bs-toggle="tooltip" data-bs-placement="top" aria-label="Chat" title="Chat dengan {{ $item->name }}">
                                                    <i class="ki-outline ki-message-text fs-2"></i>
                                                </button>
                                            @endrole
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    {{ $this->collection->links() }}
                </div>
            </div>
        </div>
        @endif
    </div>
    @endvolt
</x-app>