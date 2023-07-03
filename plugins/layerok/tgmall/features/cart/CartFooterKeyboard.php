<?php namespace Layerok\Tgmall\Features\Cart;

use Layerok\TgMall\Classes\Keyboards\InlineKeyboard;
use Layerok\TgMall\Classes\Traits\CallbackData;
use Layerok\TgMall\Classes\Traits\Lang;
use OFFLINE\Mall\Classes\Utils\Money;
use OFFLINE\Mall\Models\Currency;

class CartFooterKeyboard extends InlineKeyboard
{
    use Lang;
    use CallbackData;

    public function build(): void
    {
        $cart = $this->vars['cart'];

        if ($cart->products->count() !== 0) {
            $money = app()->make(Money::class);
            $total = self::lang('texts.all_amount_order', [
                'price' => $money->format(
                    $cart->totals()->totalPostTaxes(),
                    null,
                    Currency::$defaultCurrency
                )]
            );

            $this->append([
                'text' => $total,
                'callback_data' => self::prepareCallbackData('noop')
            ])->nextRow()->append([
                'text' => self::lang('buttons.take_order'),
                'callback_data' => self::prepareCallbackData('checkout')
            ])->nextRow();
        }

        $this->append([
            'text' => self::lang('buttons.to_categories'),
            'callback_data' => self::prepareCallbackData('category_items')
        ])->nextRow()->append([
            'text' => self::lang('buttons.in_menu_main'),
            'callback_data' => self::prepareCallbackData('start')
        ]);

    }

}
