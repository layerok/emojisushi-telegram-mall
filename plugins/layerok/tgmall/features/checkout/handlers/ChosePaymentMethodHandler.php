<?php

namespace Layerok\TgMall\Features\Checkout\Handlers;

use Layerok\TgMall\Classes\Callbacks\CallbackQueryBus;
use Layerok\TgMall\Classes\Callbacks\Handler;
use Layerok\TgMall\Classes\Traits\Lang;
use Layerok\TgMall\Features\Checkout\Keyboards\PreparePaymentChangeKeyboard;
use OFFLINE\Mall\Models\PaymentMethod;

class ChosePaymentMethodHandler extends Handler
{
    use Lang;

    protected $name = "chose_payment_method";

    public function run()
    {
        $id = $this->arguments['id'];
        $this->getState()->setOrderInfoPaymentMethodId($id);

        $method = PaymentMethod::find($id);
        if ($method->code == 'cash') {
            // наличными
            $k = new PreparePaymentChangeKeyboard();
            $this->sendMessage([
                'text' => self::lang('texts.prepare_change_question'),
                'reply_markup' => $k->getKeyboard()
            ]);
            $this->getState()->setMessageHandler(null);
            return;
        }

        CallbackQueryBus::instance()->make('list_delivery_methods', []);

    }
}
