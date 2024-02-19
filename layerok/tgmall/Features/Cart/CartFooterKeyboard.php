<?php namespace Layerok\TgMall\Features\Cart;

use Layerok\TgMall\Objects\Cart;
use Telegram\Bot\Keyboard\Keyboard;
use Illuminate\Support\Facades\Lang;

class CartFooterKeyboard
{
    public function __construct(public Cart $cart) {

    }

    public function getKeyboard(): Keyboard
    {
        $keyboard = (new Keyboard())->inline();

        if (count($this->cart->data) !== 0) {

            $keyboard->row([
                Keyboard::inlineButton([
                    'text' => Lang::get('layerok.tgmall::lang.telegram.texts.all_amount_order', [
                            'price' => $this->cart->total
                        ]
                    ),
                    'callback_data' => json_encode(['noop', []])
                ])
            ])->row([])->row([
                Keyboard::inlineButton([
                    'text' => Lang::get('layerok.tgmall::lang.telegram.buttons.take_order'),
                    'callback_data' => json_encode(['checkout', []])
                ])
            ])->row([]);
        }

        return $keyboard->row([
            Keyboard::inlineButton([
                'text' => Lang::get('layerok.tgmall::lang.telegram.buttons.to_categories'),
                'callback_data' => json_encode(['category_items', []])
            ])
        ])->row([])->row([
            Keyboard::inlineButton([
                'text' => \Lang::get('layerok.tgmall::lang.telegram.buttons.in_menu_main'),
                'callback_data' => json_encode(['start', []])
            ])
        ]);

    }

}
