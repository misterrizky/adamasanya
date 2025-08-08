<?php
use function Laravel\Folio\name;
name('profile.privacy');
?>
<x-app>
    <x-toolbar-mobile 
        :breadcrumbs="[
            ['icon' => 'arrow-left', 'url' => route('profile.setting')],
            ['text' => 'Privasi Akun', 'active' => true]
        ]"
    />
    @volt
    <div id="kt_app_content" class="app-content flex-column-fluid">
    
    </div>
    @endvolt
</x-app>