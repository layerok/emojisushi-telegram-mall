<?php

namespace Layerok\TgMall\Features\Checkout\Messages;

use Layerok\TgMall\Classes\Messages\AbstractMessageHandler;
use Layerok\TgMall\Features\Checkout\Handlers\PreConfirmOrderHandler;

class OrderCommentHandler extends AbstractMessageHandler
{
    public function handle()
    {
        $this->user->state->order->comment = $this->text;
        $this->user->save();

        $handler = new PreConfirmOrderHandler($this->getUser(), $this->api);
        $handler->make($this->update, []);
    }
}
