<?php

namespace Layerok\TgMall\Features\Checkout\Handlers;

use Layerok\TgMall\Classes\Callbacks\Handler;

class ConfirmSticksCountHandler extends Handler
{
    protected string $name = "confirm_sticks_count";

    public function run()
    {
        $count = $this->arguments[0];

        $this->user->state->order->sticks_count = $count;
        $this->user->save();

        $handler = new WishToLeaveCommentHandler($this->user, $this->api);
        $handler->make($this->update, []);
    }
}
