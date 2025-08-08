<?php
use function Livewire\Volt\{state};
?>
<script>// Frame-busting to prevent site from being loaded within a frame without permission (click-jacking) if (window.top != window.self) { window.top.location.replace(window.self.location.href); }</script>
<script data-navigate-once>
var defaultThemeMode = "light";
var themeMode;
</script>
<script data-navigate-once>
    var hostUrl = "/";
</script>
<!--begin::Global Javascript Bundle(mandatory for all pages)-->
<script data-navigate-once src="{{ asset('plugins/global/plugins.bundle.js') }}"></script>
<script data-navigate-once src="{{ asset('js/scripts.bundle.js') }}"></script>
<script data-navigate-once src="{{ asset('js/aos.js') }}"></script>
<script data-navigate-once src="{{ asset('js/function.js') }}"></script>
<script data-navigate-once src="{{ asset('js/metronic-navigated.js') }}"></script>
<script data-navigate-once src="{{ asset('js/leaflet.js') }}"></script>
<script data-navigate-once src="{{ asset('js/esri-leaflet.js') }}"></script>
<script data-navigate-once src="{{ asset('js/esri-leaflet-geocoder.js') }}"></script>
<!--end::Global Javascript Bundle-->
<!--begin::Vendors Javascript(used for this page only)-->
<script data-navigate-once src="{{asset('plugins/custom/fslightbox/fslightbox.bundle.js')}}"></script>
<script data-navigate-once src="{{asset('plugins/custom/typedjs/typedjs.bundle.js')}}"></script>
<script data-navigate-once src="{{ asset('plugins/custom/fullcalendar/fullcalendar.bundle.js') }} "></script>
<!--end::Vendors Javascript-->
<!--begin::Custom Javascript(used for this page only)-->
<script data-navigate-once src="{{ asset('js/widgets.bundle.js')}} "></script>
<script data-navigate-once src="{{ asset('js/custom/widgets.js')}} "></script>
<!--end::Custom Javascript-->