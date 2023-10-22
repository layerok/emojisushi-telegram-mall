<?php

namespace Layerok\TgMall\Features\Checkout\Messages;

use Layerok\TgMall\Classes\Messages\AbstractMessageHandler;
use Layerok\TgMall\Features\Checkout\Handlers\PreConfirmOrderHandler;

class OrderCommentHandler extends AbstractMessageHandler
{
    public function handle()
    {
        $this->state->setOrderInfoComment($this->text);

        $handler = new PreConfirmOrderHandler();
        $handler->setTelegramUser($this->getTelegramUser());
        $handler->setTelegram($this->api);
        $handler->make($this->api, $this->update, []);
    }
}
