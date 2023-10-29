<?php

namespace Layerok\TgMall\Features\Checkout\Messages;

use Layerok\TgMall\Classes\Messages\AbstractMessageHandler;
use Layerok\TgMall\Features\Checkout\Handlers\ListDeliveryMethodsHandler;


class OrderPrepareChangeMessageHandler extends AbstractMessageHandler
{
    public function handle()
    {
        $appState = $this->state->state;
        $appState->order->change = $this->text;
        $this->state->state = $appState;
        $this->state->save();

        $handler = new ListDeliveryMethodsHandler($this->getUser(), $this->api);
        $handler->make($this->update, []);

        $appState = $this->state->state;
        $appState->message_handler = null;
        $this->state->state = $appState;
        $this->state->save();

    }
}


