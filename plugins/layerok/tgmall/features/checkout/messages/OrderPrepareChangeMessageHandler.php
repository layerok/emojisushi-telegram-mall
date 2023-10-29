<?php

namespace Layerok\TgMall\Features\Checkout\Messages;

use Layerok\TgMall\Classes\Messages\AbstractMessageHandler;
use Layerok\TgMall\Features\Checkout\Handlers\ListDeliveryMethodsHandler;


class OrderPrepareChangeMessageHandler extends AbstractMessageHandler
{
    public function handle()
    {
        $this->user->state->order->change = $this->text;
        $this->user->save();

        $handler = new ListDeliveryMethodsHandler($this->getUser(), $this->api);
        $handler->make($this->update, []);

        $this->user->state->message_handler = null;
        $this->user->save();
    }
}


