<?php namespace App\Features\Cart;

use App\Objects\Cart;
use Illuminate\Support\Facades\Lang;
use Telegram\Bot\Keyboard\Keyboard;

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
                    'text' => Lang::get('lang.telegram.texts.all_amount_order', [
                            'price' => $this->cart->total
                        ]
                    ),
                    'callback_data' => json_encode(['noop', []])
                ])
            ])->row([])->row([
                Keyboard::inlineButton([
                    'text' => Lang::get('lang.telegram.buttons.take_order'),
                    'callback_data' => json_encode(['checkout', []])
                ])
            ])->row([]);
        }

        return $keyboard->row([
            Keyboard::inlineButton([
                'text' => Lang::get('lang.telegram.buttons.to_categories'),
                'callback_data' => json_encode(['category_items', []])
            ])
        ])->row([])->row([
            Keyboard::inlineButton([
                'text' => \Lang::get('lang.telegram.buttons.in_menu_main'),
                'callback_data' => json_encode(['start', []])
            ])
        ]);

    }

}
