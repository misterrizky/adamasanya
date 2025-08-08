<?php
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use function Laravel\Folio\name;
use function Livewire\Volt\{mount, state, usesFileUploads};
name('admin.profile.edit');
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
    'ig' => '',
    'tiktok' => '',
    'wa' => '',
    
    'edit_nama' => false,
    'edit_email' => false,
    'edit_phone' => false,
    'edit_nik' => false,
    'edit_ttl' => false,
    'edit_ig' => false,
    'edit_tiktok' => false,
    'edit_wa' => false,
]);
mount(function () {
    $this->avatar = $this->user->avatar;
    $this->nama = $this->user->name;
    $this->email = $this->user->email;
    $this->phone = $this->user->phone;
    $this->nik = $this->user->profile->nik;
    $this->pob = $this->user->profile->pob;
    $this->bod = $this->user->profile->bod ? $this->user->profile->bod->format('Y-m-d') : '';
    $this->wa = $this->user->profile->wa;
});
$updatedAvatar = function(){
    if($this->avatar){
        // Simpan ke disk public
        $path = $this->avatar->store('avatar', 'public');
        
        // Hapus file temporary upload
        if (File::exists($this->avatar->getRealPath())) {
            File::delete($this->avatar->getRealPath());
        }
        
        // Hapus avatar lama jika ada
        if ($this->user->avatar && Storage::disk('public')->exists($this->user->avatar)) {
            Storage::disk('public')->delete($this->user->avatar);
        }
        
        $this->user->castAndUpdate([
            'avatar' => $path
        ]);
    }
};
$removeAvatar = function(){
    try {
        $avatarPath = Auth::user()->avatar;
        logger()->info("Attempting to delete avatar: {$avatarPath}");
        
        // Gunakan disk yang sama dengan saat menyimpan (public)
        if(Storage::disk('public')->exists($avatarPath)) {
            Storage::disk('public')->delete($avatarPath);
            $this->user->castAndUpdate(['avatar' => null]);
            logger()->info("Avatar deleted successfully: {$avatarPath}");
        } else {
            logger()->error("Avatar file not found in public disk: {$avatarPath}");
        }
    } catch (\Exception $e) {
        logger()->error("Failed to delete avatar: ".$e->getMessage());
    }
};
$editNama = function(){
    $this->edit_nama = true;
};
$saveNama = function(){
    $this->user->name = $this->nama;
    $this->user->save();
    $this->edit_nama = false;
};

$editEmail = function(){
    $this->edit_email = true;
};
$saveEmail = function(){
    $this->user->email = $this->email;
    $this->user->save();
    $this->edit_email = false;
};

$editPhone = function(){
    $this->edit_phone = true;
};
$savePhone = function(){
    $this->user->phone = $this->phone;
    $this->user->save();
    $this->edit_phone = false;
};

$editNik = function(){
    $this->edit_nik = true;
};
$saveNik = function(){
    $this->user->profile->nik = $this->nik;
    $this->user->profile->save();
    $this->edit_nik = false;
};

$editTtl = function(){
    $this->edit_ttl = true;
};
$saveTtl = function(){
    $this->user->profile->pob = $this->pob;
    $this->user->profile->bod = $this->bod;
    $this->user->profile->save();
    $this->edit_ttl = false;
};

$editIg = function(){
    $this->edit_ig = true;
};
$saveIg = function(){
    $this->user->profile->ig = $this->ig;
    $this->user->profile->save();
    $this->edit_ig = false;
};

$editTiktok = function(){
    $this->edit_tiktok = true;
};
$saveTiktok = function(){
    $this->user->profile->tiktok = $this->tiktok;
    $this->user->profile->save();
    $this->edit_tiktok = false;
};

$editWa = function(){
    $this->edit_wa = true;
};
$saveWa = function(){
    $this->user->profile->wa = $this->wa;
    $this->user->profile->save();
    $this->edit_wa = false;
};
?>
<x-app>
    <x-toolbar-mobile 
        :breadcrumbs="[
            ['icon' => 'arrow-left', 'url' => route('admin.profile.setting')],
            ['text' => 'Ubah Profil', 'active' => true]
        ]"
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
                    <!--begin::Image preview wrapper-->
                    <div class="image-input-wrapper w-125px h-125px" style="background-image: url({{ Auth::user()->image }})"></div>
                    <!--end::Image preview wrapper-->
                    <!--begin::Edit button-->
                    <label class="btn btn-icon btn-circle btn-color-muted btn-active-color-primary w-25px h-25px bg-body shadow"
                    data-kt-image-input-action="change"
                    data-bs-toggle="tooltip"
                    data-bs-dismiss="click"
                    title="Pilih Foto">
                        <i class="ki-filled ki-pencil fs-6"></i>
        
                        <!--begin::Inputs-->
                        <input type="file" wire:model.change="avatar" accept=".png, .jpg, .jpeg" />
                        <input type="hidden" name="avatar_remove" />
                        <!--end::Inputs-->
                    </label>
                    <!--end::Edit button-->
        
                    <!--begin::Cancel button-->
                    <span class="btn btn-icon btn-circle btn-color-muted btn-active-color-primary w-25px h-25px bg-body shadow"
                    data-kt-image-input-action="cancel"
                    data-bs-toggle="tooltip"
                    data-bs-dismiss="click"
                    title="Batalkan">
                        <i class="ki-filled ki-cross fs-3"></i>
                    </span>
                    <!--end::Cancel button-->
        
                    <!--begin::Remove button-->
                    <span class="btn btn-icon btn-circle btn-color-muted btn-active-color-primary w-25px h-25px bg-body shadow"
                    data-kt-image-input-action="remove"
                    data-bs-toggle="tooltip"
                    data-bs-dismiss="click"
                    wire:click="removeAvatar"
                    title="Hapus">
                        <i class="ki-filled ki-trash fs-3"></i>
                    </span>
                    <!--end::Remove button-->
                </div>
            @else
            <div class="symbol symbol-100px symbol-circle">
                <div class="symbol-label" style="background-image:url({{ Auth::user()->profile->image }})"></div>
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
                                <x-form-input type="text" name="nama" class="bg-transparent" label="Nama Lengkap" placeholder="Nama Lengkap Anda"/>
                            @else
                                <span class="text-gray-800 fs-6">
                                    {{ $this->user->name }}
                                </span>
                            @endif
                        </div>
                        <div class="w-25 text-end">
                            @if($this->edit_nama)
                                <button wire:click="saveNama" class="btn btn-icon btn-light btn-sm border-0" aria-label="Next">
                                    <i class="ki-filled ki-check fs-2 text-success"></i>
                                </button>
                            @else
                                @if($this->user->st == 'pending')
                                    <button wire:click="editNama" class="btn btn-icon btn-light btn-sm border-0" aria-label="Next">
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
                                <x-form-input type="email" name="email" class="bg-transparent" label="Email" placeholder="Alamat Email Anda"/>
                            @else
                                <span class="text-gray-800 fs-6">
                                    {{ $this->user->email }}
                                </span>
                            @endif
                        </div>
                        <div class="w-25 text-end">
                            @if($this->edit_email)
                                <button wire:click="saveEmail" class="btn btn-icon btn-light btn-sm border-0" aria-label="Next">
                                    <i class="ki-filled ki-check fs-2 text-success"></i>
                                </button>
                            @else
                                @if($this->user->st == 'pending')
                                    <button wire:click="editEmail" class="btn btn-icon btn-light btn-sm border-0" aria-label="Next">
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
                                <x-form-input type="text" name="phone" class="bg-transparent" label="Nomor HP" placeholder="Nomor HP Anda"/>
                            @else
                                <span class="text-gray-800 fs-6">
                                    +62{{ $this->user->phone }}
                                </span>
                            @endif
                        </div>
                        <div class="w-25 text-end">
                            @if($this->edit_phone)
                                <button wire:click="savePhone" class="btn btn-icon btn-light btn-sm border-0" aria-label="Next">
                                    <i class="ki-filled ki-check fs-2 text-success"></i>
                                </button>
                            @else
                                @if($this->user->st == 'pending')
                                    <button wire:click="editPhone" class="btn btn-icon btn-light btn-sm border-0" aria-label="Next">
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
                                <x-form-input type="text" name="nik" class="bg-transparent" label="NIK" placeholder="Nomor Induk Kependudukan"/>
                            @else
                                <span class="text-gray-800 fs-6">
                                    {{ $this->user->profile->nik }}
                                </span>
                            @endif
                        </div>
                        <div class="w-25 text-end">
                            @if($this->edit_nik)
                                <button wire:click="saveNik" class="btn btn-icon btn-light btn-sm border-0" aria-label="Next">
                                    <i class="ki-filled ki-check fs-2 text-success"></i>
                                </button>
                            @else
                                @if($this->user->st == 'pending')
                                    <button wire:click="editNik" class="btn btn-icon btn-light btn-sm border-0" aria-label="Next">
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
                                        <x-form-input type="text" name="pob" class="bg-transparent" label="Tempat Lahir" placeholder="Tempat Lahir"/>
                                    </div>
                                    <div class="col-md-6">
                                        <x-form-input type="date" name="bod" class="bg-transparent" label="Tanggal Lahir"/>
                                    </div>
                                </div>
                            @else
                                <span class="text-gray-800 fs-6">
                                    {{ $this->user->profile->pob ? $this->user->profile->pob . ', ' . $this->user->profile->bod->format('j F Y') : '' }}
                                </span>
                            @endif
                        </div>
                        <div class="w-25 text-end">
                            @if($this->edit_ttl)
                                <button wire:click="saveTtl" class="btn btn-icon btn-light btn-sm border-0" aria-label="Next">
                                    <i class="ki-filled ki-check fs-2 text-success"></i>
                                </button>
                            @else
                                @if($this->user->st == 'pending')
                                    <button wire:click="editTtl" class="btn btn-icon btn-light btn-sm border-0" aria-label="Next">
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
                            <span class="text-gray-800 fw-bold fs-6">WhatsApp</span>
                        </div>
                        <div class="w-50">
                            @if($this->edit_wa)
                                <x-form-input type="text" name="wa" class="bg-transparent" label="WhatsApp" placeholder="Nomor WhatsApp"/>
                            @else
                                <span class="text-gray-800 fs-6">
                                    +62{{ $this->user->profile->wa }}
                                </span>
                            @endif
                        </div>
                        <div class="w-25 text-end">
                            @if($this->edit_wa)
                                <button wire:click="saveWa" class="btn btn-icon btn-light btn-sm border-0" aria-label="Next">
                                    <i class="ki-filled ki-check fs-2 text-success"></i>
                                </button>
                            @else
                                @if($this->user->st == 'pending')
                                    <button wire:click="editWa" class="btn btn-icon btn-light btn-sm border-0" aria-label="Next">
                                        <i class="ki-outline ki-arrow-right fs-2 text-primary"></i>
                                    </button>
                                @endif
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endvolt
</x-app>