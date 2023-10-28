<?php

namespace Layerok\TgMall\Features\Checkout\Handlers;

use Layerok\TgMall\Classes\Callbacks\Handler;
use Layerok\TgMall\Features\Checkout\Messages\OrderNameHandler;

class CheckoutHandler extends Handler
{
    protected string $name = "checkout";

    public function run()
    {
        // Очищаем инфу о заказе при начале оформления заказа
        $this->getUser()->state->setStateValue('order_info.phone', null);
        $this->getUser()->state->setStateValue('order_info.delivery_method_id', null);
        $this->getUser()->state->setStateValue('order_info.payment_method_id', null);
        $this->getUser()->state->setStateValue('order_info.address', null);
        $this->getUser()->state->setStateValue('order_info.sticks_count', null);
        $this->getUser()->state->setStateValue('order_info.change', null);
        $this->getUser()->state->setStateValue('order_info.comment', null);
        $this->getUser()->state->setStateValue('order_info.first_name', null);


        $this->replyWithMessage([
            'text' => \Lang::get('layerok.tgmall::lang.telegram.texts.type_your_name')
        ]);

        $this->getUser()->state->setStateValue('message_handler', OrderNameHandler::class);
    }
}
