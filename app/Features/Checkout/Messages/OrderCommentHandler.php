<?php

namespace App\Features\Checkout\Messages;

use App\Classes\Messages\AbstractMessageHandler;
use App\Features\Checkout\Handlers\PreConfirmOrderHandler;

class OrderCommentHandler extends AbstractMessageHandler
{
    public function handle()
    {
        $this->user->state->order->comment = $this->text;
        $this->user->save();

        $handler = new PreConfirmOrderHandler($this->user, $this->api);
        $handler->make($this->update, []);
    }
}
