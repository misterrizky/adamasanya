<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Laravel\Folio\Folio;

class FolioServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
         Folio::path(resource_path('views/pages'))->middleware([
            'admin/*' => ['auth', 'is_admin'],
            'consumer/*' => ['auth'],
            'checkout/*' => ['auth'],
            'cart' => ['auth'],
            'payment/*' => ['auth'],
            'history/*' => ['auth'],
            'wishlist' => ['auth'],
            'careers/applications' => ['auth'],
            'careers/*/apply' => ['auth'],
            '*' => [],
        ]);
    }
}
