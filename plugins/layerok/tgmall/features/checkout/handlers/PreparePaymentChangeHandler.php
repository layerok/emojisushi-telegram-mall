<?php

namespace Layerok\TgMall\Features\Checkout\Handlers;
use Layerok\TgMall\Classes\Callbacks\Handler;
use Layerok\TgMall\Features\Checkout\Messages\OrderPrepareChangeMessageHandler;


class PreparePaymentChangeHandler extends Handler
{
    protected string $name = "prepare_payment_change";

    public function run()
    {
        $this->sendMessage([
            'text' => \Lang::get('layerok.tgmall::lang.telegram.texts.payment_change'),
        ]);
        $this->getState()->setMessageHandler(OrderPrepareChangeMessageHandler::class);
    }
}



