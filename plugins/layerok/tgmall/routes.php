<?php

use Illuminate\Support\Facades\Route;
use Layerok\TgMall\Classes\Webhook;

Route::post('/layerok/tgmall/webhook', function ()  {
    $bot_token = \Config::get('layerok.tgmall::credentials.test_bot_token');

    try {
        new Webhook($bot_token);
    } catch (\Exception $exception) {
        $foo = [];
        return response('ok', 200);
    } catch (\Symfony\Component\ErrorHandler\Error\FatalError $error) {
        return response('ok', 200);
    }
});

// todo: create a form in the adminpanel for setting a webhook
Route::get('/tgmall/set/webhook', function(\Illuminate\Http\Request $request)  {
    $query = $request->query();
    $token = $query['token'];
    $url = $query['url'];
    $api = new \Telegram\Bot\Api($token);
    $resp = $api->setWebhook([
        'url' => $url
    ]);
    dd($resp);
});




