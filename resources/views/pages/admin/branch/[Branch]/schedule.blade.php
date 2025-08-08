<?php

use function Laravel\Folio\name;

name('admin.branch.schedule');
?>
<x-app>
    <x-toolbar-mobile 
        :breadcrumbs="[
            ['icon' => 'arrow-left', 'url' => route('admin.dashboard')],
            ['text' => 'Jadwal Cabang', 'active' => true]
        ]"
    />
    <x-toolbar 
        title="Jadwal Cabang"
        :breadcrumbs="[
            ['icon' => 'ki-outline ki-home', 'url' => route('admin.dashboard')],
            ['text' => 'Manajemen Cabang', 'active' => true],
            ['text' => 'Jadwal Cabang', 'active' => true]
        ]"
        toolbar-class="py-3 py-lg-6"
    />
</x-app>