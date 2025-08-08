<?php

return [
    'name' => 'Adamasanya',
    'manifest' => [
        'name' => env('APP_NAME', 'Adamasanya'),
        'short_name' => 'Adamasanya',
        'start_url' => '/',
        'background_color' => '#ffffff',
        'theme_color' => '#000000',
        'display' => 'standalone',
        'orientation'=> 'potrait',
        'status_bar'=> 'black',
        'icons' => [
            '72x72' => [
                'path' => '/media/icons/icon-72x72.png',
                'purpose' => 'any'
            ],
            '96x96' => [
                'path' => '/media/icons/icon-96x96.png',
                'purpose' => 'any'
            ],
            '128x128' => [
                'path' => '/media/icons/icon-128x128.png',
                'purpose' => 'any'
            ],
            '144x144' => [
                'path' => '/media/icons/icon-144x144.png',
                'purpose' => 'any'
            ],
            '152x152' => [
                'path' => '/media/icons/icon-152x152.png',
                'purpose' => 'any'
            ],
            '192x192' => [
                'path' => '/media/icons/icon-192x192.png',
                'purpose' => 'any'
            ],
            '384x384' => [
                'path' => '/media/icons/icon-384x384.png',
                'purpose' => 'any'
            ],
            '512x512' => [
                'path' => '/media/icons/icon-512x512.png',
                'purpose' => 'any'
            ],
        ],
        'splash' => [
            '640x1136' => '/media/splash/splash-640x1136.png',
            '750x1334' => '/media/splash/splash-750x1334.png',
            '828x1792' => '/media/splash/splash-828x1792.png',
            '1125x2436' => '/media/splash/splash-1125x2436.png',
            '1136x640' => '/media/splash/splash-1136x640.png',
            '1170x2532' => '/media/splash/splash-1170x2532.png',
            '1179x2556' => '/media/splash/splash-1179x2556.png',
            '1206x2622' => '/media/splash/splash-1206x2622.png',
            '1242x2208' => '/media/splash/splash-1242x2208.png',
            '1242x2688' => '/media/splash/splash-1242x2688.png',
            '1284x2778' => '/media/splash/splash-1284x2778.png',
            '1290x2796' => '/media/splash/splash-1290x2796.png',
            '1334x750' => '/media/splash/splash-1334x750.png',
            '1320x2868' => '/media/splash/splash-1320x2868.png',
            '1488x2256' => '/media/splash/splash-1488x2256.png',
            '1536x2048' => '/media/splash/splash-1536x2048.png',
            '1620x2160' => '/media/splash/splash-1620x2160.png',
            '1640x2360' => '/media/splash/splash-1640x2360.png',
            '1668x2224' => '/media/splash/splash-1668x2224.png',
            '1668x2388' => '/media/splash/splash-1668x2388.png',
            '1792x828' => '/media/splash/splash-1792x828.png',
            '2048x1536' => '/media/splash/splash-2048x1536.png',
            '2048x2732' => '/media/splash/splash-2048x2732.png',
            '2064x2752' => '/media/splash/splash-2064x2752.png',
            '2160x1620' => '/media/splash/splash-2160x1620.png',
            '2208x1242' => '/media/splash/splash-2208x1242.png',
            '2224x1668' => '/media/splash/splash-2224x1668.png',
            '2266x1488' => '/media/splash/splash-2266x1488.png',
            '2360x1640' => '/media/splash/splash-2360x1640.png',
            '2388x1688' => '/media/splash/splash-2388x1688.png',
            '2420x1668' => '/media/splash/splash-2420x1668.png',
            '2436x1125' => '/media/splash/splash-2436x1125.png',
            '2532x1170' => '/media/splash/splash-2532x1170.png',
            '2556x1179' => '/media/splash/splash-2556x1179.png',
            '2622x1206' => '/media/splash/splash-2622x1206.png',
            '2688x1242' => '/media/splash/splash-2688x1242.png',
            '2732x2048' => '/media/splash/splash-2732x2048.png',
            '2752x2064' => '/media/splash/splash-2752x2064.png',
            '2778x1284' => '/media/splash/splash-2778x1284.png',
            '2796x1290' => '/media/splash/splash-2796x1290.png',
            '2868x1320' => '/media/splash/splash-2868x1320.png',
        ],
        'shortcuts' => [
            [
                'name' => 'Shortcut Link 1',
                'description' => 'Shortcut Link 1 Description',
                'url' => '/shortcutlink1',
                'icons' => [
                    "src" => "/media/icons/icon-72x72.png",
                    "purpose" => "any"
                ]
            ],
            [
                'name' => 'Shortcut Link 2',
                'description' => 'Shortcut Link 2 Description',
                'url' => '/shortcutlink2'
            ]
        ],
        'custom' => []
    ]
];
