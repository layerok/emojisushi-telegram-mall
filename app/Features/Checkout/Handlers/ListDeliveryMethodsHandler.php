<?php

namespace App\Features\Checkout\Handlers;

use App\Classes\Callbacks\Handler;
use App\Facades\EmojisushiApi;
use App\Objects\ShipmentMethod;
use Telegram\Bot\Keyboard\Keyboard;

class ListDeliveryMethodsHandler extends Handler
{
    protected string $name = "list_delivery_methods";

    public function run()
    {
        $keyboard = (new Keyboard())->inline();
        collect(EmojisushiApi::getShippingMethods()->data)->each(function(ShipmentMethod $method) use($keyboard) {
            $keyboard->row([
                Keyboard::inlineButton([
                    'text' => $method->name,
                    'callback_data' => json_encode([
                        'chose_delivery_method',
                        ['id' => $method->id]
                    ])
                ])
            ])->row([]);
        });

        $this->replyWithMessage([
            'chat_id' => $this->user->chat_id,
            'text' => \Lang::get('lang.telegram.texts.chose_delivery_method'),
            'reply_markup' => $keyboard,
        ]);
    }
}
