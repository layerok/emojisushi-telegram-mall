<?php

namespace Layerok\TgMall\Features\Checkout\Handlers;

use Layerok\TgMall\Classes\Callbacks\Handler;
use Layerok\TgMall\Classes\Keyboards\CounterKeyboard;


class YesSticksHandler extends Handler
{
    protected string $name = "add_sticks";

    public function run() {

        $initialCount = 1;
        $this->user->state->order->sticks_count = $initialCount;
        $this->user->save();

        $this->replyWithMessage([
            'text' => \Lang::get('layerok.tgmall::lang.telegram.texts.add_sticks'),
            'reply_markup' => (new CounterKeyboard($initialCount, 'confirm_sticks_count'))->getKeyboard(),
        ]);
    }
}
