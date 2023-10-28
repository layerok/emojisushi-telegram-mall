<?php

namespace Layerok\TgMall\Features\Checkout\Handlers;
use Layerok\TgMall\Classes\Callbacks\Handler;
use Layerok\TgMall\Features\Checkout\Messages\OrderPrepareChangeMessageHandler;


class PreparePaymentChangeHandler extends Handler
{
    protected string $name = "prepare_payment_change";

    public function run()
    {
        $this->replyWithMessage([
            'text' => \Lang::get('layerok.tgmall::lang.telegram.texts.payment_change'),
        ]);
        $this->getUser()->state->setStateValue('message_handler', OrderPrepareChangeMessageHandler::class);
    }
}



