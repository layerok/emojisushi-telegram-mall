<?php

namespace Layerok\TgMall\Features\Checkout\Messages;

use Layerok\TgMall\Classes\Messages\AbstractMessageHandler;
use Layerok\TgMall\Features\Checkout\Keyboard\WishToAddSticksKeyboard;

class OrderDeliveryAddressHandler extends AbstractMessageHandler
{
    public function handle()
    {
        $this->user->state->order->address = $this->text;
        $this->user->save();

        $this->replyWithMessage([
            'text' => \Lang::get('layerok.tgmall::lang.telegram.texts.add_sticks_question'),
            'reply_markup' => (new WishToAddSticksKeyboard())->getKeyboard()
        ]);

        $this->user->state->message_handler = null;
        $this->user->save();
    }
}
