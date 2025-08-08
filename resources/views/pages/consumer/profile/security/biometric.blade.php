<?php
use function Laravel\Folio\name;

name('profile.biometric');
?>

<x-app>
    <x-toolbar-mobile 
        :breadcrumbs="[
            ['icon' => 'arrow-left', 'url' => route('profile.security')],
            ['text' => 'Sidik Jari', 'active' => true]
        ]"
    />
    
    @volt
    <div id="kt_app_content" class="app-content flex-column-fluid">
        <div class="card card-dashed hover-elevate-up parent-hover">
            <div class="card-body d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center">
                    <i class="ki-filled ki-fingerprint-scanning fs-3x"></i>
                    <div class="ms-3">
                        <div class="text-gray-700 fs-4 fw-bold">
                            Masuk dengan Sidik Jari
                        </div>
                        <small class="text-muted">Aktifkan untuk menggunakan sidik jari sebagai metode otentikasi</small>
                    </div>
                </div>
                
                <div class="form-check form-switch form-check-custom form-check-warning form-check-solid">
                    <input 
                        class="form-check-input h-30px w-50px" 
                        type="checkbox" 
                        id="switch_biometric"  
                        onclick="toggleBiometric()"
                    />
                </div>
            </div>
        </div>
    </div>
    @endvolt

    @section('custom_js')
    <script data-navigate-once>
        document.addEventListener('DOMContentLoaded', function() {
            // Check if biometric is already enabled
            checkBiometricStatus();
        });

        function generateRandomChallenge() {
            const length = 32;
            const randomValues = new Uint8Array(length);
            window.crypto.getRandomValues(randomValues);
            return randomValues;
        }

        async function toggleBiometric() {
            const switchElement = document.getElementById('switch_biometric');
            try {
                if (switchElement.checked) {
                    await enableBiometric();
                } else {
                    await disableBiometric();
                }
            } catch (error) {
                console.error('Biometric error:', error);
                switchElement.checked = !switchElement.checked;
                alert('Gagal mengubah pengaturan biometrik: ' + error.message);
            }
        }

        async function enableBiometric() {
            if (!navigator.credentials || !navigator.credentials.create) {
                throw new Error('Browser Anda tidak mendukung Web Authentication API');
            }

            const credentials = await navigator.credentials.create({
                publicKey: {
                    challenge: generateRandomChallenge(),
                    rp: { 
                        name: document.title || 'Aplikasi', 
                        id: window.location.hostname 
                    },
                    user: { 
                        id: new Uint8Array(16), 
                        name: "{{ auth()->user()->email }}", 
                        displayName: "{{ auth()->user()->name }}"
                    },
                    pubKeyCredParams: [
                        { type: "public-key", alg: -7 },  // ES256
                        { type: "public-key", alg: -257 } // RS256
                    ],
                    timeout: 60000,
                    authenticatorSelection: {
                        residentKey: "preferred",
                        requireResidentKey: false,
                        userVerification: "preferred"
                    },
                    attestation: "none",
                    extensions: { credProps: true }
                }
            });

            // Here you should send the credentials to your server for storage
            await storeCredentialsOnServer(credentials);
            
            alert('Otentikasi biometrik berhasil diaktifkan!');
            return true;
        }

        async function disableBiometric() {
            // Here you should call your server to disable biometric for this user
            await removeCredentialsFromServer();
            alert('Otentikasi biometrik dinonaktifkan');
            return true;
        }

        async function checkBiometricStatus() {
            try {
                // Check with server if biometric is enabled for this user
                const isEnabled = await fetchBiometricStatus();
                document.getElementById('switch_biometric').checked = isEnabled;
            } catch (error) {
                console.error('Error checking biometric status:', error);
            }
        }

        // Placeholder functions - replace with actual server calls
        async function storeCredentialsOnServer(credentials) {
            // Implement this to send credentials to your backend
            console.log('Storing credentials on server:', credentials);
            return Promise.resolve();
        }

        async function removeCredentialsFromServer() {
            // Implement this to remove credentials from your backend
            console.log('Removing credentials from server');
            return Promise.resolve();
        }

        async function fetchBiometricStatus() {
            // Implement this to check biometric status from your backend
            return Promise.resolve(false);
        }
    </script>
    @endsection
</x-app>