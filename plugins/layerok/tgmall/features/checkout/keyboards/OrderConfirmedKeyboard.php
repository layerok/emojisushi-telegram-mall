<?php namespace Layerok\TgMall\Features\Checkout\Keyboards;

use Telegram\Bot\Keyboard\Keyboard;

class OrderConfirmedKeyboard
{
    public function getKeyboard(): Keyboard
    {
        return (new Keyboard())->inline()->row([
            Keyboard::inlineButton([
                'text' => \Lang::get('layerok.tgmall::lang.telegram.buttons.in_menu_main'),
                'callback_data' => json_encode([
                    'start'
                ])
            ])
        ])->row([]);
    }
}
