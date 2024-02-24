<?php

namespace App\Features\Checkout\Handlers;

use App\Classes\Callbacks\Handler;
use App\Facades\EmojisushiApi;
use App\Objects\PaymentMethod;
use Telegram\Bot\Keyboard\Keyboard;

class ListPaymentMethodsHandler extends Handler
{
    protected string $name = "list_payment_methods";

    public function run()
    {
        $keyboard = (new Keyboard())->inline();
        collect(EmojisushiApi::getPaymentMethods()->data)
            ->filter(fn(PaymentMethod $method) => $method->code !== 'wayforpay')
            ->each(fn(PaymentMethod $method) => $keyboard->row([
                Keyboard::inlineButton([
                    'text' => $method->name,
                    'callback_data' => json_encode([
                        'chose_payment_method',
                        ['id' => $method->id]
                    ])
                ])
            ])->row([]));


        $this->replyWithMessage([
            'chat_id' => $this->user->chat_id,
            'text' => \Lang::get('lang.telegram.texts.chose_payment_method'),
            'reply_markup' => $keyboard
        ]);
    }
}
