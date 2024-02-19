<?php

namespace Layerok\TgMall\Features\Checkout\Handlers;

use Layerok\TgMall\Classes\Callbacks\Handler;
use Layerok\TgMall\Facades\Hydrator;
use Layerok\TgMall\Features\Checkout\Messages\OrderNameHandler;
use Layerok\TgMall\Objects2\Order;

class CheckoutHandler extends Handler
{
    protected string $name = "checkout";

    public function run()
    {
        // Очищаем инфу о заказе при начале оформления заказа
        $this->user->state->order = Hydrator::hydrate(Order::class, []);
        $this->user->save();

        $this->replyWithMessage([
            'text' => \Lang::get('layerok.tgmall::lang.telegram.texts.type_your_name')
        ]);

        $this->user->state->message_handler = OrderNameHandler::class;
        $this->user->save();
    }
}
