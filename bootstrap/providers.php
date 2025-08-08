<?php

return [
    App\Providers\AppServiceProvider::class,
    App\Providers\FolioServiceProvider::class,
    App\Providers\VoltServiceProvider::class,
    Barryvdh\DomPDF\ServiceProvider::class,
    Cmgmyr\Messenger\MessengerServiceProvider::class,
    Cog\Laravel\Ban\Providers\BanServiceProvider::class,
    Laravolt\Avatar\ServiceProvider::class,
    Maatwebsite\Excel\ExcelServiceProvider::class,
    NotificationChannels\WebPush\WebPushServiceProvider::class,
    Spatie\Permission\PermissionServiceProvider::class,
];
