<?php

namespace Layerok\TgMall\Features\Checkout\Handlers;

use Layerok\TgMall\Classes\Callbacks\Handler;
use Layerok\TgMall\Features\Checkout\Keyboard\WishToAddSticksKeyboard;

class ChoseOdesaSpotHandler extends Handler
{
    protected string $name = "chose_odesa_spot";

    public function run()
    {
        $this->user->state->spot_id = $this->arguments[0];
        $this->user->save();
        $this->replyWithMessage([
            'text' => \Lang::get('layerok.tgmall::lang.telegram.texts.add_sticks_question'),
            'reply_markup' => (new WishToAddSticksKeyboard())->getKeyboard()
        ]);
    }
}
