<?php
use Livewire\Volt\Component;
use Livewire\Attributes\Validate;
use Illuminate\Support\Facades\Password;
use function Laravel\Folio\{middleware, name};

middleware(['guest']);
name('password.request');

new class extends Component
{
    #[Validate('required|email|exists:users,email|regex:/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/')]
    public $email = '';
    public function send()
    {
        $this->validate();
        $response = Password::broker()->sendResetLink(['email' => $this->email]);

        if ($response === Password::RESET_LINK_SENT) {
            return $this->redirect(route('login'), navigate: true);
        } else {
            $this->dispatch('toast-error', message: __($response));
            return;
        }
        $this->dispatch('toast-info', message: __($response));
    }
};
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
                    <x-form class="form w-100" action="send">
                        <!--begin::Heading-->
                        <div class="text-center mb-11">
                            <!--begin::Title-->
                            <h1 class="text-gray-900 fw-bolder mb-3">Lupa Kata Sandi ?</h1>
                            <!--end::Title-->
                            <!--begin::Subtitle-->
                            <div class="text-gray-500 fw-semibold fs-6">Masukkan email Anda untuk mengatur ulang kata sandi Anda.</div>
                            <!--end::Subtitle=-->
                        </div>
                        <!--begin::Heading-->
                        <!--begin::Input group=-->
                        <div class="fv-row mb-8">
                            <!--begin::Email-->
                            <x-form-input name="email" type="email" placeholder="Masukkan Email Anda"/>
                            <!--end::Email-->
                        </div>
                        <!--begin::Submit button-->
                        <div class="d-flex flex-wrap justify-content-center pb-lg-0">
                            <x-base.button class="btn btn-primary" id="tombol_daftar" submit="true" indicator="Harap tunggu..." label="Kirim" />
                            <a href="{{ route('login') }}" wire:navigate class="btn btn-light ms-3">Batalkan</a>
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