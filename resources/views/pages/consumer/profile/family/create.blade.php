<?php
use function Laravel\Folio\name;
name('profile.create-family');
?>
<x-app>
    <x-toolbar-mobile 
        :breadcrumbs="[
            ['icon' => 'arrow-left', 'url' => route('profile.family')],
            ['text' => 'Detail Keluarga', 'active' => true]
        ]"
    />
    <div id="kt_app_content" class="app-content flex-column-fluid">
        <livewire:form.profile.family/>
    </div>
</x-app>