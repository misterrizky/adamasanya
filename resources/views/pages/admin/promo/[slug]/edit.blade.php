<?php
use function Laravel\Folio\name;

name('admin.promo.edit');
?>

<x-app>
    <div id="kt_app_content" class="app-content flex-column-fluid py-10">
        <livewire:form.promo :slug="$slug"/>
    </div>
</x-app>