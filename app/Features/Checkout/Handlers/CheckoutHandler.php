<?php

namespace App\Features\Checkout\Handlers;

use App\Classes\Callbacks\Handler;
use App\Facades\Hydrator;
use App\Features\Checkout\Messages\OrderNameHandler;
use App\Objects2\Order;

class CheckoutHandler extends Handler
{
    protected string $name = "checkout";

    public function run()
    {
        // Очищаем инфу о заказе при начале оформления заказа
        $this->user->state->order = Hydrator::hydrate(Order::class, []);
        $this->user->save();

        $this->replyWithMessage([
            'text' => \Lang::get('lang.telegram.texts.type_your_name')
        ]);

        $this->user->state->message_handler = OrderNameHandler::class;
        $this->user->save();
    }
}
