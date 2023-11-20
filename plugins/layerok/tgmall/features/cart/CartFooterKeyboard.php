<?php namespace Layerok\Tgmall\Features\Cart;

use Layerok\TgMall\Objects\Cart;
use Telegram\Bot\Keyboard\Keyboard;

class CartFooterKeyboard
{
    public array $vars;

    public function __construct($vars = [])
    {
        $this->vars = $vars;
    }

    public function getKeyboard(): Keyboard
    {
        /** @var Cart $cart */
        $cart = $this->vars['cart'];

        $keyboard = (new Keyboard())->inline();

        if (count($cart->data) !== 0) {

            $keyboard->row([
                Keyboard::inlineButton([
                    'text' => \Lang::get('layerok.tgmall::lang.telegram.texts.all_amount_order', [
                            'price' => $cart->total
                        ]
                    ),
                    'callback_data' => json_encode(['noop', []])
                ])
            ])->row([])->row([
                Keyboard::inlineButton([
                    'text' => \Lang::get('layerok.tgmall::lang.telegram.buttons.take_order'),
                    'callback_data' => json_encode(['checkout', []])
                ])
            ])->row([]);
        }

        return $keyboard->row([
            Keyboard::inlineButton([
                'text' => \Lang::get('layerok.tgmall::lang.telegram.buttons.to_categories'),
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
