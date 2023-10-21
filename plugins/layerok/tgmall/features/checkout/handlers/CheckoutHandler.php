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
        $this->getState()->setOrderInfo([]);

        $this->replyWithMessage([
            'text' => \Lang::get('layerok.tgmall::lang.telegram.texts.type_your_name')
        ]);

        $this->getState()->setMessageHandler(OrderNameHandler::class);
    }
}
