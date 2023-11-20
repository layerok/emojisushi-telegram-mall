<?php

namespace Layerok\TgMall\Features\Checkout\Keyboards;

use Telegram\Bot\Keyboard\Keyboard;

class SticksCounterKeyboard
{
    public function __construct(public $count)
    {

    }

    public function getKeyboard(): Keyboard
    {
        return (new Keyboard())->inline()->row([
            Keyboard::inlineButton([
                'text' => \Lang::get('layerok.tgmall::lang.telegram.buttons.minus'),
                'callback_data' => json_encode([
                    'update_sticks_counter',
                    [$this->count - 1]
                ])
            ]),
            Keyboard::inlineButton([
                'text' => $this->count,
                'callback_data' => json_encode(['noop', []])
            ]),
            Keyboard::inlineButton([
                'text' => \Lang::get('layerok.tgmall::lang.telegram.buttons.plus'),
                'callback_data' => json_encode(['update_sticks_counter', [$this->count + 1]])
            ])
        ])->row([
            Keyboard::inlineButton([
                'text' => \Lang::get('layerok.tgmall::lang.telegram.buttons.next'),
                'callback_data' =>
                    json_encode(['wish_to_leave_comment', []])
            ])
        ]);
    }
}
