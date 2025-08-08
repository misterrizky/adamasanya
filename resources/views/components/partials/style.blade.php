<head>
    <title>{{ config('app.name') }}</title>
    <meta charset="utf-8" />
    <meta name="description" content="{{ config('app.desc') }}" />
    <meta name="keywords" content="rental, rental iphone, rental kamera, rental lensa, rent, rent iphone, rent kamera, rent lensa,sewa, sewa iphone, sewa kamera, sewa lensa, bandung, murah, lengkap, adamasanya, adamasanya buahbatu" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta property="og:locale" content="en_US" />
    <meta property="og:type" content="article" />
    <meta property="og:title" content="{{ config('app.name') }}" />
    <meta property="og:url" content="https://adamasanya.com" />
    <meta property="og:site_name" content="eCommerce and Rent App by Yada Ekidanta" />
    <link rel="canonical" href="http://adamasanya.com" />
    <link rel="shortcut icon" href="{{ asset('media/icons/logo.png') }}" />
    <!--begin::Fonts(mandatory for all pages)-->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Inter:300,400,500,600,700" />
    <!--end::Fonts-->
    <!--begin::Vendor Stylesheets(used for this page only)-->
    <link href="{{ asset('plugins/custom/fullcalendar/fullcalendar.bundle.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('plugins/custom/datatables/datatables.bundle.css') }}" rel="stylesheet" type="text/css" />
    <!--end::Vendor Stylesheets-->
    <!--begin::Global Stylesheets Bundle(mandatory for all pages)-->
    <link href="{{ asset('plugins/global/plugins.bundle.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('css/style.bundle.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('css/leaflet.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('css/esri-leaflet-geocoder.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('css/aos.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('css/custom.css') }}" rel="stylesheet" type="text/css" />
    <!--end::Global Stylesheets Bundle-->
    @laravelPWA
</head>