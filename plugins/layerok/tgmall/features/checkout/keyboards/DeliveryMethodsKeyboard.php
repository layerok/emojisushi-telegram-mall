<?php
namespace Layerok\TgMall\Features\Checkout\Keyboards;

use Layerok\TgMall\Classes\Keyboards\InlineKeyboard;
use Layerok\TgMall\Classes\Traits\CallbackData;
use Layerok\TgMall\Facades\EmojisushiApi;

class DeliveryMethodsKeyboard extends InlineKeyboard
{
    use CallbackData;

    public function build(): void
    {
        $methods = EmojisushiApi::getShippingMethods()['data'];

        array_map(function ($method) {
            $this->append([
                'text' => $method['name'],
                'callback_data' => self::prepareCallbackData(
                    'chose_delivery_method',
                    ['id' => $method['id']]
                )
            ])->nextRow();
        }, $methods);
    }
}
