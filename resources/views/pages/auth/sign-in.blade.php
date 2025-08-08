<?php

use App\Models\User;
use App\Models\Passkey;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Livewire\Volt\Component;
use Illuminate\Auth\Events\Login;
use Livewire\Attributes\Validate;
use Illuminate\Support\Facades\Auth;
use function Livewire\Volt\{rules, state};
use function Laravel\Folio\{middleware, name};
middleware(['guest']);
name('login');

new class extends Component
{
    #[Validate('required|email|exists:users,email|regex:/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/')]
    public $email = '';
    #[Validate('required|min:8')]
    public $kata_sandi = '';
    #[Validate('bool')]
    public $rememberMe = false;
    public function updatedEmail()
    {
        $this->validate([
            'email' => 'required|exists:users,email|regex:/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/',
        ]);
    }
    public function authenticate()
    {
        $this->validate();
        $user = User::where('email', $this->email)->first();
        if (!$user) {
            $this->dispatch('toast-info', message: "Akun tidak ditemukan.");
            return;
        }
        if ($user->banned_at !== null) {
            $this->dispatch('toast-info', message: "Anda telah diblokir. Silakan hubungi admin.");
            return;
        }
        if (Auth::attemptWhen([
            'email' => $this->email,
            'password' => $this->kata_sandi
        ], function (User $user) {
            return $user->banned_at === null
                && $user->deleted_at === null;
        })) {
            $this->dispatch('toast-success', message: "Selamat datang!");
            // event(new Login(auth()->guard('web'), , $this->remember));
            $this->dispatch('check-biometric-support', userId: $user->id);
            $route = $user->getRoleNames()[0] == "Onboarding" || $user->getRoleNames()[0] == "Konsumen" ? route('home') : route('admin.dashboard');
            return $this->redirect($route, navigate: true);
        }
        $this->dispatch('toast-info', message: "Akun tidak ditemukan / Kata sandi salah.");
    }
    public function biometricLogin()
    {
        $this->dispatch('verify-passkey');
    }
    public function generateChallenge(){
        $challenge = Str::random(32);
        
        // Simpan challenge di session untuk verifikasi nanti
        session(['webauthn_challenge' => $challenge]);
        
        return response()->json([
            'challenge' => base64_encode($challenge)
        ]);
    }
    public function getUserData($userId)
    {
        $user = User::findOrFail($userId);
        
        return response()->json([
            'id' => $user->id,
            'email' => $user->email,
            'name' => $user->name
        ]);
    }
    public function registerPasskey(Request $request)
    {
        $request->validate([
            'id' => 'required',
            'rawId' => 'required',
            'type' => 'required',
            'response.attestationObject' => 'required',
            'response.clientDataJSON' => 'required'
        ]);

        $user = Auth::user();
        
        // Simpan passkey ke database
        Passkey::create([
            'user_id' => $user->id,
            'credential_id' => $request->id,
            'public_key' => json_encode($request->all()),
            'created_at' => now()
        ]);

        return response()->json(['success' => true]);
    }

    // Verifikasi passkey untuk login
    public function verifyPasskey(Request $request)
    {
        $request->validate([
            'id' => 'required',
            'rawId' => 'required',
            'type' => 'required',
            'response.authenticatorData' => 'required',
            'response.clientDataJSON' => 'required',
            'response.signature' => 'required'
        ]);

        // Cari passkey di database
        $passkey = Passkey::where('credential_id', $request->id)->first();
        
        if (!$passkey) {
            return response()->json(['success' => false, 'message' => 'Credential tidak ditemukan']);
        }

        // Verifikasi signature (dalam implementasi nyata, ini perlu divalidasi dengan library WebAuthn)
        // ...

        $user = $passkey->user;
        $token = $user->createToken('api-token')->plainTextToken;
        
        // Login user
        Auth::login($user);
        
        return response()->json([
            'success' => true,
            'redirect_url' => $user->getRoleNames()[0] == "Onboarding" ? 
                route('onboarding') : route('home')
        ]);
    }
};
?>
<x-auth>
    @volt
    <!--begin::Body-->
    <div class="d-flex flex-column-fluid flex-lg-row-auto justify-content-center justify-content-lg-end p-12">
        <!--begin::Wrapper-->
        <div class="bg-body d-flex flex-column flex-center rounded-4 w-md-600px p-10">
            <!--begin::Content-->
            <div class="d-flex flex-center flex-column align-items-stretch h-lg-100 w-md-400px">
                <!--begin::Wrapper-->
                <div class="d-flex flex-center flex-column flex-column-fluid pb-15 pb-lg-20">
                    <!--begin::Form-->
                    <x-form class="form w-100" action="authenticate">
                        <!--begin::Heading-->
                        <div class="text-center mb-11">
                            <h1 class="text-gray-900 fw-bolder mb-3">Masuk</h1>
                            <div class="text-gray-500 fw-semibold fs-6">Demi keamanan Anda, harap verifikasi identitas Anda.</div>
                        </div>
                        <!--begin::Heading-->
                        
                        <!--begin::Input group-->
                        <div class="fv-row mb-8">
                            <x-form-input name="email" class="bg-transparent" autofocus placeholder="Masukkan Email Anda" />
                        </div>
                        <!--end::Input group-->
                        
                        <div class="fv-row mb-3">
                            <x-form-input type="password" name="kata_sandi" class="bg-transparent" placeholder="Masukkan Kata Sandi Anda"/>
                        </div>
                        <!--end::Input group-->
                        
                        <!--begin::Wrapper-->
                        <div class="d-flex flex-stack flex-wrap gap-3 fs-base fw-semibold mb-8">
                            <div></div>
                            <a href="{{ route('password.request') }}" wire:navigate class=" ">Lupa Kata Sandi?</a>
                        </div>
                        <!--end::Wrapper-->
                        
                        <!--begin::Biometric Login-->
                        <div class="d-flex flex-center mb-10 d-none">
                            <button type="button" wire:click="biometricLogin" class="btn btn-light-primary fw-bold" id="biometric-login-btn">
                                <i class="ki-filled ki-fingerprint-scanning fs-2 me-2">
                                </i>
                                Login dengan Biometric
                            </button>
                        </div>
                        <!--end::Biometric Login-->
                        
                        <!--begin::Submit button-->
                        <div class="d-grid mb-10">
                            <x-button class="btn btn-primary" id="tombol_login" submit="true" indicator="Harap tunggu..." label="Masuk" />
                        </div>
                        <!--end::Submit button-->
                        
                        <!--begin::Sign up-->
                        <div class="text-gray-500 text-center fw-semibold fs-6">
                            Belum punya akun? 
                            <a href="{{ route('sign-up') }}" wire:navigate class="link-primary">Buat Akun</a>
                        </div>
                        <!--end::Sign up-->
                    </x-form>
                    <!--end::Form-->
                </div>
                <!--end::Wrapper-->
            </div>
            <!--end::Content-->
        </div>
        <!--end::Wrapper-->
    </div>
    <!--end::Body-->
    @section('custom_js')
    <script data-navigate-once>
        // Improved biometric support check
        function checkBiometricSupport() {
            return !!navigator.credentials && 
                !!navigator.credentials.create && 
                !!navigator.credentials.get;
        }

        // Hide biometric button if not supported
        function hideBiometricButton() {
            const biometricBtn = document.getElementById('biometric-login-btn');
            if (biometricBtn && !checkBiometricSupport()) {
                biometricBtn.style.display = 'none';
            }
        }

        // Initialize when DOM is loaded or Livewire navigated
        document.addEventListener('DOMContentLoaded', hideBiometricButton);
        document.addEventListener('livewire:navigated', hideBiometricButton);

        // Handle event from Livewire for passkey verification
        document.addEventListener('verify-passkey', async () => {
            try {
                if (!checkBiometricSupport()) {
                    toastr.info("Browser Anda tidak mendukung login biometric");
                    return;
                }

                // Get challenge from server
                const response = await fetch(@this.generateChallenge, {
                    method: 'GET',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });
                
                if (!response.ok) throw new Error('Failed to get challenge');
                
                const data = await response.json();
                const challenge = Uint8Array.from(atob(data.challenge), c => c.charCodeAt(0));

                // Verify with biometric
                const credentials = await navigator.credentials.get({
                    publicKey: {
                        challenge: challenge,
                        timeout: 60000,
                        userVerification: "preferred"
                    }
                });

                // Prepare credential data for server
                const credentialData = {
                    id: credentials.id,
                    rawId: Array.from(new Uint8Array(credentials.rawId)),
                    type: credentials.type,
                    response: {
                        authenticatorData: Array.from(new Uint8Array(credentials.response.authenticatorData)),
                        clientDataJSON: Array.from(new Uint8Array(credentials.response.clientDataJSON)),
                        signature: Array.from(new Uint8Array(credentials.response.signature)),
                        userHandle: credentials.response.userHandle ? 
                            Array.from(new Uint8Array(credentials.response.userHandle)) : null
                    }
                };

                // Send credential to server
                const authResponse = await fetch(@this.verifyPasskey, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify(credentialData)
                });

                if (!authResponse.ok) throw new Error('Verification failed');

                const authResult = await authResponse.json();

                if (authResult.success) {
                    toastr.success("Login dengan biometric berhasil");
                    window.location.href = authResult.redirect_url;
                } else {
                    toastr.error("Autentikasi biometric gagal");
                }
            } catch (error) {
                console.error('Error verifying passkey:', error);
                toastr.error("Autentikasi biometric gagal atau dibatalkan");
            }
        });

        // Event to offer biometric registration after regular login
        document.addEventListener('check-biometric-support', (event) => {
            const userId = event.detail.userId;
            if (checkBiometricSupport()) {
                // Show dialog to offer biometric registration
                if (confirm("Apakah Anda ingin mengaktifkan login biometric untuk akun ini?")) {
                    registerPasskey(userId);
                }
            }
        });

        // Function to register passkey
        async function registerPasskey(userId) {
            try {
                // Get user data from server
                const userResponse = await fetch(@this.getUserData(userId));
                if (!userResponse.ok) throw new Error('Failed to get user data');
                
                const userData = await userResponse.json();

                // Get challenge from server
                const challengeResponse = await fetch(@this.generateChallenge, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ email: userData.email })
                });
                
                if (!challengeResponse.ok) throw new Error('Failed to get challenge');
                
                const challengeData = await challengeResponse.json();
                const challenge = Uint8Array.from(atob(challengeData.challenge), c => c.charCodeAt(0));

                // Create new credential
                const credentials = await navigator.credentials.create({
                    publicKey: {
                        challenge: challenge,
                        rp: {
                            name: "Nama Aplikasi Anda",
                            id: window.location.hostname
                        },
                        user: {
                            id: Uint8Array.from(userId, c => c.charCodeAt(0)),
                            name: userData.email,
                            displayName: userData.name
                        },
                        pubKeyCredParams: [
                            { type: "public-key", alg: -7 },  // ES256
                            { type: "public-key", alg: -257 }  // RS256
                        ],
                        timeout: 60000,
                        authenticatorSelection: {
                            residentKey: "preferred",
                            userVerification: "preferred"
                        },
                        attestation: "none"
                    }
                });

                // Prepare credential data for server
                const credentialData = {
                    id: credentials.id,
                    rawId: Array.from(new Uint8Array(credentials.rawId)),
                    type: credentials.type,
                    response: {
                        attestationObject: Array.from(new Uint8Array(credentials.response.attestationObject)),
                        clientDataJSON: Array.from(new Uint8Array(credentials.response.clientDataJSON))
                    }
                };

                // Register passkey with server
                const registerResponse = await fetch(@this.registerPasskey, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify(credentialData)
                });

                if (!registerResponse.ok) throw new Error('Registration failed');

                toastr.success("Login biometric berhasil didaftarkan");
            } catch (error) {
                console.error('Error registering passkey:', error);
                toastr.error("Gagal mendaftarkan login biometric");
            }
        }
    </script>
    @endsection
    @endvolt
</x-auth>