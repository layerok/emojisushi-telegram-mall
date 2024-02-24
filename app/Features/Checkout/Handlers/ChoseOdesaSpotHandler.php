<?php

namespace App\Features\Checkout\Handlers;

use App\Classes\Callbacks\Handler;
use App\Features\Checkout\Keyboard\WishToAddSticksKeyboard;

class ChoseOdesaSpotHandler extends Handler
{
    protected string $name = "chose_odesa_spot";

    public function run()
    {
        $this->user->state->spot_id = $this->arguments[0];
        $this->user->save();
        $this->replyWithMessage([
            'text' => \Lang::get('lang.telegram.texts.add_sticks_question'),
            'reply_markup' => (new WishToAddSticksKeyboard())->getKeyboard()
        ]);
    }
}
