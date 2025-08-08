<?php
use function Livewire\Volt\{state};
use function Laravel\Folio\{middleware, name};
name('hrm.menu');

state(['search'])->url();
?>
<x-app>
    @volt
    <div class="d-flex flex-column flex-column-fluid">
        <!--begin::Toolbar-->
        <x-toolbar 
            title="Menu HRM"
            :breadcrumbs="[
                ['icon' => 'home', 'url' => route('home')],
                ['text' => 'HRM', 'active' => true]
            ]"
            toolbar-class="py-3 py-lg-6"
        />
        <!--end::Toolbar-->
        <!--begin::Content-->
        <div id="kt_app_content" class="app-content flex-column-fluid">
            <!--begin::card-->
            <x-widget type="profile" href="{{ route('category') }}" name="Cabang" position="Manajemen Cabang" avatar="{{ asset('media/svg/avatars/029-boy-11.svg') }}"/>
            <x-widget type="profile" href="{{ route('category') }}" name="Konsumen" position="Manajemen Konsumen" avatar="{{ asset('media/svg/avatars/029-boy-11.svg') }}"/>
            <x-widget type="profile" href="{{ route('category') }}" name="Pengguna" position="Manajemen Pengguna" avatar="{{ asset('media/svg/avatars/029-boy-11.svg') }}"/>
            <!--end::card-->
        </div>
        <!--end::Content-->
    </div>
    @endvolt
</x-app>