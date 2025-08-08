<?php
use function Laravel\Folio\name;
name('admin.profile.push-notification');
?>

<x-app>
    <x-toolbar-mobile 
        :breadcrumbs="[
            ['icon' => 'arrow-left', 'url' => route('admin.profile.notification')],
            ['text' => 'Push Notification', 'active' => true]
        ]"
    />
    
    @volt
    <div id="kt_app_content" class="app-content flex-column-fluid">
        <div class="card card-xl-stretch mb-5 mb-xl-8">
            <div class="card-header align-items-center border-0 mt-4">
                <h3 class="card-title align-items-start flex-column">
                    <span class="fw-bold text-gray-900">Cek push notification di HP-mu</span>
                </h3>
            </div>
            <div class="card-body pt-3">
                <div class="d-flex align-items-center mb-7">
                    <div class="d-flex flex-row-fluid align-items-center">
                        <div class="w-100">
                            <span class="text-gray-800 fs-6">Lacak masalah seputar push notification di sini.</span>
                        </div>
                        <div class="w-25 text-end">
                            <a href="{{ route('profile.push-notification-check') }}" wire:navigate class="btn btn-icon btn-light btn-sm border-0">
                                <i class="ki-outline ki-arrow-right fs-2 text-primary"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card shadow-sm mb-5">
            <div class="card-header">
                <h3 class="card-title">Aktivitas</h3>
            </div>
            <div class="card card-dashed hover-elevate-up parent-hover">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center">
                        <div class="ms-3">
                            <div class="text-gray-700 fs-4 fw-bold">
                                Chat
                            </div>
                        </div>
                    </div>
                    <div class="form-check form-switch form-check-custom form-check-warning form-check-solid">
                        <input 
                            class="form-check-input h-30px w-50px" 
                            type="checkbox" 
                            id="chat_promosi"
                        />
                    </div>
                </div>
            </div>
        </div>
        <div class="card shadow-sm mb-5">
            <div class="card-header">
                <h3 class="card-title">Promo</h3>
            </div>
            <div class="card card-dashed hover-elevate-up parent-hover">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center">
                        <div class="ms-3">
                            <div class="text-gray-700 fs-4 fw-bold">
                                Rekomendasi Untukmu
                            </div>
                            <div class="text-gray-500 fs-5 mt-1">
                                Dapatkan info promosi terkini di {{ config('app.name') }}
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-check form-switch form-check-custom form-check-warning form-check-solid">
                        <input 
                            class="form-check-input h-30px w-50px" 
                            type="checkbox" 
                            id="recommendation_notification"  
                        />
                    </div>
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