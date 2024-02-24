<?php

namespace App\Features\Checkout\Handlers;
use App\Classes\Callbacks\Handler;
use App\Features\Checkout\Messages\OrderPrepareChangeMessageHandler;


class PreparePaymentChangeHandler extends Handler
{
    protected string $name = "prepare_payment_change";

    public function run()
    {
        $this->replyWithMessage([
            'text' => \Lang::get('lang.telegram.texts.payment_change'),
        ]);

        $this->user->state->message_handler = OrderPrepareChangeMessageHandler::class;
        $this->user->save();
    }
}



