<?php

namespace Layerok\TgMall\Features\Checkout\Messages;

use Layerok\TgMall\Classes\Messages\AbstractMessageHandler;
use Layerok\TgMall\Features\Checkout\Handlers\ListDeliveryMethodsHandler;


class OrderPrepareChangeMessageHandler extends AbstractMessageHandler
{
    public function handle()
    {
        $this->state->setOrderInfoChange($this->text);

        $handler = new ListDeliveryMethodsHandler();
        $handler->setTelegramUser($this->getTelegramUser());
        $handler->setTelegram($this->api);
        $handler->make($this->api, $this->update, []);

        $this->state->setMessageHandler(null);
    }
}


