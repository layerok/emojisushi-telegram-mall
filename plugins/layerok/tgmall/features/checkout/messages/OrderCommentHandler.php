<?php

namespace Layerok\TgMall\Features\Checkout\Messages;

use Layerok\TgMall\Classes\Messages\AbstractMessageHandler;
use Layerok\TgMall\Classes\StateKeys;
use Layerok\TgMall\Features\Checkout\Handlers\PreConfirmOrderHandler;

class OrderCommentHandler extends AbstractMessageHandler
{
    public function handle()
    {
        $this->state->setStateValue(StateKeys::ORDER_COMMENT, $this->text);

        $handler = new PreConfirmOrderHandler($this->getUser(), $this->api);
        $handler->make($this->update, []);
    }
}
