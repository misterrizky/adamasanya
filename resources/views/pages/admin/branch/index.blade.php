<?php
use function Livewire\Volt\{state};
use function Laravel\Folio\{middleware, name};
name('admin.branch');

state(['search'])->url();
?>
<x-app>
    <!--begin::Toolbar-->
    <x-toolbar 
        title="Data Cabang"
        :breadcrumbs="[
            ['icon' => 'home', 'url' => route('home')],
            ['text' => 'HRM', 'active' => true],
            ['text' => 'Data Cabang', 'active' => true]
        ]"
    />
    <!--end::Toolbar-->
    <!--begin::Content-->
    @volt
    <div id="kt_app_content" class="app-content flex-column-fluid">
        <!--begin::Content container-->
        <livewire:data.master.branch/>
        <livewire:modal.export nama="branch" model="Branch" class="Branch\BranchExport"/>
        <!--end::Content container-->
    </div>
    @endvolt
    <!--end::Content-->
</x-app>