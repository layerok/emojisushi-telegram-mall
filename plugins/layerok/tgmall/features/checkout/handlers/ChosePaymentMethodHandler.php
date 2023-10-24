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
        $this->getUser()->state->setOrderInfoPaymentMethodId($id);

        $method = EmojisushiApi::getPaymentMethod(['id' => $id]);

        if ($method['code'] == 'cash') {
            // наличными
            $k = new PreparePaymentChangeKeyboard();
            $this->replyWithMessage([
                'text' => \Lang::get('layerok.tgmall::lang.telegram.texts.prepare_change_question'),
                'reply_markup' => $k->getKeyboard()
            ]);
            $this->getUser()->state->setMessageHandler(null);
            return;
        }

        $handler = new ListDeliveryMethodsHandler($this->getUser(), $this->getApi());
        $handler->make($this->update, []);
    }
}
