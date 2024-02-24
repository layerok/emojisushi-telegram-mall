<?php

namespace App\Features\Checkout\Handlers;

use App\Classes\Callbacks\Handler;
use App\Classes\Keyboards\YesNoKeyboard;


class WishToLeaveCommentHandler extends Handler
{
    protected string $name = "wish_to_leave_comment";

    public function run()
    {
        $this->replyWithMessage([
            'chat_id' => $this->user->chat_id,
            'text' => \Lang::get('lang.telegram.texts.leave_comment_question'),
            'reply_markup' => (new YesNoKeyboard(
                yes: ['handler' => 'leave_comment'],
                no: ['handler' => 'pre_confirm_order']
            ))->getKeyboard()
        ]);
    }
}
