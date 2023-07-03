<?php

use Illuminate\Support\Facades\Route;
use Layerok\TgMall\Classes\Webhook;

Route::post('/layerok/tgmall/webhook', function ()  {
    $bot_token = \Config::get('layerok.tgmall::credentials.bot_token');
    new Webhook($bot_token);
});

// to set webhook
// https://api.telegram.org/bot{{token}}/setWebhook?url={{url}}



