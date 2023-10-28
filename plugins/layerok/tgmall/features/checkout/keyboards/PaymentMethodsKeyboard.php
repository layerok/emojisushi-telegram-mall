<?php

namespace Layerok\TgMall\Features\Checkout\Keyboards;

use Layerok\TgMall\Classes\Keyboards\InlineKeyboard;
use Layerok\TgMall\Facades\EmojisushiApi;
use Layerok\TgMall\Objects\PaymentMethod;

class PaymentMethodsKeyboard extends InlineKeyboard
{
    public function build(): void
    {
        collect(EmojisushiApi::getPaymentMethods()->data)->each(function(PaymentMethod $method) {
            $this->append([
                'text' => $method['name'],
                'callback_data' => json_encode([
                    'chose_payment_method',
                    ['id' => $method['id']]
                ])
            ])->nextRow();
        });

    }
}
