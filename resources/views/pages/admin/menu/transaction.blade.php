<?php
use function Livewire\Volt\{state};
use function Laravel\Folio\{middleware, name};
name('transaction.menu');

state(['search'])->url();
?>
<x-app>
    @volt
    <div class="d-flex flex-column flex-column-fluid">
        <!--begin::Toolbar-->
        <x-toolbar 
            title="Menu Keuangan"
            :breadcrumbs="[
                ['icon' => 'home', 'url' => route('home')],
                ['text' => 'Keuangan', 'active' => true]
            ]"
            toolbar-class="py-3 py-lg-6"
        />
        <!--end::Toolbar-->
        <!--begin::Content-->
        <div id="kt_app_content" class="app-content flex-column-fluid">
            <x-widget type="profile" href="{{ route('category') }}" name="Kupon / Promo" position="Manajemen Kupon / Promo" avatar="{{ asset('media/svg/avatars/029-boy-11.svg') }}"/>
            <x-widget type="profile" href="{{ route('category') }}" name="Transaksi Sewa" position="Manajemen Transaksi Sewa" avatar="{{ asset('media/svg/avatars/029-boy-11.svg') }}"/>
            <x-widget type="profile" href="{{ route('category') }}" name="Transaksi Jual" position="Manajemen Transaksi Jual" avatar="{{ asset('media/svg/avatars/029-boy-11.svg') }}"/>
        </div>
        <!--end::Content-->
    </div>
    @endvolt
</x-app>