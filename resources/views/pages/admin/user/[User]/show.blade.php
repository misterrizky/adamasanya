<?php
use App\Models\User;
use function Laravel\Folio\{withTrashed, name};
use App\Notifications\AccountBannedNotification;
use App\Notifications\AccountDeletedNotification;
use App\Notifications\AccountRestoredNotification;
use App\Notifications\AccountUnbannedNotification;
use App\Notifications\AccountVerifiedNotification;
use App\Notifications\AccountSuspendedNotification;
use App\Notifications\AccountUnverifiedNotification;
use App\Notifications\AccountUnsuspendedNotification;
use function Livewire\Volt\{computed, mount, state, usesPagination};
withTrashed();

name('admin.user.show');
// First get the user model instance
state([
    'user' => fn () => User::with([
        'profile',
        'userAddresses',
        'userFamilies',
        'userTags',
        'userMeters',
        'rents' => function($query) {
            return $query->with([
                'rentItems.productBranch.product',
                'branch'
            ])->orderBy('start_date');
        },
        'sales',
        'ratings.media', // Eager load media for ratings
        'sessions',
        'activityLogs',
    ])->find($user),
    // Add state for editing family tags
    'edit_family_tags' => [],
    'family_tag_inputs' => []
]);
$pending = function(){
    $this->user->st = 'pending';
    $this->user->save();
    return $this->redirect(route('admin.user.show', ['user' => $this->user]), navigate: true);
};
$verify = function(){
    $this->user->st = 'verified';
    $this->user->save();
    $this->user->notify(new AccountVerifiedNotification());
    return $this->redirect(route('admin.user.show', ['user' => $this->user]), navigate: true);
};
$unverify = function(){
    $this->user->st = 'unverified';
    $this->user->save();
    $this->user->notify(new AccountUnverifiedNotification());
    return $this->redirect(route('admin.user.show', ['user' => $this->user]), navigate: true);
};
$suspend = function(){
    $this->user->st = 'suspend';
    $this->user->save();
    $this->user->notify(new AccountSuspendedNotification());
    return $this->redirect(route('admin.user.show', ['user' => $this->user]), navigate: true);
};
$unsuspend = function(){
    $this->user->st = 'pending';
    $this->user->save();
    $this->user->notify(new AccountUnsuspendedNotification());
    return $this->redirect(route('admin.user.show', ['user' => $this->user]), navigate: true);
};
$ban = function(){
    $this->user->ban();
    $this->user->notify(new AccountBannedNotification());
    return $this->redirect(route('admin.user.show', ['user' => $this->user]), navigate: true);
};
$unban = function(){
    $this->user->unban();
    $this->user->notify(new AccountUnbannedNotification());
    return $this->redirect(route('admin.user.show', ['user' => $this->user]), navigate: true);
};
$hapus = function(){
    $this->user->delete();
    $this->user->notify(new AccountDeletedNotification());
    return $this->redirect(route('admin.user.show', ['user' => $this->user]), navigate: true);
};
$pulihkan = function(){
    $this->user->restore();
    // $this->user->notify(new AccountRestoredNotification());
    return $this->redirect(route('admin.user.show', ['user' => $this->user]), navigate: true);
};
$addTag = function() {
    $this->validate([
        'tagName' => 'required|string|max:255',
    ]);
    
    $this->user->userTags()->create([
        'name' => $this->tagName,
        'created_by' => auth()->id(),
    ]);
    
    $this->tagName = '';
    $this->dispatch('hide-modal', ['id' => 'modalAddTag']);
    $this->dispatch('show-toast', ['message' => 'Tag berhasil ditambahkan', 'type' => 'success']);
};

$deleteTag = function($id) {
    $this->user->userTags()->where('id', $id)->delete();
    $this->dispatch('show-toast', ['message' => 'Tag berhasil dihapus', 'type' => 'success']);
};
// New methods for editing family tags
$editFamilyTag = function($familyId) {
    $this->edit_family_tags[$familyId] = true;
    $family = $this->user->userFamilies->find($familyId);
    $this->family_tag_inputs[$familyId] = $family ? $family->tags : '';
};

$saveFamilyTag = function($familyId) {
    $this->validate([
        "family_tag_inputs.{$familyId}" => ['nullable', 'string', 'max:255'],
    ]);

    $family = $this->user->userFamilies->find($familyId);
    if ($family) {
        $family->castAndUpdate(['tags' => $this->family_tag_inputs[$familyId]]);
        $this->edit_family_tags[$familyId] = false;
        $this->dispatch('show-toast', ['message' => 'Tag keluarga berhasil diperbarui', 'type' => 'success']);
    } else {
        $this->dispatch('show-toast', ['message' => 'Keluarga tidak ditemukan', 'type' => 'error']);
    }
};

$cancelFamilyTag = function($familyId) {
    $this->edit_family_tags[$familyId] = false;
    $this->family_tag_inputs[$familyId] = '';
};
// Di dalam volt component
$signOutAllSessions = function()
{
    // Hapus semua session kecuali yang sedang aktif
    $currentSession = session()->getId();
    
    $this->user->sessions()
        ->where('id', '!=', $currentSession)
        ->delete();
    
    $this->dispatch('alert', [
        'type' => 'success',
        'message' => 'Semua sesi lain telah ditandai keluar'
    ]);
};

$signOutSingleSession = function($sessionId)
{
    if ($sessionId === session()->getId()) {
        $this->dispatch('alert', [
            'type' => 'error',
            'message' => 'Tidak bisa menandai keluar sesi saat ini'
        ]);
        return;
    }
    
    $this->user->sessions()->where('id', $sessionId)->delete();
    
    $this->dispatch('alert', [
        'type' => 'success',
        'message' => 'Sesi telah ditandai keluar'
    ]);
};
?>
<x-app>
    <x-toolbar-mobile 
        :breadcrumbs="[
            ['icon' => 'arrow-left', 'url' => route('admin.consumer')],
            ['text' => 'Profil ' . $user->name, 'active' => true]
        ]"
    />
    <x-toolbar 
        title="Data {{ $user->name }}"
        :breadcrumbs="[
            ['icon' => 'home', 'url' => route('admin.dashboard')],
            ['text' => 'Data Konsumen', 'url' => route('admin.consumer')],
            ['text' => $user->name, 'active' => true]
        ]"
    />
    @volt
    <div id="kt_app_content" class="app-content flex-column-fluid">
        <div class="d-flex flex-column flex-lg-row">
            <div class="flex-column flex-lg-row-auto w-lg-250px w-xl-350px mb-10">
                <div class="card mb-5 mb-xl-8">
                    <div class="card-body">
                        <div class="d-flex flex-center flex-column py-5">
                            <div class="symbol symbol-100px symbol-circle mb-7">
                                <img src="{{ $this->user->profile->image ?? $this->user->image }}" alt="image" />
                            </div>
                            <a href="#" class="fs-3 text-gray-800 text-hover-{{ $this->user->status['class'] }} fw-bold mb-3">{{ $this->user->name }}</a>
                            <div class="mb-9">
                                <div class="badge badge-lg badge-light-{{ $this->user->status['class'] }} d-inline">
                                    {{ $this->user->status['text'] }}
                                </div>
                            </div>
                            @if($this->user->getUserAchievements()->count() > 0)
                            <div class="fw-bold mb-3">
                                {{ $this->user->achievements->first()->name }}
                                <span class="ms-2" data-bs-toggle="popover" data-bs-trigger="hover" data-bs-html="true" data-bs-content="Membership">
                                    <i class="ki-outline ki-information fs-7"></i>
                                </span>
                            </div>
                            @endif
                            <div class="d-flex flex-wrap flex-center">
                                <div class="border border-gray-300 border-dashed rounded py-3 px-3 mb-3">
                                    <div class="fs-4 fw-bold text-gray-700">
                                        <span class="w-75px">{{ $this->user->getPoints() }}</span>
                                        <i class="ki-outline ki-arrow-up fs-3 text-success"></i>
                                    </div>
                                    <div class="fw-semibold text-muted">Poin</div>
                                </div>
                                <div class="border border-gray-300 border-dashed rounded py-3 px-3 mx-4 mb-3">
                                    <div class="fs-4 fw-bold text-gray-700">
                                        <span class="w-50px">{{ number_format($this->user->balance) }}</span>
                                        <i class="ki-outline ki-arrow-down fs-3 text-danger"></i>
                                    </div>
                                    <div class="fw-semibold text-muted">Saldo</div>
                                </div>
                                <div class="border border-gray-300 border-dashed rounded py-3 px-3 mb-3">
                                    <div class="fs-4 fw-bold text-gray-700">
                                        <span class="w-50px">
                                            {{ $this->user->rents->count() + $this->user->sales->count() }}
                                        </span>
                                        <i class="ki-outline ki-arrow-up fs-3 text-success"></i>
                                    </div>
                                    <div class="fw-semibold text-muted">Total Transaksi</div>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex flex-stack fs-4 py-3">
                            <div class="fw-bold rotate collapsible" data-bs-toggle="collapse" href="#detail_user_{{ $this->user->id }}" role="button" aria-expanded="false" aria-controls="detail_user_{{ $this->user->id }}">
                                Details 
                                <span class="ms-2 rotate-180">
                                    <i class="ki-outline ki-down fs-3"></i>
                                </span>
                            </div>
                            {{-- <span data-bs-toggle="tooltip" data-bs-trigger="hover" title="Edit data konsumen">
                                <a href="#" class="btn btn-sm btn-light-primary" data-bs-toggle="modal" data-bs-target="#kt_modal_update_details">Edit</a>
                            </span> --}}
                        </div>
                        <div class="separator"></div>
                        <div id="detail_user_{{ $this->user->id }}" class="collapse show">
                            <div class="pb-5 fs-6">
                                <div class="fw-bold mt-5">Account ID</div>
                                <div class="text-gray-600">ID-{{ $this->user->id }}</div>
                                <div class="fw-bold mt-5">No HP</div>
                                <div class="text-gray-600">
                                    <a href="https://wa.me/62{{ $this->user->phone }}" target="_blank" class="text-gray-600 text-hover-primary">+62{{ $this->user->phone }}</a>
                                </div>
                                <div class="fw-bold mt-5">Email</div>
                                <div class="text-gray-600">
                                    <a href="mailto:{{ $this->user->email }}" target="_blank" class="text-gray-600 text-hover-primary">{{ $this->user->email }}</a>
                                </div>
                                @if($this->user->profile)
                                <div class="fw-bold mt-5">NIK</div>
                                <a class="text-gray-600 text-hover-primary d-block overlay" data-fslightbox="lightbox-basic" href="{{ asset('storage/' . $this->user->profile->id_card) }}">
                                    <div class="overlay-wrapper">
                                        {{ $this->user->profile->nik }}
                                    </div>
                                    <div class="overlay-layer card-rounded bg-dark bg-opacity-25 shadow">
                                        <i class="bi bi-eye-fill text-white fs-3x"></i>
                                    </div>
                                </a>
                                <div class="fw-bold mt-5">Sosial Media</div>
                                <a class="text-gray-600 text-hover-primary d-block overlay mt-3 mb-3" data-fslightbox="lightbox-basic" href="{{ asset('storage/' . $this->user->profile->ig) }}">
                                    <div class="overlay-wrapper">
                                        Instagram
                                    </div>
                                    <div class="overlay-layer card-rounded bg-dark bg-opacity-25 shadow">
                                        <i class="bi bi-eye-fill text-white fs-3x"></i>
                                    </div>
                                </a>
                                <a class="text-gray-600 text-hover-primary d-block overlay" data-fslightbox="lightbox-basic" href="{{ asset('storage/' . $this->user->profile->tiktok) }}">
                                    <div class="overlay-wrapper">
                                        TikTok
                                    </div>
                                    <div class="overlay-layer card-rounded bg-dark bg-opacity-25 shadow">
                                        <i class="bi bi-eye-fill text-white fs-3x"></i>
                                    </div>
                                </a>
                                    @if($this->user->phone != $this->user->profile->wa)
                                        <div class="fw-bold mt-5">No WA</div>
                                        <div class="text-gray-600">
                                            <a href="https://wa.me/62{{ $this->user->profile->wa }}" target="_blank" class="text-gray-600 text-hover-primary">+62{{ $this->user->profile->wa }}</a>
                                        </div>
                                    @endif
                                @endif
                                @if($this->user->userAddresses->count() > 0)
                                    @php
                                    $alamat = $this->user->userAddresses->where('is_primary', true)->first();
                                    @endphp
                                    <div class="fw-bold mt-5">Alamat</div>
                                    <div class="text-gray-600">
                                        {{ $alamat->address }}, <br/>
                                        Kel. {{ $alamat->village->name }}, Kec. {{ $alamat->subdistrict->name }} <br/>
                                        {{ $alamat->city->type }} {{ $alamat->city->name }}, {{ $alamat->state->name }} {{ $alamat->village->poscode }} <br/> ({{ $alamat->notes }})
                                    </div>
                                @endif
                                <div class="fw-bold mt-5">Terakhir Masuk</div>
                                <div class="text-gray-600">
                                    @if($this->user->last_seen)
                                        {{ $this->user->last_seen->format('d M Y, h:i a') }}
                                    @else
                                        Belum pernah masuk
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card mb-5 mb-xl-8">
                    <div class="card-body">
                        <div class="d-flex flex-stack fs-4 py-3">
                            <div class="fw-bold rotate collapsible" data-bs-toggle="collapse" href="#family_user_{{ $this->user->id }}" role="button" aria-expanded="false" aria-controls="family_user_{{ $this->user->id }}">Keluarga 
                            <span class="ms-2 rotate-180">
                                <i class="ki-outline ki-down fs-3"></i>
                            </span></div>
                            {{-- <span data-bs-toggle="tooltip" data-bs-trigger="hover" title="Edit customer details">
                                <a href="#" class="btn btn-sm btn-light-primary" data-bs-toggle="modal" data-bs-target="#kt_modal_update_details">Edit</a>
                            </span> --}}
                        </div>
                        <div class="separator"></div>
                        <div id="family_user_{{ $this->user->id }}" class="collapse show">
                            <div class="pb-5 fs-6">
                                @if($this->user->profile)
                                <div class="fw-bold mt-5 mb-3">Kartu Keluarga</div>
                                <a class="d-block overlay" data-fslightbox="lightbox-basic" href="{{ asset('storage/' . $this->user->profile->family_card) }}">
                                    <div class="overlay-wrapper bgi-no-repeat bgi-position-center bgi-size-cover card-rounded min-h-175px"
                                        style="background-image:url('{{ asset('storage/' . $this->user->profile->family_card) }}')">
                                    </div>
                                    <div class="overlay-layer card-rounded bg-dark bg-opacity-25 shadow">
                                        <i class="bi bi-eye-fill text-white fs-3x"></i>
                                    </div>
                                </a>
                                @endif
                                @if($this->user->userFamilies->count() > 0)
                                    @foreach ($this->user->userFamilies as $item)
                                    <div class="fw-bold mt-5">{{ $item->name }} ({{ $item->type }})</div>
                                    <div class="text-gray-600">
                                        <a href="https://wa.me/62{{ $item->phone }}" target="_blank" class="text-gray-600 text-hover-primary">+62{{ $item->phone }}</a>
                                    </div>
                                    <div class="fw-bold mt-5">Tag</div>
                                    <div class="text-gray-600 d-flex align-items-center">
                                        @if(isset($this->edit_family_tags[$item->id]) && $this->edit_family_tags[$item->id])
                                            <x-form-input 
                                                type="text" 
                                                name="family_tag_inputs.{{ $item->id }}" 
                                                class="bg-transparent w-50" 
                                                label="Tag Keluarga" 
                                                placeholder="Masukkan tag" 
                                                wire:model="family_tag_inputs.{{ $item->id }}"
                                            />
                                            <div class="ms-3">
                                                <button wire:click="saveFamilyTag({{ $item->id }})" class="btn btn-icon btn-light btn-sm border-0" aria-label="Save">
                                                    <i class="ki-filled ki-check fs-2 text-success"></i>
                                                </button>
                                                <button wire:click="cancelFamilyTag({{ $item->id }})" class="btn btn-icon btn-light btn-sm border-0" aria-label="Batalkan">
                                                    <i class="ki-filled ki-cross fs-2 text-danger"></i>
                                                </button>
                                            </div>
                                        @else
                                            <span class="text-gray-600 fs-6">
                                                {{ $item->tags ?? 'Belum diisi' }}
                                            </span>
                                            <button wire:click="editFamilyTag({{ $item->id }})" class="btn btn-icon btn-light btn-sm border-0 ms-3" aria-label="Edit">
                                                <i class="ki-outline ki-arrow-right fs-2 text-primary"></i>
                                            </button>
                                        @endif
                                    </div>
                                    @endforeach
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card mb-5 mb-xl-8 d-none">
                    <div class="card-header border-0">
                        <div class="card-title">
                            <h3 class="fw-bold m-0">Akun Terhubung</h3>
                        </div>
                    </div>
                    <div class="card-body pt-2">
                        {{-- <div class="notice d-flex bg-light-primary rounded border-primary border border-dashed mb-9 p-6">
                            <i class="ki-outline ki-design-1 fs-2tx text-primary me-4"></i>
                            <div class="d-flex flex-stack flex-grow-1">
                                <div class="fw-semibold">
                                    <div class="fs-6 text-gray-700">By connecting an account, you hereby agree to our 
                                    <a href="#" class="me-1">privacy policy</a>and 
                                    <a href="#">terms of use</a>.</div>
                                </div>
                            </div>
                        </div> --}}
                        <div class="py-2">
                            <div class="d-flex flex-stack">
                                <div class="d-flex">
                                    <img src="{{asset('media/svg/brand-logos/google-icon.svg')}}" class="w-30px me-6" alt="" />
                                    <div class="d-flex flex-column">
                                        <a href="#" class="fs-5 text-gray-900 text-hover-primary fw-bold">Google</a>
                                        <div class="fs-6 fw-semibold text-muted">Plan properly your workflow</div>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-end">
                                    <label class="form-check form-switch form-switch-sm form-check-custom form-check-solid">
                                        <input class="form-check-input" name="google" type="checkbox" value="1" id="kt_modal_connected_accounts_google" checked="checked" />
                                        <span class="form-check-label fw-semibold text-muted" for="kt_modal_connected_accounts_google"></span>
                                    </label>
                                </div>
                            </div>
                            <div class="separator separator-dashed my-5"></div>
                            <div class="d-flex flex-stack">
                                <div class="d-flex">
                                    <img src="{{asset('media/svg/brand-logos/github.svg')}}" class="w-30px me-6" alt="" />
                                    <div class="d-flex flex-column">
                                        <a href="#" class="fs-5 text-gray-900 text-hover-primary fw-bold">Github</a>
                                        <div class="fs-6 fw-semibold text-muted">Keep eye on on your Repositories</div>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-end">
                                    <label class="form-check form-switch form-switch-sm form-check-custom form-check-solid">
                                        <input class="form-check-input" name="github" type="checkbox" value="1" id="kt_modal_connected_accounts_github" checked="checked" />
                                        <span class="form-check-label fw-semibold text-muted" for="kt_modal_connected_accounts_github"></span>
                                    </label>
                                </div>
                            </div>
                            <div class="separator separator-dashed my-5"></div>
                            <div class="d-flex flex-stack">
                                <div class="d-flex">
                                    <img src="{{asset('media/svg/brand-logos/slack-icon.svg')}}" class="w-30px me-6" alt="" />
                                    <div class="d-flex flex-column">
                                        <a href="#" class="fs-5 text-gray-900 text-hover-primary fw-bold">Slack</a>
                                        <div class="fs-6 fw-semibold text-muted">Integrate Projects Discussions</div>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-end">
                                    <label class="form-check form-switch form-switch-sm form-check-custom form-check-solid">
                                        <input class="form-check-input" name="slack" type="checkbox" value="1" id="kt_modal_connected_accounts_slack" />
                                        <span class="form-check-label fw-semibold text-muted" for="kt_modal_connected_accounts_slack"></span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    {{-- <div class="card-footer border-0 d-flex justify-content-center pt-0">
                        <button class="btn btn-sm btn-light-primary">Save Changes</button>
                    </div> --}}
                </div>
            </div>
            <div class="flex-lg-row-fluid ms-lg-15">
                <ul class="nav nav-custom nav-tabs nav-line-tabs nav-line-tabs-2x border-0 fs-4 fw-semibold mb-8">
                    <li class="nav-item">
                        <a class="nav-link text-active-primary pb-4 active" data-bs-toggle="tab" href="#ringkasan_user_{{ $this->user->id }}">Ringkasan</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-active-primary pb-4" data-bs-toggle="tab" href="#aktivitas_user_{{ $this->user->id }}">Aktivitas</a>
                    </li>
                    <li class="nav-item ms-auto">
                        <a href="#" class="btn btn-primary ps-7" data-kt-menu-trigger="click" data-kt-menu-attach="parent" data-kt-menu-placement="bottom-end">
                            Aksi 
                            <i class="ki-outline ki-down fs-2 me-0"></i>
                        </a>
                        <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-800 menu-state-bg-light-primary fw-semibold py-4 w-250px fs-6" data-kt-menu="true">
                            <div class="menu-item px-5">
                                <div class="menu-content text-muted pb-2 px-5 fs-7 text-uppercase">Akun</div>
                            </div>
                            @if($this->user->st == "pending")
                            <div class="menu-item px-5">
                                <a onclick="verify();" class="menu-link px-5">Verifikasi</a>
                            </div>
                            <div class="menu-item px-5 my-1">
                                <a onclick="unverify();" class="menu-link px-5">Tidak Verifikasi</a>
                            </div>
                            @elseif($this->user->st == "verified" || $this->user->st == "unverified")
                            <div class="menu-item px-5 my-1">
                                <a onclick="menungguKeputusan();" class="menu-link px-5">Pending</a>
                            </div>
                            @endif
                            @role('Super Admin|Owner')
                                @if($this->user->st != "suspend")
                                <div class="menu-item px-5">
                                    <a onclick="suspend();" class="menu-link px-5">Suspend</a>
                                </div>
                                @else
                                <div class="menu-item px-5">
                                    <a onclick="unsuspend();" class="menu-link px-5">Aktifkan Kembali</a>
                                </div>
                                @endif
                                @if($this->user->isBanned())
                                <div class="menu-item px-5">
                                    <a onclick="unban();" class="menu-link px-5">Aktifkan Kembali</a>
                                </div>
                                @else
                                <div class="menu-item px-5 my-1">
                                    <a onclick="ban();" class="menu-link px-5">DPO</a>
                                </div>
                                @endif
                            @endrole
                                @if($this->user->trashed())
                                <div class="menu-item px-5">
                                    <a onclick="pulihkan();" class="menu-link px-5">Pulihkan Data</a>
                                </div>
                                @elseif(!$this->user->trashed())
                                <div class="menu-item px-5">
                                    <a onclick="hapus();" class="menu-link px-5">Hapus Data</a>
                                </div>
                                @endif
                        </div>
                    </li>
                </ul>
                <div class="tab-content" id="myTabContent">
                    <div class="tab-pane fade show active" id="ringkasan_user_{{ $this->user->id }}" role="tabpanel">
                        <div class="card card-flush mb-6 mb-xl-9">
                            @php
                            // Buat array tanggal unik dari jadwal sewa
                            $uniqueDates = $this->user->rents
                                ->flatMap(function($rent) {
                                    return [
                                        $rent->start_date->format('Y-m-d'),
                                        $rent->end_date->format('Y-m-d')
                                    ];
                                })
                                ->unique()
                                ->sort()
                                ->values();
                            
                            // Ambil 3 tanggal terdekat (hari ini dan 2 hari berikutnya)
                            $today = now()->format('Y-m-d');
                            $nextDays = [
                                now()->addDay()->format('Y-m-d'),
                                now()->addDays(2)->format('Y-m-d'),
                                now()->addDays(3)->format('Y-m-d'),
                                now()->addDays(4)->format('Y-m-d'),
                                now()->addDays(5)->format('Y-m-d'),
                                now()->addDays(6)->format('Y-m-d'),
                                now()->addDays(7)->format('Y-m-d'),
                                now()->addDays(8)->format('Y-m-d'),
                                now()->addDays(9)->format('Y-m-d'),
                                now()->addDays(10)->format('Y-m-d'),
                                now()->addDays(11)->format('Y-m-d')
                            ];
                            
                            $displayDates = array_unique(array_merge([$today], $nextDays));
                            @endphp
                            <div class="card-header mt-6">
                                <div class="card-title flex-column">
                                    <h2 class="mb-1">Jadwal Sewa</h2>
                                    <div class="fs-6 fw-semibold text-muted">{{ $this->user->rents->where('start_date', '>=', $today)->count() }} jadwal sewa</div>
                                </div>
                                <div class="card-toolbar">
                                    {{-- <button type="button" class="btn btn-light-primary btn-sm" data-bs-toggle="modal" data-bs-target="#kt_modal_add_schedule">
                                        <i class="ki-outline ki-brush fs-3"></i>Tambah Jadwal
                                    </button> --}}
                                </div>
                            </div>
                            <div class="card-body p-9 pt-4">
                                <ul class="nav nav-pills d-flex flex-nowrap hover-scroll-x py-2">
                                    @foreach($displayDates as $index => $date)
                                        @php
                                            $dateObj = \Carbon\Carbon::parse($date);
                                            $dayName = $dateObj->isoFormat('dd')[0]; // Ambil huruf pertama nama hari
                                            $dayNumber = $dateObj->format('d');
                                            $hasRentals = $this->user->rents->contains(function($rent) use ($date) {
                                                return $rent->start_date->format('Y-m-d') == $date || 
                                                    $rent->end_date->format('Y-m-d') == $date;
                                            });
                                        @endphp
                                        <li class="nav-item me-1">
                                            <a class="nav-link btn d-flex flex-column flex-center rounded-pill min-w-40px me-2 py-4 
                                                    {{ $index === 0 ? 'btn-active-primary active' : '' }} 
                                                    {{ $hasRentals ? 'border border-primary' : '' }}"
                                            data-bs-toggle="tab" href="#kt_schedule_day_{{ $index }}">
                                                <span class="opacity-50 fs-7 fw-semibold">{{ $dayName }}</span>
                                                <span class="fs-6 fw-bolder">{{ $dayNumber }}</span>
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>

                                <div class="tab-content">
                                    @foreach($displayDates as $index => $date)
                                        <div id="kt_schedule_day_{{ $index }}" class="tab-pane fade {{ $index === 0 ? 'show active' : '' }}">
                                            @php
                                                $rentalsOnDate = $this->user->rents->filter(function($rent) use ($date) {
                                                    return $rent->start_date->format('Y-m-d') == $date || 
                                                        $rent->end_date->format('Y-m-d') == $date;
                                                })->sortBy('start_date');
                                            @endphp

                                            @if($rentalsOnDate->count() > 0)
                                                @foreach($rentalsOnDate as $rent)
                                                    @php
                                                        $isPickup = $rent->start_date->format('Y-m-d') == $date;
                                                        $isReturn = $rent->end_date->format('Y-m-d') == $date;
                                                        $color = $isPickup ? 'success' : 'danger';
                                                        $time = $isPickup ? $rent->start_time : $rent->end_time;
                                                        $actionText = $isPickup ? 'Pengambilan' : 'Pengembalian';
                                                    @endphp
                                                    <div class="d-flex flex-stack position-relative mt-6">
                                                        <div class="position-absolute h-100 w-4px bg-{{ $color }} rounded top-0 start-0"></div>
                                                        <div class="fw-semibold ms-5">
                                                            <div class="fs-7 mb-1">
                                                                {{ $time->format('h:i') }}
                                                                <span class="fs-7 text-muted text-uppercase">{{ $time->format('H') >= 12 ? 'pm' : 'am' }}</span>
                                                                <span class="badge badge-light-{{ $color }} ms-2">{{ $actionText }}</span>
                                                            </div>
                                                            <a href="{{ route('admin.rent.show', ['rent' => $rent->id]) }}" class="fs-5 fw-bold text-gray-900 text-hover-primary mb-2">
                                                                {{ $rent->rentItems->first()->productBranch->product->name ?? 'Produk' }}
                                                            </a>
                                                            <div class="fs-7 text-muted">
                                                                Sewa di cabang
                                                                <a href="#">{{ $rent->branch->name }}</a>
                                                            </div>
                                                        </div>
                                                        <a wire:navigate href="{{ route('admin.rent.show', ['rent' => $rent->id]) }}" class="btn btn-light bnt-active-light-primary btn-sm">Lihat</a>
                                                    </div>
                                                @endforeach
                                            @else
                                                <div class="text-center py-10">
                                                    <div class="mb-5">
                                                        <img src="{{ asset('media/illustrations/sketchy-1/5.png') }}" class="w-100px" alt="">
                                                    </div>
                                                    <h4>Tidak ada jadwal sewa</h4>
                                                    <p class="text-muted">Tidak ada jadwal sewa pada tanggal ini</p>
                                                </div>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        <div class="card card-flush mb-6 mb-xl-9">
                            <div class="card-header mt-6">
                                <div class="card-title flex-column">
                                    <h2 class="mb-1">Tag Konsumen</h2>
                                    <div class="fs-6 fw-semibold text-muted">Total {{ $this->user->userTags->count() }} tag</div>
                                </div>
                                <div class="card-toolbar">
                                    <button type="button" class="btn btn-light-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalAddTag">
                                        <i class="ki-outline ki-add-files fs-3"></i>Tambah Tag
                                    </button>
                                </div>
                            </div>
                            <div class="card-body d-flex flex-column">
                                @if($this->user->userTags->count() > 0)
                                    @foreach($this->user->userTags as $tag)
                                    <div class="d-flex align-items-center position-relative mb-7">
                                        <div class="position-absolute top-0 start-0 rounded h-100 bg-secondary w-4px"></div>
                                        <div class="fw-semibold ms-5">
                                            <a href="#" class="fs-5 fw-bold text-gray-900 text-hover-primary">
                                                {{ $tag->name }}
                                            </a>
                                            <div class="fs-7 text-muted">
                                                Ditambahkan pada {{ $tag->created_at->format('d M Y') }}
                                            </div>
                                        </div>
                                        <button type="button" 
                                                class="btn btn-icon btn-active-light-primary w-30px h-30px ms-auto"
                                                wire:click="deleteTag({{ $tag->id }})"
                                                wire:confirm="Hapus tag ini?">
                                            <i class="ki-outline ki-trash fs-3"></i>
                                        </button>
                                    </div>
                                    @endforeach
                                @else
                                    <div class="text-center py-10">
                                        <div class="mb-5">
                                            <img src="{{ asset('media/illustrations/sketchy-1/17.png') }}" class="w-100px" alt="">
                                        </div>
                                        <h4>Belum ada tag</h4>
                                        <p class="text-muted">Tambahkan tag untuk mengkategorikan konsumen ini</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="aktivitas_user_{{ $this->user->id }}" role="tabpanel">
                        <div class="card pt-4 mb-6 mb-xl-9">
                            <div class="card-header border-0">
                                <div class="card-title">
                                    <h2>Sesi Masuk</h2>
                                </div>
                                <div class="card-toolbar">
                                    <button type="button" class="btn btn-sm btn-flex btn-light-primary"  wire:click="signOutAllSessions">
                                        <i class="ki-outline ki-entrance-right fs-3"></i>
                                        Sign out all sessions
                                    </button>
                                </div>
                            </div>
                            <div class="card-body pt-0 pb-5">
                                <div class="table-responsive">
                                    <table class="table align-middle table-row-dashed gy-5" id="kt_table_users_login_session">
                                        <thead class="border-bottom border-gray-200 fs-7 fw-bold">
                                            <tr class="text-start text-muted text-uppercase gs-0">
                                                <th class="min-w-100px">Device</th>
                                                <th>IP Address</th>
                                                <th class="min-w-125px">Terakhir Aktif</th>
                                                <th class="min-w-70px">Status</th>
                                                <th class="min-w-70px text-end">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody class="fs-6 fw-semibold text-gray-600">
                                            @forelse($this->user->sessions as $session)
                                                <tr>
                                                    <td>
                                                        @php
                                                            // Parse user agent untuk mendapatkan info device
                                                            $agent = new Jenssegers\Agent\Agent();
                                                            $agent->setUserAgent($session->user_agent);
                                                        @endphp
                                                        {{ $agent->browser() }} - {{ $agent->platform() }}
                                                    </td>
                                                    <td>{{ $session->ip_address }}</td>
                                                    <td>
                                                        {{ \Carbon\Carbon::createFromTimestamp($session->last_activity)->format('d M Y, H:i') }}
                                                        <span class="text-muted d-block fs-7">
                                                            {{ \Carbon\Carbon::createFromTimestamp($session->last_activity)->diffForHumans() }}
                                                        </span>
                                                    </td>
                                                    <td>
                                                        @if($session->id === session()->getId())
                                                            <span class="badge badge-light-success">Saat ini</span>
                                                        @else
                                                            <span class="badge badge-light-{{ \Carbon\Carbon::createFromTimestamp($session->last_activity)->diffInHours() < 1 ? 'primary' : 'warning' }}">
                                                                {{ \Carbon\Carbon::createFromTimestamp($session->last_activity)->diffInHours() < 1 ? 'Aktif' : 'Kadaluarsa' }}
                                                            </span>
                                                        @endif
                                                    </td>
                                                    <td class="text-end">
                                                        @if($session->id !== session()->getId())
                                                            <a href="#" wire:click.prevent="signOutSingleSession('{{ $session->id }}')">Hapus Sesi</a>
                                                        @else
                                                            Saat ini
                                                        @endif
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="5" class="text-center py-10">
                                                        <div class="mb-5">
                                                            <img src="{{ asset('media/illustrations/sketchy-1/9.png') }}" class="w-100px" alt="">
                                                        </div>
                                                        <h4>Tidak ada sesi aktif</h4>
                                                        <p class="text-muted">Pengguna belum pernah masuk atau sesi telah kadaluarsa</p>
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="card pt-4 mb-6 mb-xl-9">
                            <div class="card-header border-0">
                                <div class="card-title">
                                    <h2>Riwayat Aktivitas</h2>
                                </div>
                                <div class="card-toolbar">
                                    <button type="button" class="btn btn-sm btn-light-primary">
                                        <i class="ki-outline ki-cloud-download fs-3"></i>Download Report
                                    </button>
                                </div>
                            </div>
                            <div class="card-body py-0">
                                <div class="table-responsive">
                                    <table class="table align-middle table-row-dashed fw-semibold text-gray-600 fs-6 gy-5" id="kt_table_users_logs">
                                        <thead class="border-bottom border-gray-200 fs-7 fw-bold">
                                            <tr class="text-start text-muted text-uppercase gs-0">
                                                <th class="min-w-100px">Aktivitas</th>
                                                <th>Deskripsi</th>
                                                <th class="min-w-125px">Model</th>
                                                <th class="min-w-125px">Waktu</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($this->user->activityLogs as $log)
                                            <tr>
                                                <td class="min-w-70px">
                                                    <div class="badge badge-light-{{ $log->event === 'created' ? 'success' : ($log->event === 'updated' ? 'primary' : 'warning') }}">
                                                        {{ ucfirst($log->event) }}
                                                    </div>
                                                </td>
                                                <td>
                                                    {{ $log->description }}
                                                    @if($log->properties)
                                                        <div class="text-muted fs-7 mt-1">
                                                            @foreach($log->properties as $key => $value)
                                                                @if(!in_array($key, ['attributes', 'old']))
                                                                    {{ $key }}: {{ is_array($value) ? json_encode($value) : $value }}<br>
                                                                @endif
                                                            @endforeach
                                                        </div>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($log->subject_type)
                                                        {{ class_basename($log->subject_type) }}
                                                    @else
                                                        N/A
                                                    @endif
                                                </td>
                                                <td class="pe-0 text-end min-w-200px">
                                                    {{ $log->created_at->format('d M Y, H:i') }}
                                                    <span class="text-muted d-block fs-7">
                                                        {{ $log->created_at->diffForHumans() }}
                                                    </span>
                                                </td>
                                            </tr>
                                            @empty
                                            <tr>
                                                <td colspan="4" class="text-center py-10">
                                                    <div class="mb-5">
                                                        <img src="{{ asset('media/illustrations/sketchy-1/9.png') }}" class="w-100px" alt="">
                                                    </div>
                                                    <h4>Tidak ada riwayat aktivitas</h4>
                                                    <p class="text-muted">Belum ada aktivitas yang tercatat untuk pengguna ini</p>
                                                </td>
                                            </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="card pt-4 mb-6 mb-xl-9">
                            <div class="card-header border-0">
                                <div class="card-title">
                                    <h2>Transaksi</h2>
                                </div>
                                <div class="card-toolbar">
                                    <button type="button" class="btn btn-sm btn-light-primary">
                                        <i class="ki-outline ki-cloud-download fs-3"></i>Download Report
                                    </button>
                                </div>
                            </div>
                            <div class="card-body py-0">
                                <div class="table-responsive">
                                    <table class="table align-middle table-row-dashed fs-6 text-gray-600 fw-semibold gy-5" id="kt_table_customers_transactions">
                                        <thead class="border-bottom border-gray-200 fs-7 fw-bold">
                                            <tr class="text-start text-muted text-uppercase gs-0">
                                                <th class="min-w-100px">Tipe</th>
                                                <th>Kode</th>
                                                <th class="min-w-125px">Tanggal</th>
                                                <th>Produk</th>
                                                <th>Total</th>
                                                <th>Status</th>
                                                <th>Metode</th>
                                                <th class="min-w-70px">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($this->user->rents as $rent)
                                            <tr>
                                                <td>
                                                    <span class="badge badge-light-primary">Sewa</span>
                                                </td>
                                                <td>
                                                    <a wire:navigate href="{{ route('admin.rent.show', ['rent' => $rent->id]) }}" class="fw-bold text-gray-900 text-hover-primary">
                                                        {{ $rent->code }}
                                                    </a>
                                                </td>
                                                <td>
                                                    {{ $rent->start_date->format('d M Y') }}
                                                    <span class="text-muted d-block fs-7">
                                                        {{ $rent->start_date->format('H:i') }}
                                                    </span>
                                                </td>
                                                <td>
                                                    @foreach($rent->rentItems as $item)
                                                        {{ $item->productBranch->product->name }}<br>
                                                    @endforeach
                                                </td>
                                                <td class="text-end">
                                                    {{ number_format($rent->total_price) }}
                                                </td>
                                                <td>
                                                    <span class="badge {{ $rent->status['class'] }}">
                                                        {{ $rent->status['text'] }}
                                                    </span>
                                                </td>
                                                <td>
                                                    {{ ucfirst($rent->payment_type) }}
                                                </td>
                                                <td class="text-end">
                                                    <a wire:navigate href="{{ route('admin.rent.show', ['rent' => $rent->id]) }}" class="btn btn-sm btn-light-primary">Detail</a>
                                                </td>
                                            </tr>
                                            @empty
                                            <tr>
                                                <td colspan="8" class="text-center py-10">
                                                    <div class="mb-5">
                                                        <img src="{{ asset('media/illustrations/sketchy-1/9.png') }}" class="w-100px" alt="">
                                                    </div>
                                                    <h4>Tidak ada transaksi sewa</h4>
                                                    <p class="text-muted">Pengguna belum pernah melakukan transaksi sewa</p>
                                                </td>
                                            </tr>
                                            @endforelse

                                            @forelse($this->user->sales as $sale)
                                            <tr>
                                                <td>
                                                    <span class="badge badge-light-success">Beli</span>
                                                </td>
                                                <td>
                                                    <a wire:navigate href="{{ route('admin.sale.show', ['sale' => $sale->id]) }}" class="fw-bold text-gray-900 text-hover-primary">
                                                        {{ $sale->code }}
                                                    </a>
                                                </td>
                                                <td>
                                                    {{ $sale->sale_date->format('d M Y') }}
                                                    <span class="text-muted d-block fs-7">
                                                        {{ $sale->sale_date->format('H:i') }}
                                                    </span>
                                                </td>
                                                <td>
                                                    @foreach($sale->saleItems as $item)
                                                        {{ $item->productBranch->product->name }}<br>
                                                    @endforeach
                                                </td>
                                                <td class="text-end">
                                                    {{ number_format($sale->total_price) }}
                                                </td>
                                                <td>
                                                    <span class="badge badge-light-{{ $sale->status === 'completed' ? 'success' : ($sale->status === 'pending' ? 'warning' : 'danger') }}">
                                                        {{ ucfirst($sale->status) }}
                                                    </span>
                                                </td>
                                                <td>
                                                    {{ ucfirst($sale->payment_type) }}
                                                </td>
                                                <td class="text-end">
                                                    <a wire:navigate href="{{ route('admin.sale.show', ['sale' => $sale->id]) }}" class="btn btn-sm btn-light-primary">Detail</a>
                                                </td>
                                            </tr>
                                            @empty
                                            <tr>
                                                <td colspan="8" class="text-center py-10">
                                                    <div class="mb-5">
                                                        <img src="{{ asset('media/illustrations/sketchy-1/9.png') }}" class="w-100px" alt="">
                                                    </div>
                                                    <h4>Tidak ada transaksi pembelian</h4>
                                                    <p class="text-muted">Pengguna belum pernah melakukan transaksi pembelian</p>
                                                </td>
                                            </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Modal Tambah Tag -->
        <div wire:ignore.self class="modal fade" id="modalAddTag" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered mw-650px">
                <div class="modal-content">
                    <div class="modal-header">
                        <h2 class="fw-bold">Tambah Tag Baru</h2>
                        <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal" aria-label="Close">
                            <i class="ki-outline ki-cross fs-1"></i>
                        </div>
                    </div>
                    <form wire:submit.prevent="addTag">
                        <div class="modal-body py-10 px-lg-17">
                            <div class="fv-row mb-7">
                                <label class="fs-6 fw-semibold form-label mb-2">
                                    <span class="required">Nama Tag</span>
                                </label>
                                <input type="text" class="form-control form-control-solid" wire:model="tagName" placeholder="Contoh: Konsumen Prioritas" required />
                            </div>
                        </div>
                        <div class="modal-footer flex-center">
                            <button type="button" class="btn btn-light me-3" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-primary">
                                <span class="indicator-label">Simpan</span>
                                <span class="indicator-progress">Please wait...
                                    <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                                </span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @section('custom_js')
        <script data-navigate-once>
        function menungguKeputusan() {
            Swal.fire({
                title: 'Set Status ke menunggu Keputusan?',
                html: 'Apakah Anda yakin ingin mengubah status pengguna ini menjadi <b>menunggu Keputusan</b>?<br><small>Pengguna akan memiliki akses terbatas sampai diverifikasi</small>',
                icon: 'question', // Ikon pertanyaan untuk aksi netral
                showCancelButton: true,
                confirmButtonColor: '#17a2b8', // Warna biru teal untuk aksi tengah
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Set ke menunggu Keputusan',
                cancelButtonText: 'Batalkan',
                reverseButtons: true,
                backdrop: true,
                allowOutsideClick: false,
                focusConfirm: true
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Mengubah Status...',
                        html: 'Sedang memproses perubahan status ke menunggu Keputusan',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                            @this.pending().then(() => {
                                Swal.fire({
                                    title: 'Berhasil!',
                                    html: 'Status pengguna diubah ke <b>menunggu Keputusan</b>',
                                    icon: 'success',
                                    timer: 2500,
                                    timerProgressBar: true,
                                    showConfirmButton: false
                                });
                            }).catch(error => {
                                Swal.fire({
                                    title: 'Gagal!',
                                    html: `Gagal mengubah status: <br><span class="text-red-500">${error.message}</span>`,
                                    icon: 'error',
                                    confirmButtonText: 'Mengerti'
                                });
                            });
                        }
                    });
                }
            });
        }
        function verify() {
            Swal.fire({
                title: 'Verifikasi Pengguna?',
                html: 'Apakah Anda yakin ingin memverifikasi akun ini?<br><small>Pengguna akan mendapatkan akses penuh setelah diverifikasi</small>',
                icon: 'question', // Ikon question untuk aksi konfirmasi
                showCancelButton: true,
                confirmButtonColor: '#28a745', // Hijau untuk aksi positif
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Verifikasi',
                cancelButtonText: 'Batalkan',
                reverseButtons: true,
                backdrop: true,
                allowOutsideClick: false,
                focusConfirm: true
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Memverifikasi...',
                        html: 'Sedang memproses verifikasi akun',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                            @this.verify().then(() => {
                                Swal.fire({
                                    title: 'Terverifikasi!',
                                    text: 'Akun pengguna berhasil diverifikasi',
                                    icon: 'success',
                                    timer: 2500,
                                    timerProgressBar: true,
                                    showConfirmButton: false
                                });
                            }).catch(error => {
                                Swal.fire({
                                    title: 'Gagal Verifikasi!',
                                    html: `Terjadi kesalahan: <br><span class="text-red-500">${error.message}</span>`,
                                    icon: 'error',
                                    confirmButtonText: 'Mengerti'
                                });
                            });
                        }
                    });
                }
            });
        }
        function unverify() {
            Swal.fire({
                title: 'Cabut Verifikasi Pengguna?',
                html: 'Apakah Anda yakin ingin mencabut status verifikasi akun ini?<br><small>Pengguna mungkin kehilangan akses tertentu</small>',
                icon: 'warning', // Ikon warning untuk aksi penting
                showCancelButton: true,
                confirmButtonColor: '#ffc107', // Kuning untuk aksi peringatan
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Cabut Verifikasi',
                cancelButtonText: 'Batalkan',
                reverseButtons: true,
                backdrop: true,
                allowOutsideClick: false,
                focusCancel: true // Auto-focus ke cancel untuk prevent misclick
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Mencabut Verifikasi...',
                        html: 'Sedang memproses pencabutan status verifikasi',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                            @this.unverify().then(() => {
                                Swal.fire({
                                    title: 'Status Dicabut!',
                                    text: 'Verifikasi akun berhasil dicabut',
                                    icon: 'success',
                                    timer: 2500,
                                    timerProgressBar: true,
                                    showConfirmButton: false
                                });
                            }).catch(error => {
                                Swal.fire({
                                    title: 'Gagal Mencabut!',
                                    html: `Terjadi kesalahan: <br><span class="text-red-500">${error.message}</span>`,
                                    icon: 'error',
                                    confirmButtonText: 'Mengerti'
                                });
                            });
                        }
                    });
                }
            });
        }
        function suspend() {
            Swal.fire({
                title: 'Tangguhkan Pengguna?',
                text: 'Apakah Anda yakin ingin menangguhkan akun pengguna ini?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ffc107', // Kuning untuk aksi peringatan
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Tangguhkan',
                cancelButtonText: 'Batalkan',
                reverseButtons: true,
                backdrop: true,
                allowOutsideClick: false,
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Memproses...',
                        html: 'Sedang menangguhkan akun pengguna',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                            @this.suspend().then(() => {
                                Swal.fire({
                                    title: 'Berhasil!',
                                    text: 'Akun pengguna berhasil di-tangguhkan',
                                    icon: 'success',
                                    timer: 2000,
                                    timerProgressBar: true
                                });
                            }).catch(error => {
                                Swal.fire({
                                    title: 'Gagal!',
                                    html: `Gagal menangguhkan pengguna: <br><span class="text-red-500">${error.message}</span>`,
                                    icon: 'error'
                                });
                            });
                        }
                    });
                }
            });
        }
        function unsuspend() {
            Swal.fire({
                title: 'Aktifkan Kembali Pengguna?',
                text: 'Apakah Anda yakin ingin mengaktifkan kembali akun pengguna ini?',
                icon: 'success', // Ikon hijau untuk aksi positif
                showCancelButton: true,
                confirmButtonColor: '#28a745', // Hijau untuk aksi pemulihan
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Aktifkan',
                cancelButtonText: 'Batalkan',
                reverseButtons: true,
                backdrop: true,
                allowOutsideClick: false,
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Memproses...',
                        html: 'Sedang mengaktifkan kembali akun pengguna',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                            @this.unsuspend().then(() => {
                                Swal.fire({
                                    title: 'Berhasil!',
                                    text: 'Akun pengguna berhasil diaktifkan kembali',
                                    icon: 'success',
                                    timer: 2000,
                                    timerProgressBar: true,
                                    showConfirmButton: false
                                });
                            }).catch(error => {
                                Swal.fire({
                                    title: 'Gagal!',
                                    html: `Gagal mengaktifkan pengguna: <br><span class="text-red-500">${error.message}</span>`,
                                    icon: 'error'
                                });
                            });
                        }
                    });
                }
            });
        }
        function ban() {
            Swal.fire({
                title: 'Blokir Pengguna ?',
                text: 'Apakah Anda yakin ingin memblokir pengguna ini ? Tindakan ini tidak dapat dibatalkan',
                icon: 'info',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Hapus',
                cancelButtonText: 'Batalkan',
                reverseButtons: true,
                backdrop: true,
                allowOutsideClick: false,
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Blokir Data...',
                        html: 'Sedang memproses permintaan Anda',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                            @this.ban().then(() => {
                                Swal.fire({
                                    title: 'Berhasil!',
                                    text: 'Data berhasil di blokir',
                                    icon: 'success',
                                    timer: 2000,
                                    timerProgressBar: true
                                });
                            }).catch(error => {
                                Swal.fire({
                                    title: 'Gagal!',
                                    html: `Gagal memblokir data: <br><span class="text-red-500">${error.message}</span>`,
                                    icon: 'error'
                                });
                            });
                        }
                    });
                }
            });
        }
        function unban() {
            Swal.fire({
                title: 'Buka Blokir Pengguna?',
                text: 'Apakah Anda yakin ingin membuka blokir pengguna ini?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#28a745', // Hijau untuk aksi positif
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Buka Blokir',
                cancelButtonText: 'Batalkan',
                reverseButtons: true,
                backdrop: true,
                allowOutsideClick: false,
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Memproses...',
                        html: 'Sedang membuka blokir pengguna',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                            @this.unban().then(() => {
                                Swal.fire({
                                    title: 'Berhasil!',
                                    text: 'Pengguna berhasil dibuka blokir',
                                    icon: 'success',
                                    timer: 2000,
                                    timerProgressBar: true
                                });
                            }).catch(error => {
                                Swal.fire({
                                    title: 'Gagal!',
                                    html: `Gagal membuka blokir pengguna: <br><span class="text-red-500">${error.message}</span>`,
                                    icon: 'error'
                                });
                            });
                        }
                    });
                }
            });
        }
        function hapus() {
            Swal.fire({
                title: 'Hapus Akun Permanen?',
                text: 'Semua data pengguna akan dihapus selamanya dan tidak dapat dikembalikan. Yakin ingin melanjutkan?',
                icon: 'error', // Ikon merah untuk aksi berbahaya
                showCancelButton: true,
                confirmButtonColor: '#d33', // Merah untuk aksi destruktif
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Hapus Permanen',
                cancelButtonText: 'Batalkan',
                reverseButtons: true,
                backdrop: true,
                allowOutsideClick: false,
                focusCancel: true, // Auto-focus ke tombol cancel untuk prevent misclick
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Menghapus Akun...',
                        html: 'Sedang menghapus semua data pengguna',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                            @this.hapus().then(() => {
                                Swal.fire({
                                    title: 'Terhapus!',
                                    text: 'Akun pengguna telah dihapus permanen',
                                    icon: 'success',
                                    timer: 3000, // Timer lebih panjang untuk aksi penting
                                    timerProgressBar: true,
                                    showConfirmButton: false
                                });
                            }).catch(error => {
                                Swal.fire({
                                    title: 'Gagal Menghapus!',
                                    html: `Terjadi kesalahan: <br><span class="text-red-500">${error.message}</span>`,
                                    icon: 'error',
                                    confirmButtonText: 'Mengerti'
                                });
                            });
                        }
                    });
                }
            });
        }
        function pulihkan() {
            Swal.fire({
                title: 'Pulihkan Akun Ini?',
                text: 'Akun yang telah dihapus akan dikembalikan beserta semua data yang terkait',
                icon: 'info', // Ikon biru untuk aksi pemulihan
                showCancelButton: true,
                confirmButtonColor: '#17a2b8', // Warna teal untuk aksi pemulihan
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Pulihkan',
                cancelButtonText: 'Batalkan',
                reverseButtons: true,
                backdrop: true,
                allowOutsideClick: false,
                focusConfirm: true // Auto-focus ke tombol konfirmasi
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Memulihkan Akun...',
                        html: 'Sedang mengembalikan data pengguna',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                            @this.pulihkan().then(() => {
                                Swal.fire({
                                    title: 'Berhasil Dipulihkan!',
                                    text: 'Akun pengguna telah aktif kembali',
                                    icon: 'success',
                                    timer: 2500,
                                    timerProgressBar: true,
                                    showConfirmButton: false
                                });
                            }).catch(error => {
                                Swal.fire({
                                    title: 'Gagal Memulihkan!',
                                    html: `Terjadi kesalahan: <br><span class="text-red-500">${error.message}</span>`,
                                    icon: 'error',
                                    confirmButtonText: 'Mengerti'
                                });
                            });
                        }
                    });
                }
            });
        }
        
        document.addEventListener('DOMContentLoaded', () => {
            // konsumenMapInstance.init();
            refreshFsLightbox();
        });

        document.addEventListener('livewire:navigated', () => {
            // konsumenMapInstance.destroy();
            // konsumenMapInstance.init();
            refreshFsLightbox();
        });
    </script>
    @endsection
    @endvolt
    <!--end::Content-->
</x-app>