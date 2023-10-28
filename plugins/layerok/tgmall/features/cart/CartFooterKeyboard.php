<?php namespace Layerok\Tgmall\Features\Cart;

use Layerok\TgMall\Classes\Keyboards\InlineKeyboard;
use Layerok\TgMall\Objects\Cart;

class CartFooterKeyboard extends InlineKeyboard
{

    public function build(): void
    {
        /** @var Cart $cart */
        $cart = $this->vars['cart'];

        if (count($cart->data) !== 0) {
            $total = \Lang::get('layerok.tgmall::lang.telegram.texts.all_amount_order', [
                    'price' => $cart->total
                ]
            );

            $this->append([
                'text' => $total,
                'callback_data' => json_encode(['noop', []])
            ])->nextRow()->append([
                'text' => \Lang::get('layerok.tgmall::lang.telegram.buttons.take_order'),
                'callback_data' => json_encode(['checkout', []])
            ])->nextRow();
        }

        $this->append([
            'text' => \Lang::get('layerok.tgmall::lang.telegram.buttons.to_categories'),
            'callback_data' => json_encode(['category_items', []])
        ])->nextRow()->append([
            'text' => \Lang::get('layerok.tgmall::lang.telegram.buttons.in_menu_main'),
            'callback_data' => json_encode(['start', []])
        ]);

    }

}
