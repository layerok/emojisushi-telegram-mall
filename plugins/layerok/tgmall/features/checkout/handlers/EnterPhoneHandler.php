<?php

namespace Layerok\TgMall\Features\Checkout\Handlers;

use Layerok\TgMall\Classes\Callbacks\Handler;
use Layerok\TgMall\Features\Checkout\Messages\OrderPhoneMessageHandler;

class EnterPhoneHandler extends Handler
{
    protected string $name = "enter_phone";

    public function run()
    {
        $this->replyWithMessage([
            'chat_id' => $this->user->chat_id,
            'text' => \Lang::get('layerok.tgmall::lang.telegram.texts.type_your_phone'),
        ]);

        $this->getUser()->state->setMessageHandler(OrderPhoneMessageHandler::class);
    }
}
