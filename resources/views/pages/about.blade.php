<?php
use App\Models\User;
use App\Models\Master\Brand;
use App\Models\Master\Branch;
use App\Models\ProductBranch;
use function Livewire\Volt\{state};
use function Laravel\Folio\{middleware, name};
name('about');

state([
    'branches' => fn() => Branch::where('st', 'a')->get(),
    'brands' => fn() => Brand::where('st', 'a')->get(),
    'konsumen' => fn() => User::role('Konsumen')->count(),
    'produk' => fn() => ProductBranch::count(),
]);
?>

<x-app>
    <x-toolbar-mobile 
        :breadcrumbs="[
            ['icon' => 'arrow-left', 'url' => route('profile.setting')],
            ['text' => 'Tentang Kami', 'active' => true]
        ]"
    />
    <x-toolbar 
        title="Tentang Kami"
        :breadcrumbs="[
            ['icon' => 'ki-outline ki-home', 'url' => route('home')],
            ['text' => 'Tentang Kami', 'active' => true]
        ]"
        toolbar-class="py-3 py-lg-6"
    />
    @volt
    <div class="d-flex flex-column flex-column-fluid">
        <!-- Toolbar dengan Breadcrumb -->

        <!-- Konten Utama -->
        <div id="kt_app_content" class="app-content flex-column-fluid">
            <div class="card">
                <div class="card-body p-lg-17">
                    <!-- Section Tentang Perusahaan -->
                    <div class="mb-18">
                        <div class="mb-10">
                            <!-- Header Section -->
                            <div class="text-center mb-15">
                                <h3 class="fs-2hx text-gray-900 mb-5">Tentang Perusahaan Kami</h3>
                                <div class="fs-5 text-muted fw-semibold">
                                    Menyediakan solusi lengkap untuk kebutuhan penyewaan dan pembelian peralatan sejak 2017
                                </div>
                            </div>

                            <!-- Gambar Utama -->
                            <div class="overlay mb-10">
                                <img class="w-100 card-rounded" src="{{ asset('media/stock/1600x800/img-1.jpg') }}" alt="Tentang Perusahaan Kami" />
                            </div>
                        </div>

                        <!-- Deskripsi Perusahaan -->
                        <div class="fs-5 fw-semibold text-gray-600">
                            <p class="mb-8">
                                <span class="text-gray-800 fw-bold">PT. Adamasanya Solution Indonesia</span> adalah perusahaan terkemuka dalam penyediaan berbagai peralatan untuk kebutuhan konstruksi, industri, dan rumah tangga. Didirikan pada tahun 2015, kami telah melayani lebih dari 10.000 pelanggan dengan komitmen untuk menyediakan peralatan berkualitas tinggi dengan harga yang kompetitif.
                            </p>
                            
                            <p class="mb-8">
                                Kami memahami bahwa setiap proyek memiliki kebutuhan yang unik. Itulah mengapa kami menawarkan fleksibilitas baik dalam penyewaan harian/mingguan/bulanan maupun opsi pembelian untuk peralatan yang sering digunakan. Dengan stok lebih dari 5.000 unit peralatan dari berbagai merek ternama, kami siap mendukung kesuksesan proyek Anda.
                            </p>
                            
                            <p class="mb-17">
                                Keunggulan kami terletak pada pelayanan yang cepat, peralatan yang terawat baik, dan tim profesional yang siap memberikan solusi terbaik untuk kebutuhan Anda. Setiap peralatan yang kami sewakan melalui pemeriksaan rutin untuk memastikan keandalan dan keamanan penggunaannya.
                            </p>
                        </div>
                    </div>

                    <!-- Statistik Perusahaan -->
                    <div class="card bg-light mb-18">
                        <div class="card-body py-15">
                            <div class="d-flex flex-center">
                                <div class="d-flex flex-center flex-wrap mb-10 mx-auto gap-5 w-xl-900px">
                                    <!-- Statistik 1 -->
                                    <div class="octagon d-flex flex-center h-200px w-200px bg-body mx-lg-10">
                                        <div class="text-center">
                                            <i class="ki-outline ki-element-11 fs-2tx text-primary"></i>
                                            <div class="mt-1">
                                                <div class="fs-lg-2hx fs-2x fw-bold text-gray-800 d-flex align-items-center">
                                                    <div class="min-w-70px" data-kt-countup="true" data-kt-countup-value="{{ $this->konsumen }}">0</div>+
                                                </div>
                                                <span class="text-gray-600 fw-semibold fs-5 lh-0">Konsumen Puas</span>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Statistik 2 -->
                                    <div class="octagon d-flex flex-center h-200px w-200px bg-body mx-lg-10">
                                        <div class="text-center">
                                            <i class="ki-outline ki-chart-pie-4 fs-2tx text-success"></i>
                                            <div class="mt-1">
                                                <div class="fs-lg-2hx fs-2x fw-bold text-gray-800 d-flex align-items-center">
                                                    <div class="min-w-50px" data-kt-countup="true" data-kt-countup-value="{{ $this->produk }}">0</div>+
                                                </div>
                                                <span class="text-gray-600 fw-semibold fs-5 lh-0">Peralatan Tersedia</span>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Statistik 3 -->
                                    <div class="octagon d-flex flex-center h-200px w-200px bg-body mx-lg-10">
                                        <div class="text-center">
                                            <i class="ki-outline ki-basket fs-2tx text-info"></i>
                                            <div class="mt-1">
                                                <div class="fs-lg-2hx fs-2x fw-bold text-gray-800 d-flex align-items-center">
                                                    <div class="min-w-50px" data-kt-countup="true" data-kt-countup-value="{{ $this->branches->count() }}">0</div>+
                                                </div>
                                                <span class="text-gray-600 fw-semibold fs-5 lh-0">Cabang</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Testimoni -->
                            <div class="fs-2 fw-semibold text-muted text-center mb-3">
                                <span class="fs-1 lh-1 text-gray-700">"</span>Kualitas peralatan dan pelayanan yang luar biasa. Tidak pernah mengecewakan!<span class="fs-1 lh-1 text-gray-700">"</span>
                            </div>
                            <div class="fs-2 fw-semibold text-muted text-center">
                                <span class="fs-4 fw-bold text-gray-600">- Direktur PT. Konstruksi Maju Jaya</span>
                            </div>
                        </div>
                    </div>

                    <!-- Visi Misi -->
                    <div class="mb-16">
                        <div class="text-center mb-12">
                            <h3 class="fs-2hx text-gray-900 mb-5">Visi & Misi Perusahaan</h3>
                            <div class="fs-5 text-muted fw-semibold">
                                Komitmen kami untuk memberikan solusi terbaik bagi pelanggan
                            </div>
                        </div>
                        
                        <div class="row g-10">
                            <!-- Visi -->
                            <div class="col-md-6">
                                <div class="card card-flush h-100">
                                    <div class="card-body text-center p-10">
                                        <i class="ki-outline ki-rocket fs-2tx text-primary mb-5"></i>
                                        <h4 class="text-gray-900 fw-bold mb-5">Visi</h4>
                                        <p class="text-gray-600">
                                            Menjadi penyedia jasa penyewaan dan penjualan peralatan terdepan di Indonesia dengan jaringan nasional dan standar pelayanan kelas dunia.
                                        </p>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Misi -->
                            <div class="col-md-6">
                                <div class="card card-flush h-100">
                                    <div class="card-body p-10">
                                        <i class="ki-outline ki-target fs-2tx text-success mb-5 d-block text-center"></i>
                                        <h4 class="text-gray-900 fw-bold mb-5 text-center">Misi</h4>
                                        <ul class="text-gray-600">
                                            <li class="mb-3">Menyediakan peralatan berkualitas tinggi dengan harga kompetitif</li>
                                            <li class="mb-3">Memberikan pelayanan cepat dan profesional</li>
                                            <li class="mb-3">Memastikan keamanan dan keandalan setiap peralatan</li>
                                            <li class="mb-3">Mengembangkan jaringan cabang untuk jangkauan lebih luas</li>
                                            <li>Berkontribusi pada pembangunan infrastruktur Indonesia</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Cabang Kami -->
                    <div class="mb-18">
                        <div class="text-center mb-12">
                            <h3 class="fs-2hx text-gray-900 mb-5">Cabang Kami</h3>
                            <div class="fs-5 text-muted fw-semibold">
                                Kami hadir di berbagai lokasi untuk melayani Anda dengan lebih dekat
                            </div>
                        </div>
                        
                        <!-- Slider Cabang -->
                        <div class="tns tns-default mb-10">
                            <div data-tns="true" data-tns-loop="true" data-tns-swipe-angle="false" data-tns-speed="2000" 
                                 data-tns-autoplay="true" data-tns-autoplay-timeout="18000" data-tns-controls="true" 
                                 data-tns-nav="false" data-tns-items="1" data-tns-center="false" data-tns-dots="false" 
                                 data-tns-prev-button="#kt_team_slider_prev" data-tns-next-button="#kt_team_slider_next" 
                                 data-tns-responsive="{1200: {items: 3}, 992: {items: 2}}">
                                 
                                @foreach($this->branches as $branch)
                                <div class="text-center px-5">
                                    <div class="card card-flush h-100">
                                        <div class="card-body">
                                            <div class="octagon mx-auto mb-5 d-flex w-200px h-200px bgi-no-repeat bgi-size-contain bgi-position-center" 
                                                 style="background-image:url('{{ asset('media/icons/logo.png') }}')"></div>
                                            <div class="mb-5">
                                                <h3 class="text-gray-900 fw-bold text-hover-primary fs-3 mb-2">{{ $branch->name }}</h3>
                                                <div class="text-muted fs-6 fw-semibold">{{ $branch->address }}</div>
                                            </div>
                                            <a href="{{ route('branch.show', ['branch' => $branch]) }}" wire:navigate class="btn btn-sm btn-light-primary fw-bold">
                                                Lihat Detail
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                            
                            <button class="btn btn-icon btn-active-color-primary position-absolute top-50 start-0 translate-middle-y" id="kt_team_slider_prev">
                                <i class="ki-outline ki-left fs-3x"></i>
                            </button>
                            <button class="btn btn-icon btn-active-color-primary position-absolute top-50 end-0 translate-middle-y" id="kt_team_slider_next">
                                <i class="ki-outline ki-right fs-3x"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Media Sosial -->
                    <div class="card mb-4 bg-light text-center">
                        <div class="card-body py-12">
                            @foreach ($this->brands as $item)
                            <a href="{{ route('brand.show', ['slug' => $item]) }}" wire:navigate class="mx-4">
                                <img src="{{ Str::remove('-dark',$item->image) }}" class="theme-light-show h-30px my-2" alt="{{$item->name}}" />
                                <img src="{{ $item->image }}" class="theme-dark-show h-30px my-2" alt="{{$item->name}}" />
                            </a>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endvolt
</x-app>