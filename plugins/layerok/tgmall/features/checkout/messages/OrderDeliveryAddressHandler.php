<?php

namespace Layerok\TgMall\Features\Checkout\Messages;

use Layerok\TgMall\Classes\Messages\AbstractMessageHandler;
use Layerok\TgMall\Classes\StateKeys;
use Layerok\TgMall\Features\Checkout\Keyboards\SticksKeyboard;

class OrderDeliveryAddressHandler extends AbstractMessageHandler
{
    public function handle()
    {
        $this->state->setStateValue(StateKeys::ORDER_ADDRESS, $this->text);

        $k = new SticksKeyboard();
        // был выбран самовывоз
        $this->replyWithMessage([
            'text' => \Lang::get('layerok.tgmall::lang.telegram.texts.add_sticks_question'),
            'reply_markup' => $k->getKeyboard()
        ]);

        $this->state->setStateValue(StateKeys::MESSAGE_HANDLER, null);
    }
}
