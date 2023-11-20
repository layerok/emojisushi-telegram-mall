<?php

namespace Layerok\Tgmall\Features\Cart;

use Layerok\TgMall\Objects\CartProduct;
use Telegram\Bot\Keyboard\Keyboard;

class CartProductKeyboard
{
    public array $vars;

    public function __construct($vars = [])
    {
        $this->vars = $vars;
    }

    public function getKeyboard(): Keyboard
    {
        /**
         * @var CartProduct $cartProduct
         */
        $cartProduct = $this->vars['cartProduct'];

        return (new Keyboard())->inline()->row([
            Keyboard::inlineButton([
                'text' => \Lang::get('layerok.tgmall::lang.telegram.buttons.minus'),
                'callback_data' => json_encode([
                    'cart',
                    [
                        'type' => 'update',
                        'id' => $cartProduct->id,
                        'qty' => -1
                    ]
                ]),
            ]),
            Keyboard::inlineButton([
                'text' => $cartProduct->quantity,
                'callback_data' => json_encode(['noop', []])
            ]),
            Keyboard::inlineButton([
                'text' => \Lang::get('layerok.tgmall::lang.telegram.buttons.plus'),
                'callback_data' => json_encode([
                    'cart',
                    [
                        'type' => 'update',
                        'id' => $cartProduct->id,
                        'qty' => 1
                    ]
                ])
            ]),
            Keyboard::inlineButton([
                'text' => \Lang::get('layerok.tgmall::lang.telegram.buttons.del'),
                'callback_data' => json_encode([
                    'cart',
                    [
                        'type' => 'remove',
                        'id' => $cartProduct->id,
                    ]
                ]),
            ])
        ])->row([])->row([
            Keyboard::inlineButton([
                'text' => sprintf(
                    '%s: %s ₴',
                    \Lang::get('layerok.tgmall::lang.telegram.buttons.price'),
                    (number_format($cartProduct->price['UAH'] * $cartProduct->quantity / 100, 0))
                ),
                'callback_data' => json_encode(['noop', []])
            ])
        ]);
    }

}

