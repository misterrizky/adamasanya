<?php
use function Livewire\Volt\{state};
use function Laravel\Folio\{middleware, name};
name('contact');

state(['search'])->url();
?>
<x-app>
    <style>
        
    </style>
    <x-toolbar-mobile 
        :breadcrumbs="[
            ['icon' => 'arrow-left', 'url' => route('profile.setting')],
            ['text' => 'Kontak Kami', 'active' => true]
        ]"
    />
    <x-toolbar 
        title="Kontak Kami"
        :breadcrumbs="[
            ['icon' => 'home', 'url' => route('home')],
            ['text' => 'Explore More', 'active' => true],
            ['text' => 'Kontak Kami', 'active' => true]
        ]"
        toolbar-class="py-3 py-lg-6"
    />
    @volt
    <div class="d-flex flex-column flex-column-fluid">
        <div id="kt_app_content" class="app-content flex-column-fluid">
            <div class="card">
                <div class="card-body p-lg-17">
                    <div class="row mb-3">
                        <div class="col-md-6 pe-lg-10">
                            <form action="" class="form mb-15" method="post" id="kt_contact_form">
                                <h1 class="fw-bold text-gray-900 mb-9">Send Us Email</h1>
                                <div class="row mb-5">
                                    <div class="col-md-6 fv-row">
                                        <label class="fs-5 fw-semibold mb-2">Name</label>
                                        <input type="text" class="form-control form-control-solid" placeholder="" name="name" />
                                    </div>
                                    <div class="col-md-6 fv-row">
                                        <label class="fs-5 fw-semibold mb-2">Email</label>
                                        <input type="text" class="form-control form-control-solid" placeholder="" name="email" />
                                    </div>
                                </div>
                                <div class="d-flex flex-column mb-5 fv-row">
                                    <label class="fs-5 fw-semibold mb-2">Subject</label>
                                    <input class="form-control form-control-solid" placeholder="" name="subject" />
                                </div>
                                <div class="d-flex flex-column mb-10 fv-row">
                                    <label class="fs-6 fw-semibold mb-2">Message</label>
                                    <textarea class="form-control form-control-solid" rows="6" name="message" placeholder=""></textarea>
                                </div>
                                <button type="submit" class="btn btn-primary" id="kt_contact_submit_button">
                                    <span class="indicator-label">Send Feedback</span>
                                    <span class="indicator-progress">Please wait... 
                                    <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                                </button>
                            </form>
                        </div>
                        <div class="col-md-6 ps-lg-10">
                            <div id="map_adamasanya" class="w-100 rounded mb-2 mb-lg-0 mt-2" style="height: 486px"></div>
                        </div>
                    </div>
                    <div class="row g-5 mb-5 mb-lg-15">
                        <div class="col-sm-6 pe-lg-10">
                            <div class="bg-light card-rounded d-flex flex-column flex-center flex-center p-10 h-100">
                                <i class="ki-outline ki-briefcase fs-3tx text-primary"></i>
                                <h1 class="text-gray-900 fw-bold my-5">Let’s Speak</h1>
                                <div class="text-gray-700 fw-semibold fs-2">+62 877-6534-6368</div>
                            </div>
                        </div>
                        <div class="col-sm-6 ps-lg-10">
                            <div class="text-center bg-light card-rounded d-flex flex-column flex-center p-10 h-100">
                                <i class="ki-outline ki-geolocation fs-3tx text-primary"></i>
                                <h1 class="text-gray-900 fw-bold my-5">Our Head Office</h1>
                                <div class="text-gray-700 fs-3 fw-semibold">Jl. Muara Takus Raya Jl. Trowulan No.21A</div>
                            </div>
                        </div>
                    </div>
                    <div class="card mb-4 bg-light text-center">
                        <div class="card-body py-12">
                            <a href="#" class="mx-4">
                                <img src="{{asset('media/svg/brand-logos/facebook-4.svg')}}" class="h-30px my-2" alt="" />
                            </a>
                            <a href="#" class="mx-4">
                                <img src="{{asset('media/svg/brand-logos/instagram-2-1.svg')}}" class="h-30px my-2" alt="" />
                            </a>
                            <a href="#" class="mx-4">
                                <img src="{{asset('media/svg/brand-logos/github.svg')}}" class="h-30px my-2" alt="" />
                            </a>
                            <a href="#" class="mx-4">
                                <img src="{{asset('media/svg/brand-logos/behance.svg')}}" class="h-30px my-2" alt="" />
                            </a>
                            <a href="#" class="mx-4">
                                <img src="{{asset('media/svg/brand-logos/pinterest-p.svg')}}" class="h-30px my-2" alt="" />
                            </a>
                            <a href="#" class="mx-4">
                                <img src="{{asset('media/svg/brand-logos/twitter.svg')}}" class="h-30px my-2" alt="" />
                            </a>
                            <a href="#" class="mx-4">
                                <img src="{{asset('media/svg/brand-logos/dribbble-icon-1.svg')}}" class="h-30px my-2" alt="" />
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endvolt
    @section('custom_js')
    <script data-navigate-once>
        "use strict";
        var AdamasanyaMap = function() {
            var map, coordDisplay;
            return {
                init: function() {
                    if (typeof L !== 'undefined') {
                        // Initialize map
                        map = L.map('map_adamasanya', {
                            center: [-6.91879, 107.55469], // Pusat peta (Cimahi)
                            zoom: 5
                        });

                        // Add OpenStreetMap tiles
                        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                            attribution: '&copy; <a href="https://osm.org/copyright">OpenStreetMap</a> contributors'
                        }).addTo(map);

                        // Create coordinate display div
                        // coordDisplay = L.control({position: 'bottomleft'});
                        // coordDisplay.onAdd = function(map) {
                        //     this._div = L.DomUtil.create('div', 'coordinate-display');
                        //     this.update();
                        //     return this._div;
                        // };
                        // coordDisplay.update = function(pos) {
                        //     var lat = pos ? pos.lat.toFixed(5) : map.getCenter().lat.toFixed(5);
                        //     var lng = pos ? pos.lng.toFixed(5) : map.getCenter().lng.toFixed(5);
                        //     this._div.innerHTML = `
                        //         <div class="bg-white p-2 rounded shadow-sm">
                        //             <strong>Koordinat:</strong><br>
                        //             Latitude: ${lat}<br>
                        //             Longitude: ${lng}
                        //         </div>
                        //     `;
                        // };
                        // coordDisplay.addTo(map);

                        // Custom icon
                        var customIcon = L.divIcon({
                            html: '<i class="ki-solid ki-geolocation text-primary fs-3x"></i>',
                            bgPos: [10, 10],
                            iconAnchor: [20, 37],
                            popupAnchor: [0, -37],
                            className: "leaflet-marker"
                        });

                        // Array of locations
                        var locations = [
                            {
                                coords: [-6.91879, 107.55469],
                                title: "Adamasanyaforlife",
                                popup: "<b>Adamasanyaforlife</b><br>Jl. Muara Takus Raya Jl. Trowulan No.21A"
                            },
                            {
                                coords: [-8.6682339, 115.1833329],
                                title: "adamasanya.bali",
                                popup: "<b>Adamasanya Bali</b><br>Gg Dieng 10, No 10"
                            },
                            {
                                coords: [-6.3090973,106.9901487],
                                title: "adamasanya.bekasi",
                                popup: "<b>Adamasanya Bekasi</b><br>KOMPLEK SABRINA AZZURA, JALAN SDN BLOK Q NO. 8"
                            },
                            {
                                coords: [-6.9738719,107.6385797],
                                title: "adamasanya.buahbatu",
                                popup: "<b>Adamasanya BuahBatu</b><br>Komplek Permata Buah Batu Blok C 15B"
                            },
                                                        {
                                coords: [-7.3542963,107.8033139],
                                title: "adamasanya.garut",
                                popup: "<b>Adamasanya Garut</b>"
                            },
                            {
                                coords: [-6.9837539,107.2738438],
                                title: "adamasanya.gununghalu",
                                popup: "<b>Adamasanya Gunung Halu</b><br>Cibitung Simpang"
                            },
                            {
                                coords: [-6.9361339,107.7739318],
                                title: "adamasanya.jatinangor",
                                popup: "<b>Adamasanya Jatinangor</b><br>Jl. Kolonel Ahmad Syam"
                            },
                            {
                                coords: [-6.3526789,107.3763511],
                                title: "adamasanya.karawang",
                                popup: "<b>Adamasanya Karawang</b><br>Perum Vila duta pratama Blk. C No.12"
                            },
                            {
                                coords: [-6.474913,107.4615863],
                                title: "adamasanya.purwakarta",
                                popup: "<b>Adamasanya Purwakarta</b><br>Perumahan Bumi Purwa Raya No.4 Blok D"
                            },
                            {
                                coords: [-6.9277708,106.928656],
                                title: "adamasanya.smi",
                                popup: "<b>Adamasanya Sukabumi</b><br>Gg. Arayana No.Rt04/05"
                            },
                            {
                                coords: [-6.1931842,106.6947039],
                                title: "adamasanya.tgr",
                                popup: "<b>Adamasanya Tangerang</b><br>Gg. Damai 1, RT.02/RW.04"
                            },
                            {
                                coords: [-6.9194317,107.5528837],
                                title: "Adamasanya Studio & Outfit Casual",
                                popup: "<b>Sewa Outfit Casual & Studio</b><br>Jl. Rorojonggrang IV No.16"
                            }
                        ];

                        // Add markers
                        var markers = L.layerGroup().addTo(map);
                        locations.forEach(function(location) {
                            L.marker(location.coords, {
                                icon: customIcon,
                                title: location.title
                            }).addTo(markers)
                            .bindPopup(location.popup);
                        });

                        // Update coordinates on move
                        map.on('move', function() {
                            coordDisplay.update();
                        });

                        // Update coordinates on click
                        map.on('click', function(e) {
                            coordDisplay.update(e.latlng);
                            
                            // Optional: Show popup with coordinates
                            L.popup()
                                .setLatLng(e.latlng)
                                .setContent(`Koordinat yang diklik:<br>Lat: ${e.latlng.lat.toFixed(5)}<br>Lng: ${e.latlng.lng.toFixed(5)}`)
                                .openOn(map);
                        });

                        // Fit map to show all markers
                        map.fitBounds(markers.getBounds().pad(0.5));
                    }
                }
            };
        }();
        document.addEventListener('DOMContentLoaded', () => {
            AdamasanyaMap.init();
        });
        document.addEventListener('livewire:navigated', () => {
            AdamasanyaMap.init();
        });
    </script>
    @endsection
</x-app>