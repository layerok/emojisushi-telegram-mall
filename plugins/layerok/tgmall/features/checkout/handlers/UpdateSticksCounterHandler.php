<?php

namespace Layerok\TgMall\Features\Checkout\Handlers;

use Layerok\TgMall\Classes\Callbacks\Handler;
use Layerok\TgMall\Classes\StateKeys;
use Layerok\TgMall\Features\Checkout\Keyboards\SticksCounterKeyboard;


class UpdateSticksCounterHandler extends Handler
{
    protected string $name = "update_sticks_counter";

    public function run()
    {
        $count = $this->arguments[0];

        if($count < 0) {
            return;
        }

        $this->getUser()->state->setStateValue(StateKeys::ORDER_STICKS_COUNT, $count);

        $k = new SticksCounterKeyboard([
            'count' => $count
        ]);

        $this->api->editMessageReplyMarkup([
            'message_id' => $this->getUpdate()->getMessage()->message_id,
            'chat_id' => $this->getUpdate()->getChat()->id,
            'reply_markup' => $k->getKeyboard()
        ]);
    }
}
