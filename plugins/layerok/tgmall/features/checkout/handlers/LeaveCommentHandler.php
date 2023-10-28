<?php

namespace Layerok\TgMall\Features\Checkout\Handlers;

use Layerok\TgMall\Classes\Callbacks\Handler;
use Layerok\TgMall\Features\Checkout\Messages\OrderCommentHandler;

class LeaveCommentHandler extends Handler
{

    protected string $name = "leave_comment";

    public function run()
    {
        $this->replyWithMessage([
            'text' => 'Комментарий к заказу',
        ]);
        $this
            ->getUser()->state
            ->setStateValue('message_handler',OrderCommentHandler::class);
    }
}
