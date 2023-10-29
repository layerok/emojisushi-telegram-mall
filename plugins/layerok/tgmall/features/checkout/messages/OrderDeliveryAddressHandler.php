<?php

namespace Layerok\TgMall\Features\Checkout\Messages;

use Layerok\TgMall\Classes\Messages\AbstractMessageHandler;
use Layerok\TgMall\Features\Checkout\Keyboards\SticksKeyboard;

class OrderDeliveryAddressHandler extends AbstractMessageHandler
{
    public function handle()
    {
        $appState = $this->state->state;
        $appState->order->address = $this->text;
        $this->state->state = $appState;
        $this->state->save();

        $k = new SticksKeyboard();
        // был выбран самовывоз
        $this->replyWithMessage([
            'text' => \Lang::get('layerok.tgmall::lang.telegram.texts.add_sticks_question'),
            'reply_markup' => $k->getKeyboard()
        ]);

        $appState = $this->state->state;
        $appState->message_handler = null;
        $this->state->state = $appState;
        $this->state->save();
    }
}
