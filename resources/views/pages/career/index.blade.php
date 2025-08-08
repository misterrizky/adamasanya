<?php
use App\Models\Career\Vacancy; // [++]
use function Livewire\Volt\{state};
use function Laravel\Folio\{middleware, name};
name('career');

state([
    'vacancies' => fn() => Vacancy::with(['branch']) // [++]
        ->where('status', 'published') // [++]
        ->where('closed_at', '>=', now()) // [++]
        ->orderBy('posted_at', 'desc') // [++]
        ->get()
]);
?>
<x-app>
    <x-toolbar-mobile 
        :breadcrumbs="[
            ['icon' => 'arrow-left', 'url' => route('profile.setting')],
            ['text' => 'Karir', 'active' => true]
        ]"
    />
    <x-toolbar 
        title="Karir"
        :breadcrumbs="[
            ['icon' => 'home', 'url' => route('home')],
            ['text' => 'Explore More', 'active' => true],
            ['text' => 'Karir', 'active' => true]
        ]"
        toolbar-class="py-3 py-lg-6"
    />
    @volt
    <div class="d-flex flex-column flex-column-fluid">
        <div id="kt_app_content" class="app-content flex-column-fluid">
            <!--begin::Careers - List-->
            <div class="card">
                <!--begin::Body-->
                <div class="card-body p-lg-17">
                    <!--begin::Hero-->
                    <div class="position-relative mb-17">
                        <!--begin::Overlay-->
                        <div class="overlay overlay-show">
                            <!--begin::Image-->
                            <div class="bgi-no-repeat bgi-position-center bgi-size-cover card-rounded min-h-250px" style="background-image:url('{{ asset('media/stock/1600x800/img-1.jpg') }}')"></div>
                            <!--end::Image-->
                            <!--begin::layer-->
                            <div class="overlay-layer rounded bg-black" style="opacity: 0.4"></div>
                            <!--end::layer-->
                        </div>
                        <!--end::Overlay-->
                        <!--begin::Heading-->
                        <div class="position-absolute text-white mb-8 ms-10 bottom-0">
                            <!--begin::Title-->
                            <h3 class="text-white fs-2qx fw-bold mb-3 m">Karir di Adamasanya</h3>
                            <!--end::Title-->
                            <!--begin::Text-->
                            <div class="fs-5 fw-semibold">Bergabunglah dengan tim kami yang dinamis dan penuh semangat</div>
                            <!--end::Text-->
                        </div>
                        <!--end::Heading-->
                    </div>
                    <!--end::Hero-->
                    
                    <!--begin::Layout-->
                    <div class="d-flex flex-column flex-lg-row mb-17">
                        <!--begin::Content-->
                        <div class="flex-lg-row-fluid me-0 me-lg-20">
                            @if($vacancies->isEmpty()) <!-- [++] -->
                                <div class="alert alert-info">
                                    <i class="ki-outline ki-information fs-3 me-2"></i>
                                    Saat ini tidak ada lowongan yang tersedia. Silakan cek kembali nanti.
                                </div>
                            @else
                                @foreach($vacancies as $vacancy) <!-- [++] -->
                                <div class="mb-17">
                                    <!--begin::Description-->
                                    <div class="m-0">
                                        <h4 class="fs-1 text-gray-800 w-bolder mb-6">
                                            {{ $vacancy->position }} <!-- [++] -->
                                        </h4>
                                        <p class="fw-semibold fs-4 text-gray-600 mb-2">
                                            {{ $vacancy->description }} <!-- [++] -->
                                        </p>
                                    </div>
                                    
                                    <!--begin::Accordion-->
                                    @if($vacancy->requirements) <!-- [++] -->
                                    <div class="m-0">
                                        <div class="d-flex align-items-center collapsible py-3 toggle mb-0" 
                                             data-bs-toggle="collapse" 
                                             data-bs-target="#{{ $vacancy->slug }}-requirements">
                                            <div class="btn btn-sm btn-icon mw-20px btn-active-color-primary me-5">
                                                <i class="ki-outline ki-minus-square toggle-on text-primary fs-1"></i>
                                                <i class="ki-outline ki-plus-square toggle-off fs-1"></i>
                                            </div>
                                            <h4 class="text-gray-700 fw-bold cursor-pointer mb-0">Persyaratan</h4>
                                        </div>
                                        <div id="{{ $vacancy->slug }}-requirements" class="collapse show fs-6 ms-1">
                                            @foreach(explode("\n", $vacancy->requirements) as $requirement) <!-- [++] -->
                                            @if(trim($requirement))
                                            <div class="mb-4">
                                                <div class="d-flex align-items-center ps-10 mb-n1">
                                                    <span class="bullet me-3"></span>
                                                    <div class="text-gray-600 fw-semibold fs-6">
                                                        {{ trim($requirement) }} <!-- [++] -->
                                                    </div>
                                                </div>
                                            </div>
                                            @endif
                                            @endforeach
                                        </div>
                                        <div class="separator separator-dashed"></div>
                                    </div>
                                    @endif
                                    
                                    @if($vacancy->responsibilities) <!-- [++] -->
                                    <div class="m-0">
                                        <div class="d-flex align-items-center collapsible py-3 toggle collapsed mb-0" 
                                             data-bs-toggle="collapse" 
                                             data-bs-target="#{{ $vacancy->slug }}-role">
                                            <div class="btn btn-sm btn-icon mw-20px btn-active-color-primary me-5">
                                                <i class="ki-outline ki-minus-square toggle-on text-primary fs-1"></i>
                                                <i class="ki-outline ki-plus-square toggle-off fs-1"></i>
                                            </div>
                                            <h4 class="text-gray-700 fw-bold cursor-pointer mb-0">Tanggung Jawab Pekerjaan</h4>
                                        </div>
                                        <div id="{{ $vacancy->slug }}-role" class="collapse fs-6 ms-1">
                                            @foreach(explode("\n", $vacancy->responsibilities) as $responsibility) <!-- [++] -->
                                            @if(trim($responsibility))
                                            <div class="mb-4">
                                                <div class="d-flex align-items-center ps-10 mb-n1">
                                                    <span class="bullet me-3"></span>
                                                    <div class="text-gray-600 fw-semibold fs-6">
                                                        {{ trim($responsibility) }} <!-- [++] -->
                                                    </div>
                                                </div>
                                            </div>
                                            @endif
                                            @endforeach
                                        </div>
                                        <div class="separator separator-dashed"></div>
                                    </div>
                                    @endif
                                    
                                    <!-- Hapus bagian benefits dan terms karena tidak ada di model -->
                                    
                                    <button class="btn btn-sm btn-primary mt-5" 
                                            data-career-id="{{ $vacancy->id }}" <!-- [++] -->
                                            data-career-title="{{ $vacancy->position }}" <!-- [++] -->
                                            data-bs-toggle="modal" 
                                            data-bs-target="#applyJobModal">
                                        <i class="ki-outline ki-briefcase fs-2 me-2"></i> Lamar Sekarang
                                    </button>
                                </div>
                                @endforeach
                            @endif
                        </div>
                        <!--end::Content-->
                        
                        <!--begin::Sidebar-->
                        <div class="flex-lg-row-auto w-100 w-lg-275px w-xxl-350px">
                            <!--begin::Careers about-->
                            <div class="card bg-light">
                                <!--begin::Body-->
                                <div class="card-body">
                                    <!--begin::Top-->
                                    <div class="mb-7">
                                        <!--begin::Title-->
                                        <h2 class="fs-1 text-gray-800 w-bolder mb-6">Tentang Kami</h2>
                                        <!--end::Title-->
                                        <!--begin::Text-->
                                        <p class="fw-semibold fs-6 text-gray-600 mb-4">
                                            Adamasanya adalah perusahaan yang berkomitmen untuk menciptakan lingkungan kerja yang inklusif dan mendukung pertumbuhan karyawan.
                                        </p>
                                        <!--end::Text-->
                                        <!--begin::Features-->
                                        <div class="mb-0">
                                            <div class="d-flex align-items-center mb-4">
                                                <span class="bullet bg-primary me-3"></span>
                                                <div class="text-gray-700 fw-semibold">Kultur kerja yang kolaboratif</div>
                                            </div>
                                            <div class="d-flex align-items-center mb-4">
                                                <span class="bullet bg-primary me-3"></span>
                                                <div class="text-gray-700 fw-semibold">Program pengembangan karir</div>
                                            </div>
                                            <div class="d-flex align-items-center">
                                                <span class="bullet bg-primary me-3"></span>
                                                <div class="text-gray-700 fw-semibold">Fasilitas dan benefit kompetitif</div>
                                            </div>
                                        </div>
                                        <!--end::Features-->
                                    </div>
                                    <!--end::Top-->
                                </div>
                                <!--end::Body-->
                            </div>
                            <!--end::Careers about-->
                        </div>
                        <!--end::Sidebar-->
                    </div>
                    <!--end::Layout-->
                </div>
                <!--end::Body-->
            </div>
            <!--end::Careers - List-->
        </div>
        <!--end::Content-->
    </div>
    @endvolt

    <!-- Apply Job Modal -->
    <div class="modal fade" id="applyJobModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered mw-650px">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="fw-bold">Apply for <span id="modalJobTitle"></span></h2>
                    <div class="btn btn-icon btn-sm btn-active-icon-primary" data-bs-dismiss="modal">
                        <i class="ki-outline ki-cross fs-1"></i>
                    </div>
                </div>
                
                <div class="modal-body py-10 px-lg-17">
                    <form id="jobApplicationForm" action="" method="POST" enctype="multipart/form-data"> <!-- [++] -->
                        @csrf
                        <input type="hidden" name="vacancy_id" id="careerId"> <!-- [++] -->
                        
                        <!-- ... (form fields tetap sama) ... -->
                        
                        <div class="d-flex justify-content-end">
                            <button type="reset" class="btn btn-light me-3" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary">
                                <span class="indicator-label">Submit Application</span>
                                <span class="indicator-progress">Please wait...
                                    <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                                </span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app>
@section('custom_js')
<script data-navigate-once>
    document.addEventListener('DOMContentLoaded', function() {
        const applyButtons = document.querySelectorAll('[data-career-id]');
        const modal = new bootstrap.Modal(document.getElementById('applyJobModal'));
        
        applyButtons.forEach(button => {
            button.addEventListener('click', function() {
                const vacancyId = this.getAttribute('data-career-id'); // [++]
                const position = this.getAttribute('data-career-title'); // [++]
                
                document.getElementById('modalJobTitle').textContent = position;
                document.getElementById('careerId').value = vacancyId;
                
                modal.show();
            });
        });
        
        const form = document.getElementById('jobApplicationForm');
        const submitButton = form.querySelector('[type="submit"]');
        
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const indicator = submitButton.querySelector('.indicator-label');
            const indicatorProgress = submitButton.querySelector('.indicator-progress');
            
            indicator.style.display = 'none';
            indicatorProgress.style.display = 'inline-block';
            
            // Submit form
            this.submit();
        });
    });
</script>
@endsection