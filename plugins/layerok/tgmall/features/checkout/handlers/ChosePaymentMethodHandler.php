<?php

namespace Layerok\TgMall\Features\Checkout\Handlers;

use Layerok\TgMall\Classes\Callbacks\Handler;
use Layerok\TgMall\Facades\EmojisushiApi;
use Layerok\TgMall\Features\Checkout\Keyboards\PreparePaymentChangeKeyboard;

class ChosePaymentMethodHandler extends Handler
{
    protected string $name = "chose_payment_method";

    public function run()
    {
        $id = $this->arguments['id'];

        $appState = $this->user->state->state;
        $appState->order->payment_method_id = $id;
        $this->user->state->state = $appState;
        $this->user->state->save();

        $method = EmojisushiApi::getPaymentMethod(['id' => $id]);

        if ($method->code == 'cash') {
            // наличными
            $k = new PreparePaymentChangeKeyboard();
            $this->replyWithMessage([
                'text' => \Lang::get('layerok.tgmall::lang.telegram.texts.prepare_change_question'),
                'reply_markup' => $k->getKeyboard()
            ]);

            $appState = $this->user->state->state;
            $appState->message_handler = null;
            $this->user->state->state = $appState;
            $this->user->state->save();

            return;
        }

        $handler = new ListDeliveryMethodsHandler($this->getUser(), $this->getApi());
        $handler->make($this->update, []);
    }
}
