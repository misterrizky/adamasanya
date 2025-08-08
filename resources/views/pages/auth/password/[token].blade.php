<?php
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Auth\Events\PasswordReset;
use function Livewire\Volt\{state, rules};
use function Laravel\Folio\{middleware, name};
middleware(['guest']);
name('password.reset');

state([
    'email' => fn() => request()->query('email', ''),
    'token' => fn() => $token,
])->locked();
state(['kata_sandi', 'ulangi_kata_sandi']);
rules(fn () => [
    'kata_sandi' => ['required','min:8','same:ulangi_kata_sandi']
]);

$resetPassword = function(){
    $this->validate();
    $email = $this->email;
    $token = $this->token;
    $response = Password::broker()->reset(
        [
            'token' => $token,
            'email' => $email,
            'password' => $this->kata_sandi
        ],
        function ($user, $password) {
            $user->password = Hash::make($password);
            $user->setRememberToken(Str::random(60));
            $user->save();
            event(new PasswordReset($user));
        }
    );
    if ($response == Password::PASSWORD_RESET) {
        return $this->redirect(route('login'), navigate: true);
    }
    $this->dispatch('toast-info', message: $response);
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
                    <x-form class="form w-100" action="resetPassword">
                        <!--begin::Heading-->
                        <div class="text-center mb-11">
                            <!--begin::Title-->
                            <h1 class="text-gray-900 fw-bolder mb-3">Siapkan Kata Sandi Baru</h1>
                            <!--end::Title-->
                            <!--begin::Subtitle-->
                            <div class="text-gray-500 fw-semibold fs-6">
                                Apakah Anda sudah mengatur ulang kata sandi?
                                <a href="{{ route('login') }}" wire:navigate class="link-primary fw-bold">Masuk</a>
                            </div>
                            <!--end::Subtitle=-->
                        </div>
                        <!--begin::Heading-->
                        <!--begin::Input group=-->
                        <div class="fv-row mb-8">
                            <!--begin::Email-->
                            <x-form-input name="kata_sandi" type="password" placeholder="Masukkan Kata Sandi Baru Anda"/>
                            <!--end::Email-->
                        </div>
                        <div class="fv-row mb-8">
                            <!--begin::Email-->
                            <x-form-input name="ulangi_kata_sandi" type="password" placeholder="Masukkan Ulang Kata Sandi Baru Anda"/>
                            <!--end::Email-->
                        </div>
                        <!--begin::Submit button-->
                        <div class="d-grid">
                            <x-base.button class="btn btn-primary" id="tombol_reset" submit="true" indicator="Harap tunggu..." label="Kirim" />
                        </div>
                        <!--end::Submit button-->
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