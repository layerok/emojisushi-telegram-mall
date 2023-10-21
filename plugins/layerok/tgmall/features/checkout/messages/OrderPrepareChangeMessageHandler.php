<?php

namespace Layerok\TgMall\Features\Checkout\Messages;

use Layerok\TgMall\Classes\Callbacks\CallbackQueryBus;
use Layerok\TgMall\Classes\Messages\AbstractMessageHandler;


class OrderPrepareChangeMessageHandler extends AbstractMessageHandler
{
    public function handle()
    {
        $this->state->setOrderInfoChange($this->text);

        CallbackQueryBus::instance()->make(
            'list_delivery_methods',
            [], $this->getTelegramUser(),
            $this->update,
            $this->api
        );

        $this->state->setMessageHandler(null);
    }
}


