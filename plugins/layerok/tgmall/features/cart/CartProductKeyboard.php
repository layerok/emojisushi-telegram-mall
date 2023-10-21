<?php

namespace Layerok\Tgmall\Features\Cart;

use Layerok\TgMall\Classes\Keyboards\InlineKeyboard;

class CartProductKeyboard extends InlineKeyboard
{
    public function build(): void
    {
        $cartProduct = $this->vars['cartProduct'];


        $this
            ->append([
                'text' => \Lang::get('layerok.tgmall::lang.telegram.buttons.minus'),
                'callback_data' => json_encode([
                    'cart',
                    [
                        'type' => 'update',
                        'id' => $cartProduct['id'],
                        'qty' => -1
                    ]
                ]),
            ])
            ->append([
                'text' => $cartProduct['quantity'],
                'callback_data' => json_encode(['noop', []])
            ])
            ->append([
                'text' => \Lang::get('layerok.tgmall::lang.telegram.buttons.plus'),
                'callback_data' => json_encode([
                    'cart',
                    [
                        'type' => 'update',
                        'id' => $cartProduct['id'],
                        'qty' => 1
                    ]
                ])
            ])
            ->append([
                'text' => \Lang::get('layerok.tgmall::lang.telegram.buttons.del'),
                'callback_data' => json_encode([
                    'cart',
                    [
                        'type' => 'remove',
                        'id' => $cartProduct['id'],
                    ]
                ]),
            ])
            ->nextRow()
            ->append([
                'text' => sprintf(
                    '%s: %s ₴',
                    \Lang::get('layerok.tgmall::lang.telegram.buttons.price'),
                    (number_format($cartProduct['price']['UAH'] * $cartProduct['quantity'] / 100, 0))
                ),
                'callback_data' => json_encode(['noop', []])
            ]);

    }

}

