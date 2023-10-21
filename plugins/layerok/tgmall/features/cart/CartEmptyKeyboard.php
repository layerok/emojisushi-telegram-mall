<?php namespace Layerok\Tgmall\Features\Cart;

use Layerok\TgMall\Classes\Keyboards\InlineKeyboard;

class CartEmptyKeyboard extends InlineKeyboard
{
    public function build(): void
    {
        $this
            ->append([
            'text' => \Lang::get('layerok.tgmall::lang.telegram.buttons.to_categories'),
            'callback_data' => json_encode(['category_items', []])
        ])
            ->nextRow()
            ->append([
            'text' => \Lang::get('layerok.tgmall::lang.telegram.buttons.in_menu_main'),
            'callback_data' => json_encode(['start', []])
        ]);
    }

}
