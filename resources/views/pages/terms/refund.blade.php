<?php
use function Livewire\Volt\{computed, state};
use function Laravel\Folio\{middleware, name};

name('term-refund');

state(['search'])->url();

$collection = computed(function() {
    return [
        [
            'title' => '1. Pembatalan Sewa',
            'content' => "Diluar H-1 atau hari H: Pengembalian dana sebesar 75% dari total pembayaran yang telah dilakukan.
            H-1: Pengembalian dana sebesar 50% dari total pembayaran yang telah dilakukan.
            Hari H (hari pengambilan): Tidak ada pengembalian dana.

            Pembatalan harus diajukan melalui kontak resmi dan hanya diproses pada jam operasional."
        ],
        [
            'title' => '2. Pembayaran Gagal / Kesalahan Transaksi',
            'content' => "Jika terjadi kesalahan sistem atau transaksi ganda, pengguna wajib mengirimkan bukti transaksi ke layanan pelanggan kami dalam 1x24 jam untuk validasi dan pemrosesan pengembalian dana."
        ],
        [
            'title' => '3. Ketentuan Pengembalian',
            'content' => "Pengembalian dana akan diproses dalam waktu maksimal 7 hari kerja ke rekening bank yang digunakan saat transaksi. Kami tidak melayani pengembalian tunai."
        ],
        [
            'title' => '4. Situasi Tidak Mendapat Refund',
            'content' => "Pembatalan di hari pengambilan (Hari H)
            Unit sudah diterima dan digunakan sebagian
            Pelanggaran terhadap syarat dan ketentuan penyewaan"
        ],
        [
            'title' => '5. Pengembalian Produk yang Dibeli',
            'content' => "Produk yang dibeli melalui platform tidak dapat dikembalikan kecuali terdapat cacat produksi dan dilaporkan maksimal 2x24 jam setelah diterima. Verifikasi akan dilakukan oleh tim kami sebelum penggantian."
        ],
        [
            'title' => '6. Hubungi Kami',
            'content' => "Untuk pertanyaan terkait kebijakan refund, silakan hubungi kami melalui:
            Email: rizal@adamasanya.com
            WhatsApp: +62 877-6534-6368"
        ],
    ];
});
?>
<x-app>
    @volt
    <div class="d-flex flex-column flex-column-fluid">
        <!--begin::Toolbar-->
        <x-toolbar 
            title="Syarat & Ketentuan"
            :breadcrumbs="[
                ['icon' => 'home', 'url' => route('home')],
                ['text' => 'Syarat & Ketentuan', 'active' => true]
            ]"
            toolbar-class="py-3 py-lg-6"
        />
        <!--end::Toolbar-->
        <!--begin::Content-->
        <div id="kt_app_content" class="app-content flex-column-fluid">
            <div class="card">
                <!--begin::Body-->
                <div class="card-body p-lg-15">
                    <!--begin::Layout-->
                    <div class="d-flex flex-column flex-lg-row">
                        <!--begin::Sidebar-->
                        <div class="flex-column flex-lg-row-auto w-100 w-lg-275px mb-10 me-lg-20 d-none d-md-block">
                            <ul class="nav nav-tabs nav-pills flex-row border-0 flex-md-column me-5 mb-3 mb-md-0 fs-6 min-w-lg-200px">
                                @foreach($this->collection as $index => $section)
                                <li class="nav-item w-100 me-0 mb-md-2">
                                    <a class="nav-link w-100 {{ $index === 0 ? 'active' : '' }} btn btn-flex btn-active-light-success text-start" 
                                    data-bs-toggle="tab" 
                                    href="#terms_{{ $index }}">
                                        <span class="d-flex flex-column align-items-start">
                                            <span class="fs-4 fw-bold text-start">{{ $section['title'] }}</span>
                                        </span>
                                    </a>
                                </li>
                                @endforeach
                            </ul>
                        </div>
                        <!--end::Sidebar-->
                        <!--begin::Content-->
                        <div class="flex-lg-row-fluid">
                            <!--begin::Extended content-->
                            <div class="mb-13">
                                <!--begin::Content-->
                                <div class="mb-15">
                                    <!--begin::Title-->
                                    <h4 class="fs-2x text-gray-800 w-bolder mb-6">Kebijakan Pengembalian Dana (Refund Policy) – Adamasanya</h4>
                                    <!--end::Title-->
                                    <!--begin::Text-->
                                    <p class="fw-semibold fs-4 text-gray-600 mb-2">Tanggal Berlaku: <strong>1 Mei 2025</strong></p>
                                    <p class="fw-semibold fs-4 text-gray-600 mb-2">
                                        Selamat datang di Adamasanya. Dengan mengakses, mendaftar, atau menggunakan platform kami baik melalui situs web maupun aplikasi, Anda menyetujui untuk terikat oleh syarat dan ketentuan ini. Jika Anda tidak menyetujui salah satu bagian dari dokumen ini, Anda tidak diperbolehkan untuk menggunakan layanan kami.
                                    </p>
                                    <!--end::Text-->
                                </div>
                                <!--end::Content-->
                                <!--begin::Item-->
                                <div class="mb-15 d-none d-md-block">
                                    <div class="tab-content" id="termsContent">
                                        @foreach($this->collection as $index => $section)
                                        <div class="tab-pane fade {{ $index === 0 ? 'show active' : '' }}" id="terms_{{ $index }}" role="tabpanel">
                                            <h3 class="text-gray-800 w-bolder mb-4">{{ $section['title'] }}</h3>
                                            <div class="mb-4 text-gray-600 fw-semibold fs-6 ps-10">
                                                {!! nl2br(e($section['content'])) !!}
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                                <!--begin::Accordion-->
                                <div class="accordion accordion-icon-collapse d-block d-md-none" id="termsAccordion">
                                    @foreach($this->collection as $index => $section)
                                    <!--begin::Item-->
                                    <div class="mb-5">
                                        <!--begin::Header-->
                                        <div class="accordion-header py-3 d-flex align-items-center" data-bs-toggle="collapse" data-bs-target="#accordion{{$index}}">
                                            <span class="accordion-icon">
                                                <i class="ki-outline ki-plus-square fs-3 accordion-icon-off"></i>
                                                <i class="ki-outline ki-minus-square fs-3 accordion-icon-on"></i>
                                            </span>
                                            <h3 class="fs-4 fw-semibold mb-0 ms-4 text-gray-800">{{ $section['title'] }}</h3>
                                        </div>
                                        <!--end::Header-->

                                        <!--begin::Body-->
                                        <div id="accordion{{$index}}" class="fs-6 collapse ps-10" data-bs-parent="#termsAccordion">
                                            <div class="accordion-body py-4 ps-6 pe-0">
                                                {!! nl2br(e($section['content'])) !!}
                                            </div>
                                        </div>
                                        <!--end::Body-->
                                    </div>
                                    <!--end::Item-->
                                    @endforeach
                                </div>
                                <!--end::Accordion-->
                                <!--end::Item-->
                            </div>
                            <!--end::Extended content-->
                        </div>
                        <!--end::Content-->
                    </div>
                    <!--end::Layout-->
                </div>
                <!--end::Body-->
            </div>
        </div>
        <!--end::Content-->
    </div>
    @endvolt
</x-app>