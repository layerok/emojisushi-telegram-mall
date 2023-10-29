<?php

namespace Layerok\TgMall\Features\Checkout\Handlers;

use Layerok\TgMall\Classes\Callbacks\Handler;
use Layerok\TgMall\Features\Checkout\Keyboards\SticksCounterKeyboard;


class YesSticksHandler extends Handler
{
    protected string $name = "yes_sticks";

    public function run() {

        $initialCount = 1;

        $appState = $this->user->state->state;
        $appState->order->sticks_count = $initialCount;
        $this->user->state->state = $appState;
        $this->user->state->save();


        $k = new SticksCounterKeyboard([
            'count' => $initialCount
        ]);

        $this->replyWithMessage([
            'text' => \Lang::get('layerok.tgmall::lang.telegram.texts.add_sticks'),
            'reply_markup' => $k->getKeyboard(),
        ]);
    }
}
