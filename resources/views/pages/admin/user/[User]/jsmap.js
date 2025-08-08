// "use strict";
        // var KonsumenMap = function() {
        //     var map, markers;
        //     return {
        //         init: function() {
        //             if (map) {
        //                 map.remove();
        //                 map = null;
        //             }
        //             if (typeof L !== 'undefined') {
        //                 // Dapatkan alamat utama user
        //                 @if($this->user->userAddresses->count() > 0)
        //                     @php
        //                     $alamat = $this->user->userAddresses->where('is_primary', true)->first();
        //                     $latitude = $alamat->lat ?? -6.91879;
        //                     $longitude = $alamat->lng ?? 107.55469;
        //                     $hasValidCoords = isset($alamat->lat) && isset($alamat->lng);
        //                     @endphp

        //                     // Initialize map dengan koordinat user
        //                     map = L.map('map_konsumen', {
        //                         center: [{{ $latitude }}, {{ $longitude }}],
        //                         zoom: {{ $hasValidCoords ? '15' : '5' }}
        //                     });

        //                     // Buat array locations dari alamat user
        //                     var locations = [];
        //                     @if($hasValidCoords)
        //                         locations.push({
        //                             coords: [{{ $latitude }}, {{ $longitude }}],
        //                             title: "Lokasi {{ $this->user->name }}",
        //                             popup: `
        //                                 <strong>{{ $alamat->address }}</strong><br>
        //                                 Kel. {{ $alamat->village->name }}, Kec. {{ $alamat->subdistrict->name }}<br>
        //                                 {{ $alamat->city->type }} {{ $alamat->city->name }}, {{ $alamat->state->name }}<br>
        //                                 {{ $alamat->village->poscode }}
        //                             `
        //                         });
        //                     @endif
        //                 @else
        //                     // Default map jika tidak ada alamat
        //                     map = L.map('map_konsumen', {
        //                         center: [-6.91879, 107.55469], // Pusat peta (Cimahi)
        //                         zoom: 5
        //                     });

        //                     var locations = [];
        //                 @endif

        //                 // Add OpenStreetMap tiles
        //                 L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        //                     attribution: '&copy; <a href="https://osm.org/copyright">OpenStreetMap</a> contributors'
        //                 }).addTo(map);

        //                 // Custom icon
        //                 var customIcon = L.divIcon({
        //                     html: '<i class="ki-solid ki-geolocation text-primary fs-3x"></i>',
        //                     bgPos: [10, 10],
        //                     iconAnchor: [20, 37],
        //                     popupAnchor: [0, -37],
        //                     className: "leaflet-marker"
        //                 });

        //                 // Add markers
        //                 var markers = L.layerGroup().addTo(map);
        //                 locations.forEach(function(location) {
        //                     L.marker(location.coords, {
        //                         icon: customIcon,
        //                         title: location.title
        //                     }).addTo(markers)
        //                     .bindPopup(location.popup);
        //                 });

        //                 // Fit map to show all markers jika ada
        //                 if (locations.length > 0) {
        //                     locations.forEach(function(location) {
        //                         L.marker(location.coords, {
        //                             icon: customIcon,
        //                             title: location.title
        //                         }).addTo(markers)
        //                         .bindPopup(location.popup);
        //                     });

        //                     // Fit bounds hanya jika ada markers
        //                     if (markers.getLayers().length > 0) {
        //                         map.fitBounds(markers.getBounds().pad(0.5));
        //                     } else {
        //                         // Default view jika tidak ada marker valid
        //                         map.setView([{{ $latitude ?? -6.91879 }}, {{ $longitude ?? 107.55469 }}], 15);
        //                     }
        //                 }
        //             }
        //         },
        //         destroy: function() {
        //             if (map) {
        //                 map.remove();
        //                 if (markers) {
        //                     markers.clearLayers();
        //                 }
        //                 map = null;
        //                 markers = null;
        //             }
        //         }
        //     };
        // }();
        // var konsumenMapInstance = KonsumenMap;
