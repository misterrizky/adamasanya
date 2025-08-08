@props([
    'advance' => null, // 
    'bulan' => null, // 
    'tahun' => null, // 
    'dataTahun' => [], // 
    'status' => null, // 
    'dataStatus' => [], // 
])
<!--begin::Card-->
<div class="card mb-7">
    <!--begin::Card body-->
    <div class="card-body">
        <!--begin::Compact form-->
        <div class="d-flex align-items-center">
            <!--begin::Input group-->
            <div class="position-relative w-md-400px me-md-5">
                <i class="ki-outline ki-magnifier fs-3 text-gray-500 position-absolute top-50 translate-middle ms-6"></i>
                <x-form-input name="search" modifier="live" class="form-control-solid ps-10" placeholder="Cari Data" />
            </div>
            <!--end::Input group-->
            @if($advance)
            <!--begin:Action-->
                <div class="d-flex align-items-center">
                    <x-button tag="a" class="btn btn-link" data-bs-toggle="collapse" data-bs-target="#advanced_form" id="tombol_advanced_form" label="Pencarian Lanjutan" />
                </div>
            <!--end:Action-->
            @endif
        </div>
        <!--end::Compact form-->
        <!--begin::Advance form-->
        <div class="collapse" id="advanced_form">
            <!--begin::Separator-->
            <div class="separator separator-dashed mt-9 mb-6"></div>
            <!--end::Separator-->
            <!--begin::Row-->
            <div class="row g-8 mb-8">
                <!--begin::Col-->
                @if($bulan)
                <div class="col-12 col-md-4">
                    <label class="fs-6 fw-semibold form-label mb-2" for="bulan">Pilih Bulan</label>
                    @php
                    $dataBulan = [
                        '' => 'Pilih Bulan',
                        'Januari' => 'Januari',
                        'Februari' => 'Februari',
                        'Maret' => 'Maret',
                        'April' => 'April',
                        'Mei' => 'Mei',
                        'Juni' => 'Juni',
                        'Juli' => 'Juli',
                        'Agustus' => 'Agustus',
                        'September' => 'September',
                        'Oktober' => 'Oktober',
                        'November' => 'November',
                        'desember' => 'Desember'
                    ];
                    @endphp
                    <x-form-select 
                        name="bulan" 
                        modifier="live"
                        class="form-select form-select-solid fw-bold"
                        :options="$dataBulan"
                    />
                </div>
                @endif
                @if($tahun)
                <div class="col-12 col-md-4">
                    <label class="fs-6 fw-semibold form-label mb-2" for="tahun">Pilih Tahun</label>
                    <x-form-select 
                        name="tahun" 
                        modifier="live"
                        class="form-select form-select-solid fw-bold"
                        :options="$dataTahun"
                    />
                </div>
                @endif
                @if($status)
                <div class="col-12 col-md-4">
                    <label class="fs-6 fw-semibold form-label mb-2" for="status">Pilih Status</label>
                    <x-form-select 
                        name="status" 
                        modifier="live"
                        class="form-select form-select-solid fw-bold"
                        :options="$dataStatus"
                    />
                </div>
                @endif
                <!--end::Col-->
            </div>
            <!--end::Row-->
        </div>
        <!--end::Advance form-->
    </div>
    <!--end::Card body-->
</div>