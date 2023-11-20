<?php

namespace Layerok\TgMall\Features\Checkout\Keyboards;

use Layerok\TgMall\Facades\EmojisushiApi;
use Layerok\TgMall\Objects\PaymentMethod;
use Telegram\Bot\Keyboard\Keyboard;

class PaymentMethodsKeyboard
{
    public array $vars;

    public function __construct($vars = [])
    {
        $this->vars = $vars;
    }

    public function getKeyboard(): Keyboard
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

        return $keyboard;
    }
}
