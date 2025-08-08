<?php
use App\Models\User;
use App\Rules\ValidEmailDomain;
use Illuminate\Support\Facades\Auth;
// use Illuminate\Support\Str;
// use App\Notifications\WelcomeNotification;
// use Illuminate\Support\Facades\Notification;
use App\Notifications\WelcomeNotification;
use function Livewire\Volt\{rules, state};
use LevelUp\Experience\Models\Achievement;
use function Laravel\Folio\{middleware, name};
middleware(['guest']);
name('sign-up');

state([
    'nama' => '',
    'email' => '',
    'no_hp' => '',
    'kata_sandi' => '',
    'ulangi_kata_sandi' => '',
    'setuju' => false,
]);
rules(fn () => [
    'nama' => ['required','max:255'],
    'email' => ['required','email','unique:users,email','max:255',new ValidEmailDomain],
    'no_hp' => ['required','unique:users,phone','numeric','digits_between:9,12'],
    'kata_sandi' => ['required','min:8','same:ulangi_kata_sandi'],
    'ulangi_kata_sandi' => ['required','min:8'],
    'setuju' => ['accepted']
]);

$register = function(){
    $this->validate();
    $user = User::castAndCreate([
        'name' => $this->nama,
        'email' => $this->email,
        'phone' => $this->no_hp,
        'st' => 'pending',
        'password' => Hash::make($this->kata_sandi),
    ]);
    $achievement = Achievement::find(1);
    $user->grantAchievement($achievement);
    $user->addPoints(0);
    $user->assignRole('Onboarding');
    $user->notify(new WelcomeNotification());
    // $token = Str::random(64);
    // Verify::castAndCreate([
    //     'email' => $user->email, 
    //     'token' => $token
    // ]);
    // Notification::send($user, new WelcomeNotification($user,$token));
    Auth::login($user, true);
    return $this->redirect(route('home'), navigate: true);
}
?>
<x-auth>
    <!--begin::Body-->
    <div class="d-flex flex-column-fluid flex-lg-row-auto justify-content-center justify-content-lg-end p-12">
        <!--begin::Wrapper-->
        <div class="bg-body d-flex flex-column flex-center rounded-4 w-md-600px p-10">
            <!--begin::Content-->
            <div class="d-flex flex-center flex-column align-items-stretch h-lg-100 w-md-400px">
                <!--begin::Wrapper-->
                <div class="d-flex flex-center flex-column flex-column-fluid pb-15 pb-lg-20">
                    <!--begin::Form-->
                    @volt
                    <x-form class="form w-100" action="register">
                        <!--begin::Heading-->
                        <div class="text-center mb-11">
                            <!--begin::Title-->
                            <h1 class="text-gray-900 fw-bolder mb-3">Daftar</h1>
                            <!--end::Title-->
                            <!--begin::Subtitle-->
                            <div class="text-gray-500 fw-semibold fs-6">Satu ID untuk mengakses semua layanan.</div>
                            <!--end::Subtitle=-->
                        </div>
                        <!--begin::Heading-->
                        <!--begin::Input group=-->
                        <div class="fv-row mb-8">
                            <!--begin::Nama-->
                            <x-form-input type="text" name="nama" class="bg-transparent" label="Nama Lengkap" placeholder="Masukkan Nama Lengkap Anda" autofocus/>
                            <!--end::Nama-->
                        </div>
                        <!--end::Input group=-->
                        <!--begin::Input group=-->
                        <div class="fv-row mb-8">
                            <!--begin::Email-->
                            <x-form-input type="email" name="email" class="bg-transparent" label="Email" placeholder="Masukkan Email Anda"/>
                            <!--end::Email-->
                        </div>
                        <!--end::Input group=-->
                        <!--begin::Input group=-->
                        <div class="fv-row mb-8">
                            <!--begin::No HP-->
                            <x-form-input-group label="Nomor HP" >
                                <x-form-input-group-text>+62</x-form-input-group-text>
                                <x-form-input type="tel" name="no_hp" class="bg-transparent" placeholder="8123456789" id="no_hp">
                                    @slot('help')
                                    <small class="form-text text-muted">
                                        Masukkan nomor ponsel Anda tanpa angka 0 atau +62 dan - pada form diatas sesuai dengan contoh.
                                    </small>
                                    @endslot
                                </x-form-input>
                            </x-form-input-group>
                            <!--end::No HP-->
                        </div>
                        <!--end::Input group=-->
                        <!--begin::Input group=-->
                        <div class="fv-row mb-8">
                            <!--begin::Password-->
                            <x-form-input type="password" name="kata_sandi" class="bg-transparent" placeholder="Masukkan Kata Sandi Anda"/>
                            <!--end::Password-->
                        </div>
                        <!--end::Input group=-->
                        <!--begin::Input group=-->
                        <div class="fv-row mb-8">
                            <!--begin::Password-->
                            <x-form-input type="password" name="ulangi_kata_sandi" class="bg-transparent" label="Ulangi Kata Sandi" placeholder="Masukkan Ulang Kata Sandi Anda"/>
                            <x-form-input-group class="mt-8">
                                <x-form-checkbox id="setuju_signup" name="setuju" label="Saya telah membaca dan setuju dengan">
                                @slot('help')
                                    <a style="font-weight:bold" href="#" data-bs-toggle="modal" data-bs-target="#ModalSnK">Syarat dan Ketentuan {{ config('app.name') }}</a>
                                @endslot
                                </x-form-checkbox>
                            </x-form-input-group>
                            <!--end::Password-->
                        </div>
                        <!--end::Input group-->
                        <!--begin::Submit button-->
                        <div class="d-grid mb-10">
                            <x-button class="btn btn-primary" id="tombol_daftar" submit="true" indicator="Harap tunggu..." label="Daftar" />
                        </div>
                        <!--end::Submit button-->
                        <!--begin::Sign up-->
                        <div class="text-gray-500 text-center fw-semibold fs-6">Sudah punya akun? Masuk
                        <a href="{{ route('login') }}" wire:navigate class="link-primary">disini</a></div>
                        <!--end::Sign up-->
                    </x-form>
                    @endvolt
                    <!--end::Form-->
                </div>
                <!--end::Wrapper-->
            </div>
            <!--end::Content-->
        </div>
        <!--end::Wrapper-->
    </div>
    <!--end::Body-->
</x-auth>