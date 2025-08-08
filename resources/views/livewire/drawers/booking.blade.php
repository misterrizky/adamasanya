<?php
use function Livewire\Volt\{computed, state};

state(['rentId']);
$rent = computed(function() {
    return \App\Models\ProductRent::with(['product'])->find($this->rentId);
});
?>
<div>
    @if($this->rent)
    <div id="booking-drawer" 
        class="bg-white"
        data-kt-drawer="true"
        data-kt-drawer-activate="true"
        data-kt-drawer-overlay="true"
        data-kt-drawer-close="#tombol_close_booking"
        data-kt-drawer-width="{default:'300px', 'md': '500px'}"
        data-kt-drawer-direction="start">
        
        <!-- Konten drawer menggunakan $this->rent -->
        <div class="card-header">
            <h3 class="card-title">Booking {{ $this->rent->product->name }}</h3>
            <div class="card-toolbar">
                <button id="tombol_close_booking" class="btn btn-sm btn-icon btn-active-light-primary">
                    <i class="ki-outline ki-cross fs-2"></i>
                </button>
            </div>
        </div>
        <!--begin::Card body-->
        <div class="card-body hover-scroll-overlay-y h-400px pt-5">
            <!--begin::Item-->
            <div class="d-flex flex-stack">
                <!--begin::Wrapper-->
                <div class="d-flex flex-column me-3">
                    <!--begin::Section-->
                    <div class="mb-3">
                        <a href="apps/ecommerce/sales/details.html" class="text-gray-800 text-hover-primary fs-4 fw-bold">Iblender</a>
                        <span class="text-gray-500 fw-semibold d-block">The best kitchen gadget in 2022</span>
                    </div>
                    <!--end::Section-->
                    <!--begin::Section-->
                    <div class="d-flex align-items-center">
                        <span class="fw-bold text-gray-800 fs-5">$ 350</span>
                        <span class="text-muted mx-2">for</span>
                        <span class="fw-bold text-gray-800 fs-5 me-3">5</span>
                        <a href="#" class="btn btn-sm btn-light-success btn-icon-success btn-icon w-25px h-25px me-2">
                            <i class="ki-outline ki-minus fs-4"></i>
                        </a>
                        <a href="#" class="btn btn-sm btn-light-success btn-icon w-25px h-25px">
                            <i class="ki-outline ki-plus fs-4"></i>
                        </a>
                    </div>
                    <!--end::Wrapper-->
                </div>
                <!--end::Wrapper-->
                <!--begin::Pic-->
                <div class="symbol symbol-70px symbol-2by3 flex-shrink-0">
                    <img src="{{asset('media/stock/600x400/img-1.jpg')}}" alt="" />
                </div>
                <!--end::Pic-->
            </div>
            <!--end::Item-->
            <!--begin::Separator-->
            <div class="separator separator-dashed my-6"></div>
            <!--end::Separator-->
        </div>
        <!--end::Card body-->
        <!--begin::Card footer-->
        <div class="card-footer">
            <!--begin::Item-->
            <div class="d-flex flex-stack">
                <span class="fw-bold text-gray-600">Total</span>
                <span class="text-gray-800 fw-bolder fs-5">Rp 1840.00</span>
            </div>
            <!--end::Item-->
            <!--begin::Item-->
            <div class="d-flex flex-stack">
                <span class="fw-bold text-gray-600">Sub total</span>
                <span class="text-primary fw-bolder fs-5">Rp 246.35</span>
            </div>
            <!--end::Item-->
            <!--end::Action-->
            <div class="d-flex justify-content-end mt-9">
                <a href="#" class="btn btn-primary d-flex justify-content-end">Pesan Sekarang</a>
            </div>
            <!--end::Action-->
        </div>
        <!--end::Card footer-->
    </div>

    @push('scripts')
    <script>
    document.addEventListener('livewire:init', function() {
        Livewire.on('show-booking-drawer', () => {
            const drawer = KTDrawer.getInstance(document.getElementById('booking-drawer'));
            if (drawer) {
                drawer.show();
            }
        });
    });
    </script>
    @endpush
    @endif
</div>