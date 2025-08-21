<?php
use App\Models\User;
use App\Models\Master\{Branch};
use function Laravel\Folio\name;
use Cmgmyr\Messenger\Models\Thread;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Cmgmyr\Messenger\Models\Participant;
use App\Notifications\NewConsumerNotification;
use App\Notifications\OnboardingCompletedNotification;
use App\Models\Region\{Country, State, City, Subdistrict, Village};
use function Livewire\Volt\{computed, rules, state, usesFileUploads};

usesFileUploads();
name('onboarding');
state(['user' => fn () => Auth::user()]);
// State definitions
state([
    'step' => 1,
    'cabang' => '',
    'nik' => '',
    'tanggal_lahir' => '',
    'tempat_lahir' => '',
    'jenis_kelamin' => '',
    'wa' => '',
    'instagram' => '',
    'tiktok' => '',
    'id_card' => '',
    'selfi' => '',
    'status_kepemilikan_rumah' => '',
    'propinsi' => '',
    'kota' => '',
    'kecamatan' => '',
    'kelurahan' => '',
    'kode_pos' => '',
    'alamat' => '',
    'catatan' => '',
    'lat' => '',
    'lng' => '',
    'selected_provinsi' => null,
    'selected_kota' => null,
    'selected_kecamatan' => null,
    'kk' => '',
    'keluarga' => [
        [
            'status_keluarga' => '',
            'nama_keluarga' => '',
            'no_hp_keluarga' => ''
        ]
    ]
]);

// Validation rules
rules(fn () => [
    'cabang' => ['required', 'exists:branches,id'],
    'nik' => ['required', 'digits:16'],
    'jenis_kelamin' => ['required', 'in:pria,wanita'],
    'tanggal_lahir' => ['required', 'date', 'before:today'],
    'tempat_lahir' => ['required', 'string', 'max:100'],
    'wa' => ['required', 'string', 'max:15'],
    'tiktok' => ['required', 'image', 'mimes:jpeg,jpg,png', 'max:2048'],
    'instagram' => ['required', 'image', 'mimes:jpeg,jpg,png', 'max:2048'],
    'id_card' => ['required', 'image', 'mimes:jpeg,jpg,png', 'max:2048'],
    'selfi' => ['required', 'image', 'mimes:jpeg,jpg,png', 'max:2048'],
    'status_kepemilikan_rumah' => ['required', 'string', 'max:255'],
    'catatan' => ['required', 'string', 'max:255'],
    'alamat' => ['required', 'string', 'max:255'],
    'propinsi' => ['required', 'numeric', 'exists:states,id'],
    'kota' => ['required', 'numeric', 'exists:cities,id'],
    'kecamatan' => ['required', 'numeric', 'exists:subdistricts,id'],
    'kelurahan' => ['required', 'numeric', 'exists:villages,id'],
    'kode_pos' => ['required', 'digits:5'],
    'kk' => ['required', 'image', 'mimes:jpeg,jpg,png', 'max:2048'],
    'keluarga' => ['required', 'array', 'min:1'],
    'keluarga.*.status_keluarga' => ['required', 'in:Kepala Keluarga,Suami,Istri,Anak,Menantu,cucu,Orangtua,Mertua,Famili Lain,Pembantu,Lainnya'],
    'keluarga.*.nama_keluarga' => ['required', 'string', 'max:100'],
    'keluarga.*.no_hp_keluarga' => ['required', 'string', 'max:15'],
]);

$setLatitude = function($value){
    $this->lat = $value;
};
$setLongitude = function($value){
    $this->lng = $value;
};
// Navigation functions
$navigateStep = function($direction, $currentStep = null) {
    $currentStep = $currentStep ?: $this->step;
    
    if ($direction === 'back') {
        $newStep = max(1, $currentStep - 1);
        $this->step = $newStep;
        $this->dispatch("onboarding-step-{$currentStep}-back");
    } else {
        $this->validateCurrentStep($currentStep);
        $newStep = min(5, $currentStep + 1);
        $this->step = $newStep;
        $this->dispatch("onboarding-step-{$currentStep}-finish");
    }
};
// Validation functions
$validateCurrentStep = function($step) {
    $validationRules = [
        1 => ['cabang' => 'required|exists:branches,id'],
        2 => [
            'nik' => 'required|digits:16',
            'tempat_lahir' => 'required|string|max:100',
            'tanggal_lahir' => 'required|date|before:today',
            'jenis_kelamin' => 'required|in:pria,wanita',
            'wa' => 'required|string|max:15',
            'tiktok' => 'required|image|mimes:jpeg,jpg,png|max:2048',
            'instagram' => 'required|image|mimes:jpeg,jpg,png|max:2048',
            'id_card' => 'required|image|mimes:jpeg,jpg,png|max:2048',
            'selfi' => 'required|image|mimes:jpeg,jpg,png|max:2048',
        ],
        3 => [
            'status_kepemilikan_rumah' => 'required|string|max:255',
            'propinsi' => 'required|numeric|exists:states,id',
            'kota' => 'required|numeric|exists:cities,id',
            'kecamatan' => 'required|numeric|exists:subdistricts,id',
            'kelurahan' => 'required|numeric|exists:villages,id',
            'kode_pos' => 'required|digits:5',
            'alamat' => 'required|string|max:255',
            'catatan' => 'required|string|max:255',
        ],
        4 => [
            'kk' => 'required|image|mimes:jpeg,jpg,png|max:2048',
            'keluarga' => 'required|array|min:1',
            'keluarga.*.status_keluarga' => 'required|in:Kepala Keluarga,Suami,Istri,Anak,Menantu,cucu,Orangtua,Mertua,Famili Lain,Pembantu,Lainnya',
            'keluarga.*.nama_keluarga' => 'required|string|max:100',
            'keluarga.*.no_hp_keluarga' => 'required|string|max:15',
        ]
    ];
    
    if (isset($validationRules[$step])) {
        $this->dispatch('toast-warning', message: "Harap periksa data Anda sebelum melanjutkan.");
        $this->validate($validationRules[$step]);
    }
};

// Helper functions
$validateKeluarga = function() {
    $validKeluarga = collect($this->keluarga)->filter(function($keluarga) {
        return !empty($keluarga['status_keluarga']) && 
               !empty($keluarga['nama_keluarga']) && 
               !empty($keluarga['no_hp_keluarga']);
    });

    if ($validKeluarga->isEmpty()) {
        $this->dispatch('toast-warning', message: "Minimal 1 data keluarga harus diisi.");
        return;
    }

    // Validasi setiap data keluarga
    $this->validate([
        'keluarga.*.status_keluarga' => 'required|in:Kepala Keluarga,Suami,Istri,Anak,Menantu,cucu,Orangtua,Mertua,Famili Lain,Pembantu,Lainnya',
        'keluarga.*.nama_keluarga' => 'required|string|max:100',
        'keluarga.*.no_hp_keluarga' => 'required|string|max:15',
    ], [
        'keluarga.*.status_keluarga.required' => 'Status keluarga wajib diisi.',
        'keluarga.*.nama_keluarga.required' => 'Nama keluarga wajib diisi.',
        'keluarga.*.no_hp_keluarga.required' => 'Nomor HP keluarga wajib diisi.',
    ]);

    // Hapus data keluarga yang tidak valid
    $this->keluarga = $validKeluarga->values()->all();
};

$updatedKelurahan = function() {
    if ($this->kelurahan) {
        $this->kode_pos = Village::find($this->kelurahan)->poscode;
    }
};

$addKeluarga = function() {
    $this->keluarga[] = ['status_keluarga' => '', 'nama_keluarga' => '', 'no_hp_keluarga' => ''];
};

$removeKeluarga = function($index) {
    if (count($this->keluarga) > 1) {
        unset($this->keluarga[$index]);
        $this->keluarga = array_values($this->keluarga); // Reindex array
    } else {
        $this->dispatch('toast-warning', message: "Minimal 1 data keluarga harus diisi.");
    }
};

// Save functions
$save = function() {
    $this->dispatch('toast-warning', message: "Harap periksa data Anda sebelum melanjutkan.");
    $user = Auth::user();
    
    // Assign role
    $user->assignRole('Konsumen');
    $user->removeRole('Onboarding');
    
    // Save keluarga
    $this->saveAlamat($user);
    $this->saveKeluarga($user);
    $this->saveProfil($user);
    
    // Update user status
    $user->branch_id = $this->cabang;
    $user->st = 'pending';
    $user->save();
    $user->notify(new OnboardingCompletedNotification());

    // Kirim notifikasi ke pengguna cabang
    $branch = \App\Models\Master\Branch::findOrFail($this->cabang);
    $branchUsers = \App\Models\User::role('Cabang')->where('branch_id', $this->cabang)->first();
    $branchUsers->notify(new NewConsumerNotification($user, $branch));
    $this->startChat($branchUsers->id);
    $this->dispatch('toast-success', message: "Data berhasil disimpan. Silakan tunggu verifikasi dari admin.");
    return $this->redirect(route('home'), navigate: true);
};

$saveAlamat = function($user){
    $data = [
        'user_id' => $user->id,
        'label' => $this->status_kepemilikan_rumah,
        'state_id' => $this->propinsi,
        'city_id' => $this->kota,
        'subdistrict_id' => $this->kecamatan ?? 0,
        'village_id' => $this->kelurahan ?? 0,
        'postal_code' => $this->kode_pos ?? 0,
        'address' => $this->alamat,
        'notes' => $this->catatan,
    ];
    \App\Models\UserAddress::castAndCreate($data);
};
$saveKeluarga = function($user) {
    if (!empty($this->keluarga)) {
        foreach ($this->keluarga as $keluarga) {
            if (!empty($keluarga['status_keluarga']) && !empty($keluarga['nama_keluarga']) && !empty($keluarga['no_hp_keluarga'])) {
                \App\Models\UserFamily::castAndCreate([
                    'user_id' => $user->id,
                    'type' => $keluarga['status_keluarga'],
                    'name' => $keluarga['nama_keluarga'],
                    'phone' => $keluarga['no_hp_keluarga']
                ]);
            }
        }
    }
};
$saveProfil = function($user) {
    $platforms = [
        'id_card' => $this->id_card,
        'family_card' => $this->kk,
        'selfie' => $this->selfi,
        'ig' => $this->instagram,
        'tiktok' => $this->tiktok
    ];
    $data = [
        'user_id' => $user->id,
        'gender' => $this->jenis_kelamin,
        'pob' => $this->tempat_lahir,
        'bod' => $this->tanggal_lahir,
        'nik' => $this->nik,
        'wa' => $this->wa
    ];
    foreach ($platforms as $key => $value) {
        // Check if the user has a profile and if the file exists
        if ($user->profile && $user->profile->$key && Storage::disk('public')->exists($user->profile->$key)) {
            Storage::disk('public')->delete($user->profile->$key);
        }
        
        // Store the new file
        $data[$key] = $value->store($key, 'public');
    }

    // Update or create the profile
    if ($user->profile) {
        $user->profile->castAndUpdate($data);
    } else {
        \App\Models\UserProfile::castAndCreate($data);
    }
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
};
// Get data for selects
$branches = computed(function(){
    return Branch::where('st', 'a')->get();
});
?>
<x-app>
    <x-toolbar-mobile 
        :breadcrumbs="[
            ['icon' => 'arrow-left', 'url' => route('home')],
            ['text' => 'Onboarding', 'active' => true]
        ]"
    />
    <x-toolbar 
        title="Onboarding"
        :breadcrumbs="[
            ['icon' => 'ki-outline ki-home', 'url' => route('home')],
            ['text' => 'Onboarding', 'active' => true]
        ]"
        toolbar-class="py-3 py-lg-6"
    />
    @volt
    @php
    $foreignCountries = Country::whereNot('id', 103)->pluck('name', 'id')->prepend('Pilih Negara', '');
    @endphp
    <div id="kt_app_content" class="app-content flex-column-fluid">
        <!--begin::Content container-->
        <div id="kt_app_content_container" class="app-container container-xxl">
            <!--begin::Stepper-->
            @role('Onboarding')
            <div class="stepper stepper-pills stepper-column d-flex flex-column flex-xl-row flex-row-fluid gap-10" id="step_onboarding">
                <!--begin::Aside-->
                <div class="card d-flex justify-content-center justify-content-xl-start flex-row-auto w-100 w-xl-300px w-xxl-400px d-none d-xl-block">
                    <!--begin::Wrapper-->
                    <div class="card-body px-6 px-lg-10 px-xxl-15 py-20">
                        <!--begin::Nav-->
                        <div class="stepper-nav">
                            <!--begin::Step-->
                            @foreach([1,2,3,4,5] as $s)
                            <div class="stepper-item {{ $s === 5 ? 'mark-completed' : ($this->step == $s ? 'current' : '') }}" data-kt-stepper-element="nav">
                                <!--begin::Wrapper-->
                                <div class="stepper-wrapper">
                                    <!--begin::Icon-->
                                    <div class="stepper-icon w-40px h-40px">
                                        <i class="ki-filled {{ $this->step >= $s ? 'ki-check' : '' }} fs-2 stepper-check"></i>
                                        <span class="stepper-number">{{ $s }}</span>
                                    </div>
                                    <!--end::Icon-->
                                    <!--begin::Label-->
                                    <div class="stepper-label">
                                        <h3 class="stepper-title"> 
                                            @if($s == 1) Cabang 
                                            @elseif($s==2) Pribadi 
                                            @elseif($s==3) Domisili 
                                            @elseif($s==4) Keluarga 
                                            @else Selesai @endif
                                        </h3>
                                        <div class="stepper-desc fw-semibold">
                                            @if($s == 1) Cabang untuk sewa 
                                            @elseif($s==2) Info Terkait NIK, Sosial Media 
                                            @elseif($s==3) Pastikan data domisili Anda akurat 
                                            @elseif($s==4) Info terkait KK, nama anggota keluarga, no hp keluarga 
                                            @endif
                                        </div>
                                    </div>
                                    <!--end::Label-->
                                </div>
                                <!--end::Wrapper-->
                                <!--begin::Line-->
                                @if($s < 5)
                                <div class="stepper-line h-40px"></div>
                                @endif
                                <!--end::Line-->
                            </div>
                            @endforeach
                            <!--end::Step-->
                        </div>
                        <!--end::Nav-->
                    </div>
                    <!--end::Wrapper-->
                </div>
                <!--end::Aside-->
                <!--begin::Content-->
                <div class="card d-flex flex-row-fluid flex-center">
                    <!--begin::Form-->
                    <x-form action="save" class="card-body py-20 w-100" hasFiles id="form_onboarding" novalidate="novalidate">
                        <!--begin::Step 1-->
                        <div class="current" data-kt-stepper-element="content" wire:ignore.self>
                            <!--begin::Wrapper-->
                            <div class="w-100">
                                <!--begin::Heading-->
                                <div class="pb-10 pb-lg-15">
                                    <!--begin::Title-->
                                    <h2 class="fw-bold d-flex align-items-center text-gray-900">
                                        Pilih Cabang 
                                        <span class="ms-1" data-bs-toggle="tooltip" title="Cabang ....">
                                        <i class="ki-outline ki-information-5 text-gray-500 fs-6"></i>
                                    </span></h2>
                                    <!--end::Title-->
                                    <!--begin::Notice-->
                                    <div class="text-muted fw-semibold fs-6">
                                        Jika Anda memerlukan informasi lebih lanjut, silakan cek 
                                        <a href="#" class="link-primary fw-bold">Halaman Bantuan</a>.
                                    </div>
                                    <!--end::Notice-->
                                </div>
                                <!--end::Heading-->
                                @error('cabang')
                                    <div class="fv-plugins-message-container mb-5">
                                        <div class="fv-help-block text-danger">{{ $message }}</div>
                                    </div>
                                @enderror
                                <!--begin::Input group-->
                                <div class="fv-row">
                                    <!--begin::Row-->
                                    <div class="row">
                                        <!--begin::Col-->
                                        @foreach ($this->branches as $item)
                                        <div class="col-lg-6">
                                            <!--begin::Option-->
                                            <input type="radio" class="btn-check" name="cabang" wire:model="cabang" value="{{ $item->id }}" id="branch_{{ $item->id }}" />
                                            <label class="btn btn-outline btn-outline-dashed btn-active-light-primary p-7 d-flex align-items-center mb-10" for="branch_{{ $item->id }}">
                                                <i class="ki-outline ki-shop fs-3x me-5"></i>
                                                <!--begin::Info-->
                                                <span class="d-block fw-semibold text-start">
                                                    <span class="text-gray-900 fw-bold d-block fs-4 mb-2">{{ $item->name }}</span>
                                                    <span class="text-muted fw-semibold fs-6">
                                                        {{ $item->address }}
                                                        <a href="https://instagram.com/{{ $item->ig }}" target="_blank" class="link-primary fw-bold">
                                                            {{ $item->ig }}
                                                        </a>
                                                    </span>
                                                </span>
                                                <!--end::Info-->
                                            </label>
                                            <!--end::Option-->
                                        </div>
                                        @endforeach
                                        <!--end::Col-->
                                    </div>
                                    <!--end::Row-->
                                </div>
                                <!--end::Input group-->
                            </div>
                            <!--end::Wrapper-->
                        </div>
                        <!--end::Step 1-->
                        <!--begin::Step 2-->
                        <div data-kt-stepper-element="content" wire:ignore.self>
                            <!--begin::Wrapper-->
                            <div class="w-100">
                                <!--begin::Heading-->
                                <div class="pb-10 pb-lg-15">
                                    <!--begin::Title-->
                                    <h2 class="fw-bold text-gray-900">Info Pribadi</h2>
                                    <!--end::Title-->
                                    <!--begin::Notice-->
                                    <div class="text-muted fw-semibold fs-6">
                                        Jika Anda memerlukan informasi lebih lanjut, silakan cek 
                                        <a href="#" class="link-primary fw-bold">Halaman Bantuan</a>.
                                    </div>
                                    <!--end::Notice-->
                                </div>
                                <!--end::Heading-->
                                <!--begin::Input group-->
                                <div class="row">
                                    <div class="col-12 col-md-3 mb-5">
                                        <x-form-group name="nik" label="NIK" required>
                                            <x-form-input type="tel" max="16" name="nik" class="bg-transparent" id="nik">
                                                @slot('help')
                                                <small class="form-text text-muted">
                                                    Masukkan NIK KTP/KIA.
                                                </small>
                                                @endslot
                                            </x-form-input>
                                        </x-form-group>
                                    </div>
                                    <div class="col-12 col-md-3 mb-5">
                                        <x-form-group name="tempat_lahir" label="Tempat Lahir" required>
                                            <x-form-input type="text" name="tempat_lahir" class="bg-transparent" id="tempat_lahir"/>
                                        </x-form-group>
                                    </div>
                                    <div class="col-12 col-md-3 mb-5">
                                        <x-form-group name="tanggal_lahir" label="Tanggal Lahir" required>
                                            <x-form-input type="date" name="tanggal_lahir" class="bg-transparent" id="tanggal_lahir"/>
                                        </x-form-group>
                                    </div>
                                    <div class="col-12 col-md-3 mb-5">
                                        <label class="required fs-6 fw-semibold form-label mb-2" for="jenis_kelamin">Pilih Jenis Kelamin:</label>
                                        <!--end::Label-->
                                        <!--begin::Input-->
                                        @php
                                        $data = [
                                            '' => 'Pilih jenis kelamin',
                                            'pria' => 'Pria',
                                            'wanita' => 'Wanita'
                                        ];
                                        @endphp
                                        <x-form-select 
                                            name="jenis_kelamin" 
                                            class="form-select form-select-solid fw-bold"
                                            :options="$data"
                                        />
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-12 col-md-4 mb-5">
                                        <x-form-group label="No WhatsApp" required>
                                            <x-form-input-group>
                                                <x-form-input-group-text>+62</x-form-input-group-text>
                                                <x-form-input type="text" name="wa" class="bg-transparent" placeholder="8123456789" id="wa">
                                                    @slot('help')
                                                    <small class="form-text text-muted">
                                                        Masukkan nomor WhatsApp Anda tanpa angka 0 atau +62 dan - pada form diatas sesuai dengan contoh.
                                                    </small>
                                                    @endslot
                                                </x-form-input>
                                            </x-form-input-group>
                                        </x-form-group>
                                    </div>
                                    <div class="col-12 col-md-4 mb-5">
                                        <x-form-group name="tiktok" label="Tiktok" required>
                                            <x-form-input type="file" accept="image/*" name="tiktok" class="bg-transparent" id="tiktok"/>
                                        </x-form-group>
                                    </div>
                                    <div class="col-12 col-md-4 mb-5">
                                        <x-form-group name="instagram" label="Instagram" required>
                                            <x-form-input type="file" accept="image/*" name="instagram" class="bg-transparent" id="instagram"/>
                                        </x-form-group>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-12 col-md-6 mb-5">
                                        <x-form-group name="id_card" label="KTP" required>
                                            <x-form-input type="file" accept="image/*" name="id_card" class="bg-transparent" id="id_card"/>
                                        </x-form-group>
                                    </div>
                                    <div class="col-12 col-md-6 mb-5">
                                        <x-form-group name="selfi" label="Foto Selfi" required>
                                            <x-form-input type="file" accept="image/*" name="selfi" class="bg-transparent" id="selfi"/>
                                        </x-form-group>
                                    </div>
                                </div>
                                <!--end::Input group-->
                            </div>
                            <!--end::Wrapper-->
                        </div>
                        <!--end::Step 2-->
                        <!--begin::Step 3-->
                        <div data-kt-stepper-element="content" wire:ignore.self>
                            <!--begin::Wrapper-->
                            <div class="w-100">
                                <!--begin::Heading-->
                                <div class="pb-10 pb-lg-12">
                                    <!--begin::Title-->
                                    <h2 class="fw-bold text-gray-900">Info Domisili</h2>
                                    <!--end::Title-->
                                    <!--begin::Notice-->
                                    <div class="text-muted fw-semibold fs-6">
                                        Jika Anda memerlukan informasi lebih lanjut, silakan cek 
                                        <a href="#" class="link-primary fw-bold">Halaman Bantuan</a>.
                                    </div>
                                    <!--end::Notice-->
                                </div>
                                <!--end::Heading-->
                                <div class="row mb-5">
                                    <div class="col-12 col-md-4 mb-5">
                                        <label class="required fs-6 fw-semibold form-label mb-2" for="status_kepemilikan_rumah">Pilih Status Kepemilikan Rumah:</label>
                                        <!--end::Label-->
                                        <!--begin::Input-->
                                        @php
                                        $data = [
                                            '' => 'Pilih Status Kepemilikan Rumah',
                                            'Rumah Pribadi' => 'Rumah Pribadi',
                                            'Rumah Orangtua' => 'Rumah Orangtua',
                                            'Apartment' => 'Apartment',
                                            'Kantor' => 'Kantor',
                                            'Kost/Kontrakan' => 'Kost/Kontrakan'
                                        ];
                                        @endphp
                                        <x-form-select 
                                            name="status_kepemilikan_rumah" 
                                            class="form-select form-select-solid fw-bold"
                                            :options="$data"
                                        />
                                    </div>
                                    <div class="col-12 col-md-4 mb-5">
                                        <label class="required fs-6 fw-semibold form-label mb-2" for="propinsi">Pilih Propinsi</label>
                                        <!--end::Label-->
                                        <!--begin::Input-->
                                        @php
                                        $propinsi = State::where('country_id', 103)->pluck('name', 'id')->prepend('Pilih Propinsi', '');
                                        @endphp
                                        <x-form-select 
                                            name="propinsi" 
                                            class="form-select form-select-solid fw-bold"
                                            modifier="live"
                                            :options="$propinsi"
                                        />
                                    </div>
                                    <div class="col-12 col-md-4 mb-5">
                                        <label class="required fs-6 fw-semibold form-label mb-2" for="kota">Pilih Kota</label>
                                        <!--end::Label-->
                                        <!--begin::Input-->
                                        @php
                                        $kota = City::where('state_id', $this->propinsi)
                                            ->get(['id', 'name', 'type'])
                                            ->mapWithKeys(function ($item) {
                                                return [$item->id => $item->type . ' ' . $item->name];
                                            })
                                            ->prepend('Pilih Kota', '');
                                        @endphp
                                        <x-form-select 
                                            name="kota" 
                                            class="form-select form-select-solid fw-bold"
                                            modifier="live"
                                            :options="$kota"
                                        />
                                    </div>
                                </div>
                                <div class="row mb-5">
                                    <div class="col-12 col-md-4 mb-5">
                                        <label class="required fs-6 fw-semibold form-label mb-2" for="kecamatan">Pilih Kecamatan</label>
                                        <!--end::Label-->
                                        <!--begin::Input-->
                                        @php
                                        $kecamatan = Subdistrict::where('city_id', $this->kota)->pluck('name', 'id')->prepend('Pilih Kecamatan', '');
                                        @endphp
                                        <x-form-select 
                                            name="kecamatan" 
                                            class="form-select form-select-solid fw-bold"
                                            modifier="live"
                                            :options="$kecamatan"
                                        />
                                    </div>
                                    <div class="col-12 col-md-4 mb-5">
                                        <label class="required fs-6 fw-semibold form-label mb-2" for="kelurahan">Pilih Kelurahan</label>
                                        <!--end::Label-->
                                        <!--begin::Input-->
                                        @php
                                        $kelurahan = Village::where('subdistrict_id', $this->kecamatan)->pluck('name', 'id')->prepend('Pilih Kelurahan', '');
                                        @endphp
                                        <x-form-select 
                                            name="kelurahan" 
                                            class="form-select form-select-solid fw-bold"
                                            modifier="live"
                                            :options="$kelurahan"
                                        />
                                    </div>
                                    <div class="col-12 col-md-4 mb-5">
                                        <x-form-group name="kode_pos" label="Kode Pos" required>
                                            <x-form-input type="tel" name="kode_pos" readonly class="bg-transparent" id="kode_pos"/>
                                        </x-form-group>
                                    </div>
                                </div>
                                <div class="row mb-5">
                                    <div class="col-12 col-md-6 mb-5">
                                        <x-form-group name="alamat" label="Alamat" required>
                                            <x-form-textarea name="alamat" class="bg-transparent" id="alamat"/>
                                        </x-form-group>
                                    </div>
                                    <div class="col-12 col-md-6 mb-5">
                                        <x-form-group name="catatan" label="Catatan" required>
                                            <x-form-textarea name="catatan" class="bg-transparent" id="catatan"/>
                                        </x-form-group>
                                    </div>
                                </div>
                                <input type="hidden" wire:model="lat">
                                <input type="hidden" wire:model="lng">
                            </div>
                            <!--end::Wrapper-->
                        </div>
                        <!--end::Step 3-->
                        <!--begin::Step 4-->
                        <div data-kt-stepper-element="content" wire:ignore.self>
                            <!--begin::Wrapper-->
                            <div class="w-100">
                                <!--begin::Heading-->
                                <div class="pb-10 pb-lg-15">
                                    <!--begin::Title-->
                                    <h2 class="fw-bold text-gray-900">Info Keluarga</h2>
                                    <!--end::Title-->
                                    <!--begin::Notice-->
                                    <div class="text-muted fw-semibold fs-6">
                                        Jika Anda memerlukan informasi lebih lanjut, silakan cek 
                                        <a href="#" class="link-primary fw-bold">Halaman Bantuan</a>.
                                    </div>
                                    <!--end::Notice-->
                                </div>
                                <!--end::Heading-->
                                <!--begin::Input group-->
                                <div class="row">
                                    <div class="col-12 col-md-6 mb-5">
                                        <x-form-group name="kk" label="Kartu Keluarga" required>
                                            <x-form-input type="file" accept="image/*" name="kk" class="bg-transparent" id="kk"/>
                                        </x-form-group>
                                    </div>
                                </div>
                                <div id="repeater_keluarga">
                                    <x-form-group>
                                        <div data-repeater-list="repeater_keluarga">
                                            @foreach($keluarga as $index => $keluarga)
                                            <div data-repeater-item>
                                                <x-form-group class="row">
                                                    <div class="col-12 col-md-3 mb-3 mb-md-5">
                                                        <label class="fs-6 fw-semibold form-label mb-2 required" for="keluarga.{{ $index }}.status_keluarga">Pilih Status Keluarga:</label>
                                                        <!--end::Label-->
                                                        <!--begin::Input-->
                                                        @php
                                                        $data_status_keluarga = [
                                                            '' => 'Pilih status keluarga',
                                                            'Kepala Keluarga' => 'Kepala Keluarga',
                                                            'Suami' => 'Suami',
                                                            'Istri' => 'Istri',
                                                            'Anak' => 'Anak',
                                                            'Menantu' => 'Menantu',
                                                            'cucu' => 'cucu',
                                                            'Orangtua' => 'Orangtua',
                                                            'Mertua' => 'Mertua',
                                                            'Famili Lain' => 'Famili Lain',
                                                            'Pembantu' => 'Pembantu',
                                                            'Lainnya' => 'Lainnya',
                                                        ];
                                                        @endphp
                                                        <x-form-select 
                                                            name="keluarga.{{ $index }}.status_keluarga" 
                                                            class="form-select form-select-solid fw-bold"
                                                            :options="$data_status_keluarga"
                                                        />
                                                    </div>
                                                    <div class="col-12 col-md-3 mb-3 mb-md-5">
                                                        <x-form-group name="keluarga.{{ $index }}.nama_keluarga" label="Nama Keluarga" required>
                                                            <x-form-input type="text" name="keluarga.{{ $index }}.nama_keluarga" class="bg-transparent" id="nama_keluarga.{{ $index }}"/>
                                                        </x-form-group>
                                                    </div>
                                                    <div class="col-12 col-md-3 mb-3 mb-md-5">
                                                        <x-form-group label="No HP" required>
                                                            <x-form-input-group>
                                                                <x-form-input-group-text>+62</x-form-input-group-text>
                                                                <x-form-input type="text" name="keluarga.{{ $index }}.no_hp_keluarga" class="bg-transparent" placeholder="8123456789" id="keluarga.{{ $index }}.no_hp_keluarga">
                                                                    @slot('help')
                                                                    <small class="form-text text-muted">
                                                                        Masukkan nomor HP keluarga Anda tanpa angka 0 atau +62 dan - pada form diatas sesuai dengan contoh.
                                                                    </small>
                                                                    @endslot
                                                                </x-form-input>
                                                            </x-form-input-group>
                                                        </x-form-group>
                                                    </div>
                                                    <div class="col-md-3 mb-5">
                                                        <x-button href="removeKeluarga({{$index}})" class="btn btn-sm btn-light-danger mt-3 mt-md-8" icon="ki-outline ki-trash fs-5" label="Hapus" />
                                                    </div>
                                                </x-form-group>
                                            </div>
                                            @endforeach
                                        </div>
                                    </x-form-group>
                                    <!--begin::Form group-->
                                    <div class="form-group mt-3">
                                        <x-button href="addKeluarga" class="btn btn-light-primary w-100 w-md-auto" icon="ki-duotone ki-plus fs-3" label="Tambah Anggota Keluarga"/>
                                    </div>
                                    <!--end::Form group-->
                                </div>
                                <!--end::Input group-->
                            </div>
                            <!--end::Wrapper-->
                        </div>
                        <!--end::Step 4-->
                        <!--begin::Step 5-->
                        <div data-kt-stepper-element="content" wire:ignore.self>
                            <!--begin::Wrapper-->
                            <div class="w-100">
                                <!--begin::Heading-->
                                <div class="pb-8 pb-lg-10">
                                    <!--begin::Title-->
                                    <h2 class="fw-bold text-gray-900">Anda Sudah Selesai!</h2>
                                    <!--end::Title-->
                                    <!--begin::Notice-->
                                    {{-- <div class="text-muted fw-semibold fs-6">Jika Anda memerlukan informasi lebih lanjut, silakan
                                    <a href="authentication/layouts/corporate/sign-in.html" class="link-primary fw-bold">Sign In</a>.</div> --}}
                                    <!--end::Notice-->
                                </div>
                                <!--end::Heading-->
                                <!--begin::Body-->
                                <div class="mb-0">
                                    <!--begin::Text-->
                                    <div class="fs-6 text-gray-600 mb-5">
                                        
                                    </div>
                                    <!--end::Text-->
                                    <!--begin::Alert-->
                                    <!--begin::Notice-->
                                    <div class="notice d-flex bg-light-warning rounded border-warning border border-dashed p-6">
                                        <!--begin::Icon-->
                                        <i class="ki-outline ki-information fs-2tx text-warning me-4"></i>
                                        <!--end::Icon-->
                                        <!--begin::Wrapper-->
                                        <div class="d-flex flex-stack flex-grow-1">
                                            <!--begin::Content-->
                                            <div class="fw-semibold">
                                                <h4 class="text-gray-900 fw-bold">Sebutkan email kalian ke dm instagram untuk verifikasi✅</h4>
                                                {{-- <div class="fs-6 text-gray-700">To start using great tools, please, 
                                                <a href="utilities/wizards/vertical.html" class="fw-bold">Create Team Platform</a></div> --}}
                                            </div>
                                            <!--end::Content-->
                                        </div>
                                        <!--end::Wrapper-->
                                    </div>
                                    <!--end::Notice-->
                                    <!--end::Alert-->
                                </div>
                                <!--end::Body-->
                            </div>
                            <!--end::Wrapper-->
                        </div>
                        <!--end::Step 5-->
                        <!--begin::Actions-->
                        <div class="d-flex flex-column flex-md-row justify-content-between pt-10 gap-2">
                            <!--begin::Wrapper-->
                            <div>
                                @if($this->step > 1)
                                    <x-button class="btn btn-lg btn-light-primary w-100" href="navigateStep('back',{{ $this->step }})" indicator="Harap tunggu..." label="Kembali" icon="ki-outline ki-arrow-left fs-4 me-1"/>
                                @endif
                            </div>
                            <!--end::Wrapper-->
                            <!--begin::Wrapper-->
                            <div>
                                @if($this->step < 5)
                                    <x-button class="btn btn-lg btn-primary w-100" href="navigateStep('next',{{ $this->step }})" indicator="Harap tunggu..." label="Selanjutnya" icon="ki-outline ki-arrow-right fs-4 ms-1 me-0"/>
                                @else
                                    <x-button class="btn btn-lg btn-primary w-100" id="tombol_simpan_onboarding" submit="true" indicator="Harap tunggu..." label="Simpan Data" icon="ki-outline ki-check fs-4 ms-1 me-0"/>
                                @endif
                            </div>
                            <!--end::Wrapper-->
                        </div>
                        <!--end::Actions-->
                    </x-form>
                    <!--end::Form-->
                </div>
                <!--end::Content-->
            </div>
            @endrole
            <!--end::Stepper-->
        </div>
    </div>
    @section('custom_js')
    <script data-navigate-once src="{{asset('js/onboarding.js')}}"></script>
    <script data-navigate-once src="{{asset('plugins/custom/formrepeater/formrepeater.bundle.js')}}"></script>
    <script data-navigate-once>
        function requestLocationPermission() {
            navigator.geolocation.getCurrentPosition(
                (position) => {
                    // Berhasil mendapatkan lokasi
                    const lat = position.coords.latitude;
                    const lng = position.coords.longitude;
                    console.log(lat + ':' + lng);
                    // Kirim data ke Livewire
                    @this.setLatitudes(lat);
                    @this.setLongitudes(lng);
                    
                    // Panggil fungsi lain jika diperlukan
                    checkLocationPermission();
                },
                (error) => {
                    // Gagal mendapatkan lokasi
                    console.error("Error getting location", error);
                    checkLocationPermission();
                },
                {
                    enableHighAccuracy: true, // Akurasi tinggi
                    timeout: 10000,           // Timeout 10 detik
                    maximumAge: 0              // Jangan gunakan cache
                }
            );
        }
        // requestNotificationPermission();
        // requestMicrophonePermission();
        // requestCameraPermission()();
        // requestLocation();
        // pickContacts();
        $('#repeater_keluarga').repeater({
            initEmpty: false,
            show: function () {
                $(this).slideDown();
                // Add new empty keluarga to Livewire state
                @this.call('addKeluarga');
            },
            hide: function (deleteElement) {
                $(this).slideUp(deleteElement);
                // Remove keluarga from Livewire state
                @this.call('removeKeluarga', $(this).index());
            }
        });
    </script>
    @endsection
    @endvolt
</x-app>