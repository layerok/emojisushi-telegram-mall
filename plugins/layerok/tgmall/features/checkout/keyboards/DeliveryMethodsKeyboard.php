<?php

namespace Layerok\TgMall\Features\Checkout\Keyboards;

use Layerok\TgMall\Facades\EmojisushiApi;
use Layerok\TgMall\Objects\ShipmentMethod;
use Telegram\Bot\Keyboard\Keyboard;

class DeliveryMethodsKeyboard
{
    public array $vars;

    public function __construct($vars = [])
    {
        $this->vars = $vars;
    }

    public function getKeyboard(): Keyboard
    {
        $keyboard = (new Keyboard())->inline();
        collect(EmojisushiApi::getShippingMethods()->data)->each(function (ShipmentMethod $method) use ($keyboard) {
            $keyboard->row([
                Keyboard::inlineButton([
                    'text' => $method->name,
                    'callback_data' => json_encode([
                        'chose_delivery_method',
                        ['id' => $method->id]
                    ])
                ])
            ]);
        });

        return $keyboard;
    }
}
