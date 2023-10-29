<?php

namespace Layerok\TgMall\Features\Checkout\Keyboards;

use Layerok\TgMall\Classes\Keyboards\InlineKeyboard;
use Layerok\TgMall\Facades\EmojisushiApi;
use Layerok\TgMall\Objects\ShipmentMethod;

class DeliveryMethodsKeyboard extends InlineKeyboard
{
    public function build(): void
    {
        collect(EmojisushiApi::getShippingMethods()->data)->each(function (ShipmentMethod $method) {
            $this->append([
                'text' => $method->name,
                'callback_data' => json_encode([
                    'chose_delivery_method',
                    ['id' => $method->id]
                ])
            ])->nextRow();
        });
    }
}
