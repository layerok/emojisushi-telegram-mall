<?php namespace Layerok\TgMall\Features\Checkout\Keyboards;

use Layerok\TgMall\Classes\Keyboards\InlineKeyboard;


class SticksKeyboard extends InlineKeyboard
{
    public function build(): void
    {
        $this->append([
            'text' => \Lang::get('layerok.tgmall::lang.telegram.buttons.yes'),
            'callback_data' => json_encode([
                'yes_sticks', []
            ])
        ])->append([
            'text' => \Lang::get('layerok.tgmall::lang.telegram.buttons.no'),
            'callback_data' => json_encode([
                'wish_to_leave_comment', []
            ])
        ]);

    }
}
