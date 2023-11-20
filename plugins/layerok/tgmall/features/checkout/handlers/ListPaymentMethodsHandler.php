<?php

namespace Layerok\TgMall\Features\Checkout\Handlers;

use Layerok\TgMall\Classes\Callbacks\Handler;
use Layerok\TgMall\Facades\EmojisushiApi;
use Layerok\TgMall\Objects\PaymentMethod;
use Telegram\Bot\Keyboard\Keyboard;

class ListPaymentMethodsHandler extends Handler
{
    protected string $name = "list_payment_methods";


    public function run()
    {
        $keyboard = (new Keyboard())->inline();
        collect(EmojisushiApi::getPaymentMethods()->data)->each(function(PaymentMethod $method) use($keyboard) {
            $keyboard->row([
                Keyboard::inlineButton([
                    'text' => $method->name,
                    'callback_data' => json_encode([
                        'chose_payment_method',
                        ['id' => $method->id]
                    ])
                ])
            ])->row([]);
        });


        $this->replyWithMessage([
            'chat_id' => $this->user->chat_id,
            'text' => \Lang::get('layerok.tgmall::lang.telegram.texts.chose_payment_method'),
            'reply_markup' => $keyboard
        ]);
    }
}
