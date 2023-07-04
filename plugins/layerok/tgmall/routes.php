<?php

use Illuminate\Support\Facades\Route;


Route::post('/layerok/tgmall/webhook', \Layerok\TgMall\Controllers\WebhookController::class);

// to set webhook
// https://api.telegram.org/bot{{token}}/setWebhook?url={{url}}



