<?php

namespace App\Features\Checkout\Handlers;

use App\Classes\Callbacks\Handler;
use App\Features\Checkout\Messages\OrderCommentHandler;
use Illuminate\Support\Facades\Lang;

class LeaveCommentHandler extends Handler
{

    protected string $name = "leave_comment";

    public function run()
    {
        $this->replyWithMessage([
            'text' => Lang::get('lang.telegram.texts.order_comment'),
        ]);

        $this->user->state->message_handler = OrderCommentHandler::class;
        $this->user->save();
    }
}
