<?php

namespace Layerok\TgMall\Features\Checkout\Keyboards;

use Layerok\TgMall\Classes\Keyboards\InlineKeyboard;

class SticksCounterKeyboard extends InlineKeyboard
{
    public function build(): void
    {

        $count = $this->vars['count'];

        $this->append([
            'text' => \Lang::get('layerok.tgmall::lang.telegram.buttons.minus'),
            'callback_data' => json_encode([
                'update_sticks_counter',
                [$count - 1]
            ])
        ])->append([
            'text' => $count,
            'callback_data' => json_encode(['noop', []])
        ])->append([
            'text' => \Lang::get('layerok.tgmall::lang.telegram.buttons.plus'),
            'callback_data' => json_encode(['update_sticks_counter', [$count + 1]])
        ])->nextRow()->append([
            'text' => \Lang::get('layerok.tgmall::lang.telegram.buttons.next'),
            'callback_data' =>
                json_encode(['wish_to_leave_comment', []])
        ]);
    }
}
