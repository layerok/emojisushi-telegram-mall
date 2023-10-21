<?php

namespace Layerok\TgMall\Features\Checkout\Handlers;

use Layerok\TgMall\Classes\Callbacks\Handler;
use Layerok\TgMall\Features\Checkout\Keyboards\PaymentMethodsKeyboard;

class ListPaymentMethodsHandler extends Handler
{
    protected string $name = "list_payment_methods";


    public function run()
    {
        $k = new PaymentMethodsKeyboard();

        $this->sendMessage([
            'text' => \Lang::get('layerok.tgmall::lang.telegram.texts.chose_payment_method'),
            'reply_markup' => $k->getKeyboard()
        ]);
    }
}
