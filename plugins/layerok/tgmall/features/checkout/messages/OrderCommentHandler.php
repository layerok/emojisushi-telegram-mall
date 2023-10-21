<?php

namespace Layerok\TgMall\Features\Checkout\Messages;

use Layerok\TgMall\Classes\Callbacks\CallbackQueryBus;
use Layerok\TgMall\Classes\Messages\AbstractMessageHandler;

class OrderCommentHandler extends AbstractMessageHandler
{
    public function handle()
    {
        $this->state->setOrderInfoComment($this->text);

        CallbackQueryBus::instance()->make(
            'pre_confirm_order',
            [],
            $this->getTelegramUser(),
            $this->update,
            $this->api
        );
    }
}
