<?php namespace Layerok\TgMall\Features\Checkout\Keyboards;

use Layerok\TgMall\Classes\Keyboards\InlineKeyboard;

class OrderConfirmedKeyboard extends InlineKeyboard
{
    public function build(): void
    {
        $this->append([
            'text' => \Lang::get('layerok.tgmall::lang.telegram.buttons.in_menu_main'),
            'callback_data' => json_encode([
                'start'
            ])
        ])->nextRow();
    }
}
