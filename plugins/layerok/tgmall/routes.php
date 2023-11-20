<?php

use Illuminate\Support\Facades\Route;


Route::post('/layerok/tgmall/webhook', \Layerok\TgMall\Controllers\WebhookController::class);

// to set webhook
// https://api.telegram.org/bot{{token}}/setWebhook?url={{ngrokUrl}}/layerok/tgmall/webhook

// to proxy webhook requests
// ngrok http {{host}} --host-header=rewrite


