<?php
namespace Layerok\TgMall\Features\Checkout\Keyboards;

use Layerok\TgMall\Classes\Keyboards\InlineKeyboard;
use Layerok\TgMall\Facades\EmojisushiApi;

class DeliveryMethodsKeyboard extends InlineKeyboard
{
    public function build(): void
    {
        $methods = EmojisushiApi::getShippingMethods()['data'];

        array_map(function ($method) {
            $this->append([
                'text' => $method['name'],
                'callback_data' => json_encode([
                    'chose_delivery_method',
                    ['id' => $method['id']]
                ])
            ])->nextRow();
        }, $methods);
    }
}
