<?php
use Illuminate\Support\Facades\Hash;
use function Livewire\Volt\{rules, state};
use function Laravel\Folio\name;
name('profile.pin');

state(['user' => fn () => \App\Models\User::where('id', Auth::user()->id)->first()]);
state([
    'code_1' => '',
    'code_2' => '',
    'code_3' => '',
    'code_4' => '',
    'code_5' => '',
    'code_6' => ''
]);
rules(fn () => [
    'code_1' => 'required|digits:1',
    'code_2' => 'required|digits:1',
    'code_3' => 'required|digits:1',
    'code_4' => 'required|digits:1',
    'code_5' => 'required|digits:1',
    'code_6' => 'required|digits:1'
]);

$savePin = function(){
    // Combine all PIN digits
    $pin = $this->code_1 . $this->code_2 . $this->code_3 . 
           $this->code_4 . $this->code_5 . $this->code_6;
    // Validate that all digits are filled
    if(strlen($pin) !== 6) {
        return;
    }
    
    // Update user's PIN
    $this->user->castAndUpdate([
        'pin' => Hash::make($pin)
    ]);
    
    // Redirect or show success message
    // $this->redirect(route('profile.security'), navigate: true);
}
?>
<x-app>
    <x-toolbar-mobile 
        :breadcrumbs="[
            ['icon' => 'arrow-left', 'url' => route('profile.security')],
        ]"
    />
    <style>
        .pin-wrapper {
            display: flex;
            justify-content: center;
            margin-top: 20px;
            margin-bottom: 20px;
        }
        .pin-box {
            width: 50px;
            height: 50px;
            text-align: center;
            font-size: 24px;
            margin: 0 5px;
            background-color: #1e1e1e;
            border: 1px solid #444;
            color: white;
            border-radius: 8px;
        }
    </style>
    @volt
    <div id="kt_app_content" class="app-content flex-column-fluid">
        <div class="d-flex flex-center flex-column align-items-stretch h-lg-100 w-md-400px">
            <div class="d-flex flex-center flex-column flex-column-fluid pb-15 pb-lg-20">
                <x-form class="form w-100 mb-13" novalidate="novalidate" id="form_pin" action="savePin">
                    <div class="text-center mb-10">
                        <img alt="Logo" class="mh-125px theme-light-show" src="{{asset('media/illustrations/icons/lock.png')}}" />
                        <img alt="Logo" class="mh-125px theme-dark-show" src="{{asset('media/illustrations/icons/lock-dark.png')}}" />
                    </div>
                    <div class="text-center mb-10">
                        <h1 class="text-gray-900 mb-3">PIN Baru {{ config('app.name') }}</h1>
                        <div class="text-muted fw-semibold fs-5 mb-5">
                            Masukkan PIN Baru kamu
                        </div>
                    </div>
                    <div class="mb-10">
                        <div class="pin-wrapper">
                            <input type="text" wire:model="code_1" data-inputmask="'mask': '9', 'placeholder': ''" maxlength="1" class="form-control bg-transparent pin-box h-60px w-60px fs-2qx text-center mx-1 my-2" value="" />
                            <input type="text" wire:model="code_2" data-inputmask="'mask': '9', 'placeholder': ''" maxlength="1" class="form-control bg-transparent pin-box h-60px w-60px fs-2qx text-center mx-1 my-2" value="" />
                            <input type="text" wire:model="code_3" data-inputmask="'mask': '9', 'placeholder': ''" maxlength="1" class="form-control bg-transparent pin-box h-60px w-60px fs-2qx text-center mx-1 my-2" value="" />
                            <input type="text" wire:model="code_4" data-inputmask="'mask': '9', 'placeholder': ''" maxlength="1" class="form-control bg-transparent pin-box h-60px w-60px fs-2qx text-center mx-1 my-2" value="" />
                            <input type="text" wire:model="code_5" data-inputmask="'mask': '9', 'placeholder': ''" maxlength="1" class="form-control bg-transparent pin-box h-60px w-60px fs-2qx text-center mx-1 my-2" value="" />
                            <input type="text" wire:model="code_6" data-inputmask="'mask': '9', 'placeholder': ''" maxlength="1" class="form-control bg-transparent pin-box h-60px w-60px fs-2qx text-center mx-1 my-2" value="" />
                        </div>
                    </div>
                    <div class="fw-bold text-gray-900 fs-3">
                        Hindari menggunakan kombinasi angka yang mudah ditebak, seperti tanggal lahir atau nomor telepon.
                    </div>
                </x-form>
            </div>
        </div>
        <script data-navigate-once>
            // Autofocus PIN input
            const inputs = document.querySelectorAll(".pin-box");
            inputs.forEach((input, index) => {
                input.addEventListener("input", () => {
                    if (input.value.length === 1 && index < inputs.length - 1) {
                        inputs[index + 1].focus();
                    }
                    
                    // Check if all inputs are filled
                    const allFilled = Array.from(inputs).every(input => input.value.length === 1);
                    if(allFilled) {
                        // Submit the form
                        document.getElementById("form_pin").dispatchEvent(new Event('submit'));
                    }
                });
                
                // Also handle paste event for better UX
                input.addEventListener("paste", (e) => {
                    e.preventDefault();
                    const pasteData = e.clipboardData.getData('text').trim();
                    if(/^\d{6}$/.test(pasteData)) {
                        inputs.forEach((input, idx) => {
                            input.value = pasteData[idx] || '';
                            // Trigger Livewire model update
                            const event = new Event('input', { bubbles: true });
                            input.dispatchEvent(event);
                        });
                        document.getElementById("form_pin").dispatchEvent(new Event('submit'));
                    }
                });
            });
        </script>
    </div>
    @endvolt
</x-app>