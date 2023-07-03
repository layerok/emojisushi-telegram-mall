<?php

namespace Layerok\Tgmall\Features\Cart;

use Layerok\TgMall\Classes\Keyboards\InlineKeyboard;
use Layerok\TgMall\Classes\Traits\CallbackData;
use Layerok\TgMall\Classes\Traits\Lang;
use OFFLINE\Mall\Classes\Utils\Money;
use OFFLINE\Mall\Models\Currency;

class CartProductKeyboard extends InlineKeyboard
{
    use Lang;
    use CallbackData;

    public function build(): void
    {
        $cartProduct = $this->vars['cartProduct'];
        $money = app()->make(Money::class);

        $this
            ->append([
                'text' => self::lang('buttons.minus'),
                'callback_data' => self::prepareCallbackData(
                    'cart',
                    [
                        'type' => 'update',
                        'id' => $cartProduct->id,
                        'qty' => -1
                    ]
                ),
            ])
            ->append([
                'text' => $cartProduct->quantity,
                'callback_data' => self::prepareCallbackData('noop')
            ])
            ->append([
                'text' => self::lang('buttons.plus'),
                'callback_data' => self::prepareCallbackData(
                    'cart',
                    [
                        'type' => 'update',
                        'id' => $cartProduct->id,
                        'qty' => 1
                    ]
                )
            ])
            ->append([
                'text' => self::lang('buttons.del'),
                'callback_data' => self::prepareCallbackData(
                    'cart',
                    [
                        'type' => 'remove',
                        'id' => $cartProduct->id,
                    ]
                ),
            ])
            ->nextRow()
            ->append([
                'text' => self::lang('buttons.price') . ': ' . $money->format(
                        $cartProduct->price()->price * $cartProduct->quantity,
                        null,
                        Currency::$defaultCurrency
                    ),
                'callback_data' => self::prepareCallbackData('noop')
            ]);

    }

}

