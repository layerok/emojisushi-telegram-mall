<?php namespace Layerok\TgMall\Features\Checkout\Keyboards;

use Telegram\Bot\Keyboard\Keyboard;

class PreparePaymentChangeKeyboard
{
    public function getKeyboard(): Keyboard
    {
        return (new Keyboard())->inline()->row([
            Keyboard::inlineButton([
                'text' => 'Так',
                'callback_data' => json_encode([
                    'prepare_payment_change',
                    []
                ])
            ]),
            Keyboard::inlineButton([
                'text' => 'Ні',
                'callback_data' => json_encode([
                    'list_delivery_methods',
                    []
                ])
            ])
        ]);
    }
}
