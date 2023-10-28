<?php

namespace Layerok\TgMall\Features\Checkout\Messages;

use Layerok\TgMall\Classes\Messages\AbstractMessageHandler;
use Layerok\TgMall\Features\Checkout\Handlers\ListDeliveryMethodsHandler;


class OrderPrepareChangeMessageHandler extends AbstractMessageHandler
{
    public function handle()
    {
        $this->state->setStateValue('order_info.change', $this->text);

        $handler = new ListDeliveryMethodsHandler($this->getUser(), $this->api);
        $handler->make($this->update, []);

        $this->state->setStateValue('message_handler', null);
    }
}


