<?php
use function Laravel\Folio\name;
name('profile.edit-address');
?>
<x-app>
    <x-toolbar-mobile 
        :breadcrumbs="[
            ['icon' => 'arrow-left', 'url' => route('profile.address')],
            ['text' => 'Detail Alamat', 'active' => true]
        ]"
    />
    <div id="kt_app_content" class="app-content flex-column-fluid">
        <livewire:form.profile.address :address="$userAddress"/>
    </div>
</x-app>