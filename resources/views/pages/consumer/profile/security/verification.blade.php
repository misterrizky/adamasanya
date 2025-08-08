<?php
use function Laravel\Folio\name;
name('profile.verification');

$mulaiVerifikasi = function(){
    return $this->redirect(route('onboarding'), navigate: true);
};
?>
<x-app>
    <x-toolbar-mobile 
        :breadcrumbs="[
            ['icon' => 'arrow-left', 'url' => route('profile.security')],
        ]"
    />
    @volt
    <div id="kt_app_content" class="app-content flex-column-fluid">
        <div class="text-center mt-5 px-4">
            <img class="mw-100 mh-300px card-rounded-bottom" alt="" src="{{ asset('media/svg/illustrations/6.svg') }}"/>
            <h1 class="mt-10 fw-bold">Yuk, nikmatin keuntungan ini setelah verifikasi!</h1>
        </div>

        <!-- Fitur Sewa -->
        <div class="card card-xl-stretch mb-5 mb-xl-8">
            <div class="card-header align-items-center border-0 mt-4">
                <h3 class="card-title align-items-start flex-column">
                    <span class="fw-bold text-gray-900">Fitur buat sewa</span>
                </h3>
            </div>
            <div class="card-body pt-3">
                <!-- Sewa Produk -->
                <div class="d-flex align-items-sm-center mb-7">
                    <div class="symbol symbol-60px symbol-2by3 me-4">
                        <div class="symbol-label bg-body theme-light-show" style="background-image: url('{{ asset('media/illustrations/icons/mobile-phone.png') }}')"></div>
                        <div class="symbol-label bg-body theme-dark-show" style="background-image: url('{{ asset('media/illustrations/icons/mobile-phone-dark.png') }}')"></div>
                    </div>
                    <div class="d-flex flex-row-fluid align-items-center flex-wrap my-lg-0 me-2">
                        <div class="flex-grow-1 my-lg-0 my-2 me-2">
                            <a href="#" class="text-gray-800 fw-bold text-hover-primary fs-6">Sewa elektronik dan kebutuhan lainnya</a>
                            <span class="text-muted fw-semibold d-block pt-1">Akses berbagai produk berkualitas untuk disewa dengan harga terjangkau dan proses mudah</span>
                        </div>
                    </div>
                </div>
                
                <!-- Sistem Penyewaan Fleksibel -->
                <div class="d-flex align-items-sm-center mb-7">
                    <div class="symbol symbol-60px symbol-2by3 me-4">
                        <div class="symbol-label bg-body theme-light-show" style="background-image: url('{{ asset('media/illustrations/icons/watch.png') }}')"></div>
                        <div class="symbol-label bg-body theme-dark-show" style="background-image: url('{{ asset('media/illustrations/icons/watch-dark.png') }}')"></div>
                    </div>
                    <div class="d-flex flex-row-fluid align-items-center flex-wrap my-lg-0 me-2">
                        <div class="flex-grow-1 my-lg-0 my-2 me-2">
                            <a href="#" class="text-gray-800 fw-bold text-hover-primary fs-6">Durasi sewa fleksibel</a>
                            <span class="text-muted fw-semibold d-block pt-1">Pilih durasi sewa sesuai kebutuhan Anda, mulai harian hingga bulanan</span>
                        </div>
                    </div>
                </div>
                
                <!-- Pembayaran Mudah -->
                <div class="d-flex align-items-sm-center mb-7">
                    <div class="symbol symbol-60px symbol-2by3 me-4">
                        <div class="symbol-label bg-body theme-light-show" style="background-image: url('{{ asset('media/illustrations/icons/card-2.png') }}')"></div>
                        <div class="symbol-label bg-body theme-dark-show" style="background-image: url('{{ asset('media/illustrations/icons/card-2-dark.png') }}')"></div>
                    </div>
                    <div class="d-flex flex-row-fluid align-items-center flex-wrap my-lg-0 me-2">
                        <div class="flex-grow-1 my-lg-0 my-2 me-2">
                            <a href="#" class="text-gray-800 fw-bold text-hover-primary fs-6">Berbagai metode pembayaran</a>
                            <span class="text-muted fw-semibold d-block pt-1">Bayar dengan transfer bank, e-wallet, atau saldo aplikasi untuk kemudahan transaksi</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Fitur Beli -->
        <div class="card card-xl-stretch mb-5 mb-xl-8">
            <div class="card-header align-items-center border-0 mt-4">
                <h3 class="card-title align-items-start flex-column">
                    <span class="fw-bold text-gray-900">Fitur buat beli</span>
                </h3>
            </div>
            <div class="card-body pt-3">
                <!-- Produk Berkualitas -->
                <div class="d-flex align-items-sm-center mb-7">
                    <div class="symbol symbol-60px symbol-2by3 me-4">
                        <div class="symbol-label bg-body theme-light-show" style="background-image: url('{{ asset('media/illustrations/icons/tick.png') }}')"></div>
                        <div class="symbol-label bg-body theme-dark-show" style="background-image: url('{{ asset('media/illustrations/icons/tick-dark.png') }}')"></div>
                    </div>
                    <div class="d-flex flex-row-fluid align-items-center flex-wrap my-lg-0 me-2">
                        <div class="flex-grow-1 my-lg-0 my-2 me-2">
                            <a href="#" class="text-gray-800 fw-bold text-hover-primary fs-6">Produk original dan berkualitas</a>
                            <span class="text-muted fw-semibold d-block pt-1">Beli produk-produk berkualitas dari brand ternama dengan jaminan keaslian</span>
                        </div>
                    </div>
                </div>
                
                <!-- Pengiriman Cepat -->
                <div class="d-flex align-items-sm-center mb-7">
                    <div class="symbol symbol-60px symbol-2by3 me-4">
                        <div class="symbol-label bg-body theme-light-show" style="background-image: url('{{ asset('media/illustrations/icons/airplane.png') }}')"></div>
                        <div class="symbol-label bg-body theme-dark-show" style="background-image: url('{{ asset('media/illustrations/icons/airplane-dark.png') }}')"></div>
                    </div>
                    <div class="d-flex flex-row-fluid align-items-center flex-wrap my-lg-0 me-2">
                        <div class="flex-grow-1 my-lg-0 my-2 me-2">
                            <a href="#" class="text-gray-800 fw-bold text-hover-primary fs-6">Pengiriman cepat</a>
                            <span class="text-muted fw-semibold d-block pt-1">Pesanan Anda akan diproses cepat dan dikirim ke alamat tujuan</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Keuntungan Lainnya -->
        <div class="card card-xl-stretch mb-5 mb-xl-8">
            <div class="card-header align-items-center border-0 mt-4">
                <h3 class="card-title align-items-start flex-column">
                    <span class="fw-bold text-gray-900">Keuntungan lainnya</span>
                </h3>
            </div>
            <div class="card-body pt-3">
                <!-- Poin dan Hadiah -->
                <div class="d-flex align-items-sm-center mb-7">
                    <div class="symbol symbol-60px symbol-2by3 me-4">
                        <div class="symbol-label bg-body theme-light-show" style="background-image: url('{{ asset('media/illustrations/icons/coin.png') }}')"></div>
                        <div class="symbol-label bg-body theme-dark-show" style="background-image: url('{{ asset('media/illustrations/icons/coin-dark.png') }}')"></div>
                    </div>
                    <div class="d-flex flex-row-fluid align-items-center flex-wrap my-lg-0 me-2">
                        <div class="flex-grow-1 my-lg-0 my-2 me-2">
                            <a href="#" class="text-gray-800 fw-bold text-hover-primary fs-6">Program poin dan hadiah</a>
                            <span class="text-muted fw-semibold d-block pt-1">Dapatkan poin dari setiap transaksi yang bisa ditukar dengan hadiah menarik</span>
                        </div>
                    </div>
                </div>
                
                <!-- Promo Spesial -->
                <div class="d-flex align-items-sm-center mb-7">
                    <div class="symbol symbol-60px symbol-2by3 me-4">
                        <div class="symbol-label bg-body theme-light-show" style="background-image: url('{{ asset('media/illustrations/icons/dollars.png') }}')"></div>
                        <div class="symbol-label bg-body theme-dark-show" style="background-image: url('{{ asset('media/illustrations/icons/dollars-dark.png') }}')"></div>
                    </div>
                    <div class="d-flex flex-row-fluid align-items-center flex-wrap my-lg-0 me-2">
                        <div class="flex-grow-1 my-lg-0 my-2 me-2">
                            <a href="#" class="text-gray-800 fw-bold text-hover-primary fs-6">Promo dan diskon spesial</a>
                            <span class="text-muted fw-semibold d-block pt-1">Nikmati berbagai promo dan diskon eksklusif untuk member terverifikasi</span>
                        </div>
                    </div>
                </div>
                
                <!-- Layanan Pelanggan -->
                <div class="d-flex align-items-sm-center mb-7">
                    <div class="symbol symbol-60px symbol-2by3 me-4">
                        <div class="symbol-label bg-body" style="background-image: url('{{ asset('media/illustrations/icons/24-hours-support.png') }}')"></div>
                    </div>
                    <div class="d-flex flex-row-fluid align-items-center flex-wrap my-lg-0 me-2">
                        <div class="flex-grow-1 my-lg-0 my-2 me-2">
                            <a href="#" class="text-gray-800 fw-bold text-hover-primary fs-6">Layanan pelanggan 24/7</a>
                            <span class="text-muted fw-semibold d-block pt-1">Tim kami siap membantu Anda kapan saja melalui berbagai channel komunikasi</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="text-center mb-15">
            <button type="button" class="btn btn-success btn-lg w-100" data-bs-toggle="offcanvas"
                    data-bs-target="#mulaiVerifikasi" data-kt-drawer-height="500px">Ambil Keuntungannya</button>
        </div>
        <div class="offcanvas offcanvas-bottom" tabindex="-1" id="mulaiVerifikasi">
            <div class="offcanvas-header">
                <h5 class="offcanvas-title text-center">Verifikasi datamu dulu, yuk! Gampang banget, lho~</h5>
            </div>
            <div class="offcanvas-body">
                <div class="d-flex align-items-sm-center mb-7">
                    <div class="symbol symbol-60px symbol-2by3 me-4">
                        <div class="symbol-label bg-body" style="background-image: url('{{ asset('media/illustrations/icons/registration.png') }}')"></div>
                    </div>
                    <div class="d-flex flex-row-fluid align-items-center flex-wrap my-lg-0 me-2">
                        <div class="flex-grow-1 my-lg-0 my-2 me-2">
                            <a href="#" class="text-gray-800 fw-bold text-hover-primary fs-6">Isi data diri</a>
                            <span class="text-muted fw-semibold d-block pt-1">
                                Isi data diri Anda dengan benar dan lengkap, termasuk akun IG, TikTok, Data Alamat, dan Data Keluarga
                            </span>
                        </div>
                    </div>
                </div>
                <div class="d-flex align-items-sm-center mb-7">
                    <div class="symbol symbol-60px symbol-2by3 me-4">
                        <div class="symbol-label bg-body" style="background-image: url('{{ asset('media/illustrations/icons/id-card.png') }}')"></div>
                    </div>
                    <div class="d-flex flex-row-fluid align-items-center flex-wrap my-lg-0 me-2">
                        <div class="flex-grow-1 my-lg-0 my-2 me-2">
                            <a href="#" class="text-gray-800 fw-bold text-hover-primary fs-6">Ambil foto e-KTP</a>
                            <span class="text-muted fw-semibold d-block pt-1">
                                Siapin e-KTP asli kamu dan pastikan masih berlaku, ya.
                            </span>
                        </div>
                    </div>
                </div>
                <div class="d-flex align-items-sm-center mb-7">
                    <div class="symbol symbol-60px symbol-2by3 me-4">
                        <div class="symbol-label bg-body" style="background-image: url('{{ asset('media/illustrations/icons/selfie.png') }}')"></div>
                    </div>
                    <div class="d-flex flex-row-fluid align-items-center flex-wrap my-lg-0 me-2">
                        <div class="flex-grow-1 my-lg-0 my-2 me-2">
                            <a href="#" class="text-gray-800 fw-bold text-hover-primary fs-6">Ambil selfie</a>
                            <span class="text-muted fw-semibold d-block pt-1">
                                Siap-siap! Cari tempat yang terang, lepas kacamata atau masker kamu
                            </span>
                        </div>
                    </div>
                </div>
                <x-form-input-group class="mt-8">
                    <x-form-checkbox name="setuju" id="checkbox_setuju" label="Saya telah membaca dan setuju dengan">
                    @slot('help')
                        <a style="font-weight:bold" href="#" data-bs-toggle="modal" data-bs-target="#ModalSnK">Syarat dan Ketentuan {{ config('app.name') }}</a>
                    @endslot
                    </x-form-checkbox>
                </x-form-input-group>
                <x-button class="btn btn-success btn-block w-100 mt-3" disabled id="tombol_mulai_verifikasi" href="mulaiVerifikasi" type="button" indicator="Harap tunggu..." label="Mulai Verifikasi" />
            </div>
        </div>
        @section('custom_js')
            <script data-navigate-once>
                document.addEventListener('DOMContentLoaded', function() {
                    const checkbox = document.getElementById('checkbox_setuju');
                    const tombolMulai = document.getElementById('tombol_mulai_verifikasi');

                    // Atur status awal tombol berdasarkan status checkbox
                    tombolMulai.disabled = !checkbox.checked;

                    // Tambahkan event listener untuk perubahan checkbox
                    checkbox.addEventListener('change', function() {
                        tombolMulai.disabled = !this.checked;
                    });
                });
                document.addEventListener('livewire:navigated', function() {
                    const checkbox = document.getElementById('checkbox_setuju');
                    const tombolMulai = document.getElementById('tombol_mulai_verifikasi');

                    // Atur status awal tombol berdasarkan status checkbox
                    tombolMulai.disabled = !checkbox.checked;

                    // Tambahkan event listener untuk perubahan checkbox
                    checkbox.addEventListener('change', function() {
                        tombolMulai.disabled = !this.checked;
                    });
                });
            </script>
        @endsection
        <livewire:modal.toc/>
    </div>
    @endvolt
</x-app>