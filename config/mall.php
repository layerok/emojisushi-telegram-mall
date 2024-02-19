<?php

return [
    'credentials' => [
        'bot_token' => env('TG_MALL_BOT_TOKEN')
    ],
    'api_url' => env('EMOJISUSHI_API_URL', 'https://api.emojisushi.com.ua/api/'),
    'settings' => [
        'products' => [
            'per_page' => 10
        ]
    ]
];
