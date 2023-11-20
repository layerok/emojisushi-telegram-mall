<?php namespace Layerok\TgMall\Features\Checkout\Keyboards;

use Lang;
use Telegram\Bot\Keyboard\Keyboard;


class SticksKeyboard
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
                'text' => Lang::get('layerok.tgmall::lang.telegram.buttons.yes'),
                'callback_data' => json_encode([
                    'yes_sticks', []
                ])
            ]),
            Keyboard::inlineButton([
                'text' => Lang::get('layerok.tgmall::lang.telegram.buttons.no'),
                'callback_data' => json_encode([
                    'wish_to_leave_comment', []
                ])
            ])
        ]);

    }
}
