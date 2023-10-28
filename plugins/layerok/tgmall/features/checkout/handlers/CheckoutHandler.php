<?php

namespace Layerok\TgMall\Features\Checkout\Handlers;

use Layerok\TgMall\Classes\Callbacks\Handler;
use Layerok\TgMall\Classes\StateKeys;
use Layerok\TgMall\Features\Checkout\Messages\OrderNameHandler;

class CheckoutHandler extends Handler
{
    protected string $name = "checkout";

    public function run()
    {
        // Очищаем инфу о заказе при начале оформления заказа
        $this->getUser()->state->setStateValue(StateKeys::ORDER_PHONE, null);
        $this->getUser()->state->setStateValue(StateKeys::ORDER_DELIVERY_METHOD_ID, null);
        $this->getUser()->state->setStateValue(StateKeys::ORDER_PAYMENT_METHOD_ID, null);
        $this->getUser()->state->setStateValue(StateKeys::ORDER_ADDRESS, null);
        $this->getUser()->state->setStateValue(StateKeys::ORDER_COMMENT, null);
        $this->getUser()->state->setStateValue(StateKeys::ORDER_FIRST_NAME, null);
        $this->getUser()->state->setStateValue(StateKeys::ORDER_STICKS_COUNT, null);
        $this->getUser()->state->setStateValue(StateKeys::ORDER_CHANGE, null);


        $this->replyWithMessage([
            'text' => \Lang::get('layerok.tgmall::lang.telegram.texts.type_your_name')
        ]);

        $this->getUser()->state->setStateValue(StateKeys::MESSAGE_HANDLER, OrderNameHandler::class);
    }
}
