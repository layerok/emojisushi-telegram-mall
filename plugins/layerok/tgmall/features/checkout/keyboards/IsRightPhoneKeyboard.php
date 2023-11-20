<?php

namespace Layerok\TgMall\Features\Checkout\Keyboards;

use Telegram\Bot\Keyboard\Keyboard;

class IsRightPhoneKeyboard
{
    public array $vars;

    public function __construct($vars = [])
    {
        $this->vars = $vars;
    }

    public function getKeyboard(): Keyboard
    {
        return (new Keyboard())->inline()->row([
            Keyboard::inlineButton([
                'text' => \Lang::get('layerok.tgmall::lang.telegram.buttons.yes'),
                'callback_data' => json_encode(['list_payment_methods', []])
            ]),
            Keyboard::inlineButton([
                'text' => \Lang::get('layerok.tgmall::lang.telegram.buttons.no'),
                'callback_data' => json_encode(['enter_phone', []])
            ])
        ]);
    }
}
