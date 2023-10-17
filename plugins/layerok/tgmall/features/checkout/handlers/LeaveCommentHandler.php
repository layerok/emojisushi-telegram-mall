<?php

namespace Layerok\TgMall\Features\Checkout\Handlers;

use Layerok\TgMall\Classes\Callbacks\Handler;
use Layerok\TgMall\Features\Checkout\Messages\OrderCommentHandler;

class LeaveCommentHandler extends Handler
{

    protected string $name = "leave_comment";

    public function run()
    {
        $this->sendMessage([
            'text' => 'Комментарий к заказу',
        ]);
        $this
            ->getState()
            ->setMessageHandler(OrderCommentHandler::class);
    }
}
