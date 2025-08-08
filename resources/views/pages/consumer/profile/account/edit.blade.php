<?php
use Illuminate\Support\Str;
use function Laravel\Folio\name;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use function Livewire\Volt\{mount, state, usesFileUploads};
use App\Models\UserProfile; // Import UserProfile model for creating profiles

name('profile.edit');
usesFileUploads();

state(['user' => fn() => \App\Models\User::where('id', Auth::user()->id)->first()]);
state([
    'avatar' => '',
    'remove_avatar' => '',
    'nama' => '',
    'email' => '',
    'phone' => '',
    'nik' => '',
    'pob' => '',
    'bod' => '',
    'gender' => '',
    'id_card' => '',
    'family_card' => '',
    'ig' => '',
    'tiktok' => '',
    'wa' => '',
    
    'edit_nama' => false,
    'edit_email' => false,
    'edit_phone' => false,
    'edit_nik' => false,
    'edit_ttl' => false,
    'edit_gender' => false,
    'edit_ktp' => false,
    'edit_kk' => false,
    'edit_ig' => false,
    'edit_tiktok' => false,
    'edit_wa' => false,
]);

mount(function () {
    $this->avatar = $this->user->avatar ?? '';
    $this->nama = $this->user->name ?? '';
    $this->email = $this->user->email ?? '';
    $this->phone = $this->user->phone ?? '';
    $this->nik = $this->user->profile ? $this->user->profile->nik : '';
    $this->pob = $this->user->profile ? $this->user->profile->pob : '';
    $this->bod = $this->user->profile && $this->user->profile->bod ? $this->user->profile->bod->format('Y-m-d') : '';
    $this->gender = $this->user->profile ? $this->user->profile->gender : '';
    $this->id_card = $this->user->profile ? $this->user->profile->id_card : '';
    $this->family_card = $this->user->profile ? $this->user->profile->family_card : '';
    $this->ig = $this->user->profile ? $this->user->profile->ig : '';
    $this->tiktok = $this->user->profile ? $this->user->profile->tiktok : '';
    $this->wa = $this->user->profile ? $this->user->profile->wa : '';
});

$updatedAvatar = function() {
    $this->validate([
        'avatar' => ['nullable', 'image', 'mimes:png,jpg,jpeg', 'max:2048'],
    ]);

    if ($this->avatar) {
        $path = $this->avatar->store('avatar', 'public');
        
        if (File::exists($this->avatar->getRealPath())) {
            File::delete($this->avatar->getRealPath());
        }
        
        if ($this->user->avatar && Storage::disk('public')->exists($this->user->avatar)) {
            Storage::disk('public')->delete($this->user->avatar);
        }
        
        $this->user->castAndUpdate(['avatar' => $path]);
    }
};

$removeAvatar = function() {
    try {
        $avatarPath = Auth::user()->avatar;
        if ($avatarPath && Storage::disk('public')->exists($avatarPath)) {
            Storage::disk('public')->delete($avatarPath);
            $this->user->castAndUpdate(['avatar' => null]);
            logger()->info("Avatar deleted successfully: {$avatarPath}");
        }
    } catch (\Exception $e) {
        logger()->error("Failed to delete avatar: " . $e->getMessage());
    }
};

$editNama = function() { $this->edit_nama = true; };
$saveNama = function() {
    $this->validate(['nama' => ['required', 'string', 'max:255']]);
    $this->user->castAndUpdate(['name' => $this->nama]);
    $this->edit_nama = false;
};

$editEmail = function() { $this->edit_email = true; };
$saveEmail = function() {
    $this->validate(['email' => ['required', 'email', 'max:255', 'unique:users,email,' . $this->user->id]]);
    $this->user->castAndUpdate(['email' => $this->email]);
    $this->edit_email = false;
};

$editPhone = function() { $this->edit_phone = true; };
$savePhone = function() {
    $this->validate(['phone' => ['required', 'string', 'max:15', 'unique:users,phone,' . $this->user->id]]);
    $this->user->castAndUpdate(['phone' => $this->phone]);
    $this->edit_phone = false;
};

$editNik = function() { $this->edit_nik = true; };
$saveNik = function() {
    $this->validate(['nik' => ['required', 'string', 'max:16']]);
    
    if (!$this->user->profile) {
        $this->user->profile = UserProfile::castAndCreate(['user_id' => $this->user->id]);
    }
    
    $this->user->profile->castAndUpdate(['nik' => $this->nik]);
    $this->edit_nik = false;
};

$editTtl = function() { $this->edit_ttl = true; };
$saveTtl = function() {
    $this->validate([
        'pob' => ['required', 'string', 'max:255'],
        'bod' => ['required', 'date'],
    ]);
    
    if (!$this->user->profile) {
        $this->user->profile = UserProfile::castAndCreate(['user_id' => $this->user->id]);
    }
    
    $this->user->profile->castAndUpdate([
        'pob' => $this->pob,
        'bod' => $this->bod,
    ]);
    $this->edit_ttl = false;
};

$editGender = function() { $this->edit_gender = true; };
$saveGender = function() {
    $this->validate(['gender' => ['required', 'in:pria,wanita']]);
    
    if (!$this->user->profile) {
        $this->user->profile = UserProfile::castAndCreate(['user_id' => $this->user->id]);
    }
    
    $this->user->profile->castAndUpdate(['gender' => $this->gender]);
    $this->edit_gender = false;
};

$editKtp = function() { $this->edit_ktp = true; };
$cancelKtp = function() { $this->edit_ktp = false; };
$saveKtp = function() {
    $this->validate(['id_card' => ['nullable', 'image', 'mimes:png,jpg,jpeg', 'max:2048']]);
    
    if ($this->id_card) {
        if (!$this->user->profile) {
            $this->user->profile = UserProfile::castAndCreate(['user_id' => $this->user->id]);
        }
        
        $path = $this->id_card->store('id_card', 'public');
        if ($this->user->profile->id_card && Storage::disk('public')->exists($this->user->profile->id_card)) {
            Storage::disk('public')->delete($this->user->profile->id_card);
        }
        $this->user->profile->castAndUpdate(['id_card' => $path]);
    }
    $this->edit_ktp = false;
};

$editKk = function() { $this->edit_kk = true; };
$cancelKk = function() { $this->edit_kk = false; };
$saveKk = function() {
    $this->validate(['family_card' => ['nullable', 'image', 'mimes:png,jpg,jpeg', 'max:2048']]);
    
    if ($this->family_card) {
        if (!$this->user->profile) {
            $this->user->profile = UserProfile::castAndCreate(['user_id' => $this->user->id]);
        }
        
        $path = $this->family_card->store('family_card', 'public');
        if ($this->user->profile->family_card && Storage::disk('public')->exists($this->user->profile->family_card)) {
            Storage::disk('public')->delete($this->user->profile->family_card);
        }
        $this->user->profile->castAndUpdate(['family_card' => $path]);
    }
    $this->edit_kk = false;
};

$editIg = function() { $this->edit_ig = true; };
$cancelIg = function() { $this->edit_ig = false; };
$saveIg = function() {
    $this->validate(['ig' => ['nullable', 'image', 'mimes:png,jpg,jpeg', 'max:2048']]);
    
    if ($this->ig) {
        if (!$this->user->profile) {
            $this->user->profile = UserProfile::castAndCreate(['user_id' => $this->user->id]);
        }
        
        $path = $this->ig->store('ig', 'public');
        if ($this->user->profile->ig && Storage::disk('public')->exists($this->user->profile->ig)) {
            Storage::disk('public')->delete($this->user->profile->ig);
        }
        $this->user->profile->castAndUpdate(['ig' => $path]);
    }
    $this->edit_ig = false;
};

$editTiktok = function() { $this->edit_tiktok = true; };
$cancelTiktok = function() { $this->edit_tiktok = false; };
$saveTiktok = function() {
    $this->validate(['tiktok' => ['nullable', 'image', 'mimes:png,jpg,jpeg', 'max:2048']]);
    
    if ($this->tiktok) {
        if (!$this->user->profile) {
            $this->user->profile = UserProfile::castAndCreate(['user_id' => $this->user->id]);
        }
        
        $path = $this->tiktok->store('tiktok', 'public');
        if ($this->user->profile->tiktok && Storage::disk('public')->exists($this->user->profile->tiktok)) {
            Storage::disk('public')->delete($this->user->profile->tiktok);
        }
        $this->user->profile->castAndUpdate(['tiktok' => $path]);
    }
    $this->edit_tiktok = false;
};

$editWa = function() { $this->edit_wa = true; };
$saveWa = function() {
    $this->validate(['wa' => ['nullable', 'string', 'max:15']]);
    
    if (!$this->user->profile) {
        $this->user->profile = UserProfile::castAndCreate(['user_id' => $this->user->id]);
    }
    
    $this->user->profile->castAndUpdate(['wa' => $this->wa]);
    $this->edit_wa = false;
};
$deleteAccount = function(){
    $user = \App\Models\User::findOrFail(Auth::user()->id);
    $user->delete();
    $route = route('home');
    return $this->redirect($route, navigate: true);
};
?>

<x-app>
    <x-toolbar-mobile 
        :breadcrumbs="[['icon' => 'arrow-left', 'url' => route('profile.setting')], ['text' => 'Ubah Profil', 'active' => true]]"
    />
    <style>
        .image-input-placeholder {
            background-image: url('/media/svg/avatars/blank.svg');
        }
        [data-bs-theme="dark"] .image-input-placeholder {
            background-image: url('/media/svg/avatars/blank-dark.svg');
        }
    </style>
    @volt
    <div id="kt_app_content" class="app-content flex-column-fluid py-10">
        <!--begin::Image input-->
        <div class="text-center mb-10">
            @if($this->user->st == 'pending')
                <div class="image-input {{ !Auth::user()->avatar ? 'image-input-empty image-input-placeholder' : 'image-input-circle' }}" data-kt-image-input="true">
                    <div class="image-input-wrapper w-125px h-125px" style="background-image: url({{ Auth::user()->avatar ? asset('storage/' . Auth::user()->avatar) : '' }})"></div>
                    <label class="btn btn-icon btn-circle btn-color-muted btn-active-color-primary w-25px h-25px bg-body shadow"
                        data-kt-image-input-action="change"
                        data-bs-toggle="tooltip"
                        data-bs-dismiss="click"
                        title="Pilih Foto">
                        <i class="ki-filled ki-pencil fs-6"></i>
                        <input type="file" wire:model="avatar" accept=".png,.jpg,.jpeg" />
                        <input type="hidden" name="avatar_remove" />
                    </label>
                    <span class="btn btn-icon btn-circle btn-color-muted btn-active-color-primary w-25px h-25px bg-body shadow"
                        data-kt-image-input-action="cancel"
                        data-bs-toggle="tooltip"
                        data-bs-dismiss="click"
                        title="Batalkan">
                        <i class="ki-filled ki-cross fs-3"></i>
                    </span>
                    <span class="btn btn-icon btn-circle btn-color-muted btn-active-color-primary w-25px h-25px bg-body shadow"
                        data-kt-image-input-action="remove"
                        data-bs-toggle="tooltip"
                        data-bs-dismiss="click"
                        wire:click="removeAvatar"
                        title="Hapus">
                        <i class="ki-filled ki-trash fs-3"></i>
                    </span>
                </div>
            @else
                <div class="symbol symbol-100px symbol-circle">
                    <div class="symbol-label" style="background-image:url({{ Auth::user()->avatar ? asset('storage/' . Auth::user()->avatar) : '' }})"></div>
                </div>
            @endif
        </div>
        <!--end::Image input-->

        <div class="card card-xl-stretch mb-5 mb-xl-8">
            <div class="card-header align-items-center border-0 mt-4">
                <h3 class="card-title align-items-start flex-column">
                    <span class="fw-bold text-gray-900">Info Profil</span>
                </h3>
            </div>
            <div class="card-body pt-3">
                <div class="d-flex align-items-center mb-7">
                    <div class="d-flex flex-row-fluid align-items-center">
                        <div class="w-25">
                            <span class="text-gray-800 fw-bold fs-6">Nama</span>
                        </div>
                        <div class="w-50">
                            @if($this->edit_nama)
                                <x-form-input type="text" name="nama" class="bg-transparent" label="Nama Lengkap" placeholder="Nama Lengkap Anda" wire:model="nama"/>
                            @else
                                <span class="text-gray-800 fs-6">
                                    {{ $this->user->name ?? 'Belum diisi' }}
                                </span>
                            @endif
                        </div>
                        <div class="w-25 text-end">
                            @if($this->edit_nama)
                                <button wire:click="saveNama" class="btn btn-icon btn-light btn-sm border-0" aria-label="Save">
                                    <i class="ki-filled ki-check fs-2 text-success"></i>
                                </button>
                            @else
                                @if($this->user->st == 'pending')
                                    <button wire:click="editNama" class="btn btn-icon btn-light btn-sm border-0" aria-label="Edit">
                                        <i class="ki-outline ki-arrow-right fs-2 text-primary"></i>
                                    </button>
                                @endif
                            @endif
                        </div>
                    </div>
                </div>
                <div class="d-flex align-items-center mb-7">
                    <div class="d-flex flex-row-fluid align-items-center">
                        <div class="w-25">
                            <span class="text-gray-800 fw-bold fs-6">Email</span>
                        </div>
                        <div class="w-50">
                            @if($this->edit_email)
                                <x-form-input type="email" name="email" class="bg-transparent" label="Email" placeholder="Alamat Email Anda" wire:model="email"/>
                            @else
                                <span class="text-gray-800 fs-6">
                                    {{ $this->user->email ?? 'Belum diisi' }}
                                </span>
                            @endif
                        </div>
                        <div class="w-25 text-end">
                            @if($this->edit_email)
                                <button wire:click="saveEmail" class="btn btn-icon btn-light btn-sm border-0" aria-label="Save">
                                    <i class="ki-filled ki-check fs-2 text-success"></i>
                                </button>
                            @else
                                @if($this->user->st == 'pending')
                                    <button wire:click="editEmail" class="btn btn-icon btn-light btn-sm border-0" aria-label="Edit">
                                        <i class="ki-outline ki-arrow-right fs-2 text-primary"></i>
                                    </button>
                                @endif
                            @endif
                        </div>
                    </div>
                </div>
                <div class="d-flex align-items-center mb-7">
                    <div class="d-flex flex-row-fluid align-items-center">
                        <div class="w-25">
                            <span class="text-gray-800 fw-bold fs-6">Nomor HP</span>
                        </div>
                        <div class="w-50">
                            @if($this->edit_phone)
                                <x-form-input type="text" name="phone" class="bg-transparent" label="Nomor HP" placeholder="Nomor HP Anda" wire:model="phone"/>
                            @else
                                <span class="text-gray-800 fs-6">
                                    {{ $this->user->phone ? '+62' . $this->user->phone : 'Belum diisi' }}
                                </span>
                            @endif
                        </div>
                        <div class="w-25 text-end">
                            @if($this->edit_phone)
                                <button wire:click="savePhone" class="btn btn-icon btn-light btn-sm border-0" aria-label="Save">
                                    <i class="ki-filled ki-check fs-2 text-success"></i>
                                </button>
                            @else
                                @if($this->user->st == 'pending')
                                    <button wire:click="editPhone" class="btn btn-icon btn-light btn-sm border-0" aria-label="Edit">
                                        <i class="ki-outline ki-arrow-right fs-2 text-primary"></i>
                                    </button>
                                @endif
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card card-xl-stretch mb-5 mb-xl-8">
            <div class="card-header align-items-center border-0 mt-4">
                <h3 class="card-title align-items-start flex-column">
                    <span class="fw-bold text-gray-900">Info Pribadi</span>
                </h3>
            </div>
            <div class="card-body pt-3">
                <div class="d-flex align-items-center mb-7">
                    <div class="d-flex flex-row-fluid align-items-center">
                        <div class="w-25">
                            <span class="text-gray-800 fw-bold fs-6">NIK</span>
                        </div>
                        <div class="w-50">
                            @if($this->edit_nik)
                                <x-form-input type="text" name="nik" class="bg-transparent" label="NIK" placeholder="Nomor Induk Kependudukan" wire:model="nik"/>
                            @else
                                <span class="text-gray-800 fs-6">
                                    {{ $this->user->profile?->nik ?? 'Belum diisi' }}
                                </span>
                            @endif
                        </div>
                        <div class="w-25 text-end">
                            @if($this->edit_nik)
                                <button wire:click="saveNik" class="btn btn-icon btn-light btn-sm border-0" aria-label="Save">
                                    <i class="ki-filled ki-check fs-2 text-success"></i>
                                </button>
                            @else
                                @if($this->user->st == 'pending')
                                    <button wire:click="editNik" class="btn btn-icon btn-light btn-sm border-0" aria-label="Edit">
                                        <i class="ki-outline ki-arrow-right fs-2 text-primary"></i>
                                    </button>
                                @endif
                            @endif
                        </div>
                    </div>
                </div>
                <div class="d-flex align-items-center mb-7">
                    <div class="d-flex flex-row-fluid align-items-center">
                        <div class="w-25">
                            <span class="text-gray-800 fw-bold fs-6">TTL</span>
                        </div>
                        <div class="w-50">
                            @if($this->edit_ttl)
                                <div class="row">
                                    <div class="col-md-6">
                                        <x-form-input type="text" name="pob" class="bg-transparent" label="Tempat Lahir" placeholder="Tempat Lahir" wire:model="pob"/>
                                    </div>
                                    <div class="col-md-6">
                                        <x-form-input type="date" name="bod" class="bg-transparent" label="Tanggal Lahir" wire:model="bod"/>
                                    </div>
                                </div>
                            @else
                                <span class="text-gray-800 fs-6">
                                    {{ $this->user->profile && $this->user->profile->pob && $this->user->profile->bod ? $this->user->profile->pob . ', ' . $this->user->profile->bod->format('j F Y') : 'Belum diisi' }}
                                </span>
                            @endif
                        </div>
                        <div class="w-25 text-end">
                            @if($this->edit_ttl)
                                <button wire:click="saveTtl" class="btn btn-icon btn-light btn-sm border-0" aria-label="Save">
                                    <i class="ki-filled ki-check fs-2 text-success"></i>
                                </button>
                            @else
                                @if($this->user->st == 'pending')
                                    <button wire:click="editTtl" class="btn btn-icon btn-light btn-sm border-0" aria-label="Edit">
                                        <i class="ki-outline ki-arrow-right fs-2 text-primary"></i>
                                    </button>
                                @endif
                            @endif
                        </div>
                    </div>
                </div>
                <div class="d-flex align-items-center mb-7">
                    <div class="d-flex flex-row-fluid align-items-center">
                        <div class="w-25">
                            <span class="text-gray-800 fw-bold fs-6">Jenis Kelamin</span>
                        </div>
                        <div class="w-50">
                            @if($this->edit_gender)
                                <select name="gender" class="form-select bg-transparent" wire:model="gender">
                                    <option value="">Pilih Jenis Kelamin</option>
                                    <option value="pria">Pria</option>
                                    <option value="wanita">Wanita</option>
                                </select>
                            @else
                                <span class="text-gray-800 fs-6">
                                    {{ $this->user->profile?->gender ? Str::title($this->user->profile->gender) : 'Belum diisi' }}
                                </span>
                            @endif
                        </div>
                        <div class="w-25 text-end">
                            @if($this->edit_gender)
                                <button wire:click="saveGender" class="btn btn-icon btn-light btn-sm border-0" aria-label="Save">
                                    <i class="ki-filled ki-check fs-2 text-success"></i>
                                </button>
                            @else
                                @if($this->user->st == 'pending')
                                    <button wire:click="editGender" class="btn btn-icon btn-light btn-sm border-0" aria-label="Edit">
                                        <i class="ki-outline ki-arrow-right fs-2 text-primary"></i>
                                    </button>
                                @endif
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card card-xl-stretch mb-5 mb-xl-8">
            <div class="card-header align-items-center border-0 mt-4">
                <h3 class="card-title align-items-start flex-column">
                    <span class="fw-bold text-gray-900">Dokumen Penting</span>
                </h3>
            </div>
            <div class="card-body pt-3">
                <div class="d-flex align-items-center mb-7">
                    <div class="d-flex flex-row-fluid align-items-center">
                        <div class="w-25">
                            <span class="text-gray-800 fw-bold fs-6">KTP</span>
                        </div>
                        <div class="w-50">
                            @if($this->edit_ktp)
                                <x-form-input type="file" name="id_card" class="bg-transparent" label="KTP" accept=".png,.jpg,.jpeg" wire:model="id_card"/>
                            @else
                                <span class="text-gray-800 fs-6">
                                    @if($this->user->profile?->id_card)
                                        <img src="{{ asset('storage/' . $this->user->profile->id_card) }}" alt="KTP" class="img-fluid" style="max-width: 100px; max-height: 100px;">
                                    @else
                                        Belum diunggah
                                    @endif
                                </span>
                            @endif
                        </div>
                        <div class="w-25 text-end">
                            @if($this->edit_ktp)
                                <button wire:click="saveKtp" class="btn btn-icon btn-light btn-sm border-0" aria-label="Save">
                                    <i class="ki-filled ki-check fs-2 text-success"></i>
                                </button>
                                <button wire:click="cancelKtp" class="btn btn-icon btn-light btn-sm border-0" aria-label="Batalkan">
                                    <i class="ki-filled ki-cross fs-2 text-danger"></i>
                                </button>
                            @else
                                @if($this->user->st == 'pending')
                                    <button wire:click="editKtp" class="btn btn-icon btn-light btn-sm border-0" aria-label="Edit">
                                        <i class="ki-outline ki-arrow-right fs-2 text-primary"></i>
                                    </button>
                                @endif
                            @endif
                        </div>
                    </div>
                </div>
                <div class="d-flex align-items-center mb-7">
                    <div class="d-flex flex-row-fluid align-items-center">
                        <div class="w-25">
                            <span class="text-gray-800 fw-bold fs-6">Kartu Keluarga</span>
                        </div>
                        <div class="w-50">
                            @if($this->edit_kk)
                                <x-form-input type="file" name="family_card" class="bg-transparent" label="Kartu Keluarga" accept=".png,.jpg,.jpeg" wire:model="family_card"/>
                            @else
                                <span class="text-gray-800 fs-6">
                                    @if($this->user->profile?->family_card)
                                        <img src="{{ asset('storage/' . $this->user->profile->family_card) }}" alt="Kartu Keluarga" class="img-fluid" style="max-width: 100px; max-height: 100px;">
                                    @else
                                        Belum diunggah
                                    @endif
                                </span>
                            @endif
                        </div>
                        <div class="w-25 text-end">
                            @if($this->edit_kk)
                                <button wire:click="saveKk" class="btn btn-icon btn-light btn-sm border-0" aria-label="Save">
                                    <i class="ki-filled ki-check fs-2 text-success"></i>
                                </button>
                                <button wire:click="cancelKk" class="btn btn-icon btn-light btn-sm border-0" aria-label="Batalkan">
                                    <i class="ki-filled ki-cross fs-2 text-danger"></i>
                                </button>
                            @else
                                @if($this->user->st == 'pending')
                                    <button wire:click="editKk" class="btn btn-icon btn-light btn-sm border-0" aria-label="Edit">
                                        <i class="ki-outline ki-arrow-right fs-2 text-primary"></i>
                                    </button>
                                @endif
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card card-xl-stretch mb-5 mb-xl-8">
            <div class="card-header align-items-center border-0 mt-4">
                <h3 class="card-title align-items-start flex-column">
                    <span class="fw-bold text-gray-900">Sosial Media</span>
                </h3>
            </div>
            <div class="card-body pt-3">
                <div class="d-flex align-items-center mb-7">
                    <div class="d-flex flex-row-fluid align-items-center">
                        <div class="w-25">
                            <span class="text-gray-800 fw-bold fs-6">WhatsApp</span>
                        </div>
                        <div class="w-50">
                            @if($this->edit_wa)
                                <x-form-input type="text" name="wa" class="bg-transparent" label="WhatsApp" placeholder="Nomor WhatsApp" wire:model="wa"/>
                            @else
                                <span class="text-gray-800 fs-6">
                                    {{ $this->user->profile?->wa ? '+62' . $this->user->profile->wa : 'Belum diisi' }}
                                </span>
                            @endif
                        </div>
                        <div class="w-25 text-end">
                            @if($this->edit_wa)
                                <button wire:click="saveWa" class="btn btn-icon btn-light btn-sm border-0" aria-label="Save">
                                    <i class="ki-filled ki-check fs-2 text-success"></i>
                                </button>
                            @else
                                @if($this->user->st == 'pending')
                                    <button wire:click="editWa" class="btn btn-icon btn-light btn-sm border-0" aria-label="Edit">
                                        <i class="ki-outline ki-arrow-right fs-2 text-primary"></i>
                                    </button>
                                @endif
                            @endif
                        </div>
                    </div>
                </div>
                <div class="d-flex align-items-center mb-7">
                    <div class="d-flex flex-row-fluid align-items-center">
                        <div class="w-25">
                            <span class="text-gray-800 fw-bold fs-6">Instagram</span>
                        </div>
                        <div class="w-50">
                            @if($this->edit_ig)
                                <x-form-input type="file" name="ig" class="bg-transparent" label="Instagram" accept=".png,.jpg,.jpeg" wire:model="ig"/>
                            @else
                                <span class="text-gray-800 fs-6">
                                    @if($this->user->profile?->ig)
                                        <img src="{{ asset('storage/' . $this->user->profile->ig) }}" alt="Instagram" class="img-fluid" style="max-width: 100px; max-height: 100px;">
                                    @else
                                        Belum diunggah
                                    @endif
                                </span>
                            @endif
                        </div>
                        <div class="w-25 text-end">
                            @if($this->edit_ig)
                                <button wire:click="saveIg" class="btn btn-icon btn-light btn-sm border-0" aria-label="Save">
                                    <i class="ki-filled ki-check fs-2 text-success"></i>
                                </button>
                                <button wire:click="cancelIg" class="btn btn-icon btn-light btn-sm border-0" aria-label="Batalkan">
                                    <i class="ki-filled ki-cross fs-2 text-danger"></i>
                                </button>
                            @else
                                @if($this->user->st == 'pending')
                                    <button wire:click="editIg" class="btn btn-icon btn-light btn-sm border-0" aria-label="Edit">
                                        <i class="ki-outline ki-arrow-right fs-2 text-primary"></i>
                                    </button>
                                @endif
                            @endif
                        </div>
                    </div>
                </div>
                <div class="d-flex align-items-center mb-7">
                    <div class="d-flex flex-row-fluid align-items-center">
                        <div class="w-25">
                            <span class="text-gray-800 fw-bold fs-6">TikTok</span>
                        </div>
                        <div class="w-50">
                            @if($this->edit_tiktok)
                                <x-form-input type="file" name="tiktok" class="bg-transparent" label="TikTok" accept=".png,.jpg,.jpeg" wire:model="tiktok"/>
                            @else
                                <span class="text-gray-800 fs-6">
                                    @if($this->user->profile?->tiktok)
                                        <img src="{{ asset('storage/' . $this->user->profile->tiktok) }}" alt="TikTok" class="img-fluid" style="max-width: 100px; max-height: 100px;">
                                    @else
                                        Belum diunggah
                                    @endif
                                </span>
                            @endif
                        </div>
                        <div class="w-25 text-end">
                            @if($this->edit_tiktok)
                                <button wire:click="saveTiktok" class="btn btn-icon btn-light btn-sm border-0" aria-label="Save">
                                    <i class="ki-filled ki-check fs-2 text-success"></i>
                                </button>
                                <button wire:click="cancelTiktok" class="btn btn-icon btn-light btn-sm border-0" aria-label="Batalkan">
                                    <i class="ki-filled ki-cross fs-2 text-danger"></i>
                                </button>
                            @else
                                @if($this->user->st == 'pending')
                                    <button wire:click="editTiktok" class="btn btn-icon btn-light btn-sm border-0" aria-label="Edit">
                                        <i class="ki-outline ki-arrow-right fs-2 text-primary"></i>
                                    </button>
                                @endif
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="text-center">
            <button onclick="deleteAccount();" class="btn btn-outline-danger">Tutup Akun</button>
        </div>
        @section('custom_js')
            <script data-navigate-once>
                function deleteAccount() {
                    Swal.fire({
                        title: 'Hapus Akun Permanen?',
                        html: `
                            <div class="text-left">
                                <p>Anda yakin ingin menghapus akun Anda? Tindakan ini akan:</p>
                                <ul class="list-disc pl-5">
                                    <li>Menghapus semua data Anda secara permanen</li>
                                    <li>Tidak dapat dikembalikan (irreversible)</li>
                                    <li>Menghentikan semua langganan aktif</li>
                                </ul>
                                <p class="mt-3 font-bold">Ketikan "<span class="text-red-500">konfirmasi</span>" untuk verifikasi:</p>
                                <input type="text" id="confirmationInput" class="swal2-input" placeholder="ketik konfirmasi...">
                            </div>
                        `,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#3085d6',
                        confirmButtonText: 'Ya, Hapus Akun',
                        cancelButtonText: 'Batalkan',
                        reverseButtons: true,
                        backdrop: true,
                        allowOutsideClick: false,
                        preConfirm: () => {
                            const inputValue = document.getElementById('confirmationInput').value;
                            if (inputValue.toLowerCase() !== 'konfirmasi') {
                                Swal.showValidationMessage('Harap ketik "konfirmasi" dengan benar');
                            }
                            return inputValue;
                        }
                    }).then((result) => {
                        if (result.isConfirmed) {
                            Swal.fire({
                                title: 'Menghapus Akun...',
                                html: 'Mohon tunggu, akun Anda sedang dihapus',
                                allowOutsideClick: false,
                                didOpen: () => {
                                    Swal.showLoading();
                                    @this.deleteAccount().then(() => {
                                        Swal.fire({
                                            title: 'Berhasil!',
                                            text: 'Akun Anda telah berhasil dihapus',
                                            icon: 'success'
                                        });
                                    }).catch(error => {
                                        Swal.fire({
                                            title: 'Gagal!',
                                            html: `Gagal menghapus akun: <br><span class="text-red-500">${error.message}</span>`,
                                            icon: 'error'
                                        });
                                    });
                                }
                            });
                        }
                    });
                }
            </script>
        @endsection
    </div>
    @endvolt
</x-app>