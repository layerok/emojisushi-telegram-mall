<?php

namespace App\Features\Checkout\Handlers;

use App\Classes\Callbacks\Handler;
use App\Features\Checkout\Messages\OrderPhoneMessageHandler;

class EnterPhoneHandler extends Handler
{
    protected string $name = "enter_phone";

    public function run()
    {
        $this->replyWithMessage([
            'chat_id' => $this->user->chat_id,
            'text' => \Lang::get('lang.telegram.texts.type_your_phone'),
        ]);

        $this->user->state->message_handler = OrderPhoneMessageHandler::class;
        $this->user->save();

    }
}
