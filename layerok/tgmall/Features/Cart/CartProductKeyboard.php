<?php

namespace Layerok\TgMall\Features\Cart;

use Layerok\TgMall\Objects\CartProduct;
use Telegram\Bot\Keyboard\Keyboard;

class CartProductKeyboard
{
    public function __construct(public CartProduct $cartProduct)
    {

    }

    public function getKeyboard(): Keyboard
    {
        return (new Keyboard())->inline()->row([
            Keyboard::inlineButton([
                'text' => \Lang::get('layerok.tgmall::lang.telegram.buttons.minus'),
                'callback_data' => json_encode([
                    'cart',
                    [
                        'type' => 'update',
                        'id' => $this->cartProduct->id,
                        'qty' => -1
                    ]
                ]),
            ]),
            Keyboard::inlineButton([
                'text' => $this->cartProduct->quantity,
                'callback_data' => json_encode(['noop', []])
            ]),
            Keyboard::inlineButton([
                'text' => \Lang::get('layerok.tgmall::lang.telegram.buttons.plus'),
                'callback_data' => json_encode([
                    'cart',
                    [
                        'type' => 'update',
                        'id' => $this->cartProduct->id,
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
                        'id' => $this->cartProduct->id,
                    ]
                ]),
            ])
        ])->row([])->row([
            Keyboard::inlineButton([
                'text' => sprintf(
                    '%s: %s ₴',
                    \Lang::get('layerok.tgmall::lang.telegram.buttons.price'),
                    (number_format($this->cartProduct->price['UAH'] * $this->cartProduct->quantity / 100, 0))
                ),
                'callback_data' => json_encode(['noop', []])
            ])
        ]);
    }

}

