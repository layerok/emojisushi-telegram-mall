<?php

use Illuminate\Support\Facades\Route;
use \Layerok\TgMall\Controllers\WebhookController;


Route::post('/layerok/tgmall/webhook', WebhookController::class);

// to set webhook
// https://api.telegram.org/bot{{token}}/setWebhook?url={{ngrokUrl}}/layerok/tgmall/webhook

// to proxy webhook requests
// ngrok http {{host}} --host-header=rewrite


