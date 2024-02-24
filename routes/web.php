<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WebhookController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::post('/webhook', WebhookController::class);

// to set webhook
// https://api.telegram.org/bot{{token}}/setWebhook?url={{ngrokUrl}}/webhook

// to proxy webhook requests
// ngrok http {{host}} --host-header=rewrite
