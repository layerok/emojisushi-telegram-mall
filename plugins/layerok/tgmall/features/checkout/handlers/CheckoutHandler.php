<?php

namespace Layerok\TgMall\Features\Checkout\Handlers;

use Layerok\TgMall\Classes\Callbacks\Handler;
use Layerok\TgMall\Features\Checkout\Messages\OrderNameHandler;

class CheckoutHandler extends Handler
{
    protected string $name = "checkout";

    public function run()
    {
        // Очищаем инфу о заказе при начале оформления заказа
        $appState = $this->user->state->state;
        $appState->order = null;
        $this->user->state->state = $appState;
        $this->user->state->save();

        $this->replyWithMessage([
            'text' => \Lang::get('layerok.tgmall::lang.telegram.texts.type_your_name')
        ]);

        $appState = $this->user->state->state;
        $appState->message_handler = OrderNameHandler::class;
        $this->user->state->state = $appState;
        $this->user->state->save();
    }
}
