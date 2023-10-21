<?php

namespace Layerok\TgMall\Features\Checkout\Handlers;

use Layerok\TgMall\Classes\Callbacks\Handler;
use Event;
use Layerok\TgMall\Facades\EmojisushiApi;
use Layerok\TgMall\Features\Checkout\Keyboards\OrderConfirmedKeyboard;


class ConfirmOrderHandler extends Handler
{
    public string $name = "confirm_order";

    public function run()
    {
        $result = Event::fire('tgmall.order.confirmed', [$this], true);

        if($result) {

            $k = new OrderConfirmedKeyboard();

            EmojisushiApi::clearCart();


            $this->sendMessage([
                'text' => \Lang::get('layerok.tgmall::lang.telegram.texts.thank_you'),
                'reply_markup' => $k->getKeyboard()
            ]);
        }
    }


}
