<?php
use function Livewire\Volt\{state};
use function Laravel\Folio\{middleware, name};
name('product.menu');

state(['search'])->url();
?>
<x-app>
    @volt
    <div class="d-flex flex-column flex-column-fluid">
        <!--begin::Toolbar-->
        <x-toolbar 
            title="Menu Produk"
            :breadcrumbs="[
                ['icon' => 'home', 'url' => route('home')],
                ['text' => 'Produk', 'active' => true]
            ]"
            toolbar-class="py-3 py-lg-6"
        />
        <!--end::Toolbar-->
        <!--begin::Content-->
        <div id="kt_app_content" class="app-content flex-column-fluid">
            <!--begin::card-->
            <x-widget type="profile" href="{{ route('category') }}" name="Kategori" position="Manajemen Kategori" avatar="{{ asset('media/svg/avatars/029-boy-11.svg') }}"/>
            <x-widget type="profile" href="{{ route('brand') }}" name="Merk" position="Manajemen Merk" avatar="{{ asset('media/svg/avatars/029-boy-11.svg') }}"/>
            <x-widget type="profile" href="{{ route('product-master') }}" name="Master" position="Master Produk" avatar="{{ asset('media/svg/avatars/029-boy-11.svg') }}"/>
            <x-widget type="profile" href="{{ route('product-rent') }}" name="Sewa" position="Produk Sewa" avatar="{{ asset('media/svg/avatars/029-boy-11.svg') }}"/>
            <x-widget type="profile" href="{{ route('product-sale') }}" name="Sewa" position="Produk Jual" avatar="{{ asset('media/svg/avatars/029-boy-11.svg') }}"/>
            <!--end::card-->
        </div>
        <!--end::Content-->
    </div>
    @endvolt
</x-app>