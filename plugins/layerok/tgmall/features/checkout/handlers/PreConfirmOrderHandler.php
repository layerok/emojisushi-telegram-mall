<?php

namespace Layerok\TgMall\Features\Checkout\Handlers;

use Event;
use Layerok\TgMall\Classes\Callbacks\Handler;
use Layerok\TgMall\Classes\Keyboards\YesNoKeyboard;
use Layerok\TgMall\Classes\Traits\Lang;
use OFFLINE\Mall\Classes\Utils\Money;
use OFFLINE\Mall\Models\Currency;
use OFFLINE\Mall\Models\PaymentMethod;
use OFFLINE\Mall\Models\ShippingMethod;


class PreConfirmOrderHandler extends Handler
{
    use Lang;

    protected $name = "pre_confirm_order";

    public function run()
    {
        $user = $this->getTelegramUser();
        $products = $this->getCart()->products()->get();
        $phone = $user->phone;
        $firstName = $user->firstname;
        $lastName = $user->lastname;


        $delivery = ShippingMethod::find($this->getState()->getOrderInfoDeliveryMethodId());
        $payment_method = PaymentMethod::find($this->getState()->getOrderInfoPaymentMethodId());


        $receipt = $this->getReceipt();
        $money = app()->make(Money::class);

        $receipt
            ->headline(self::lang('receipt.confirm_order_question'))
            ->field('first_name', $firstName)
            ->field('last_name', $lastName)
            ->field('phone', $phone)
            ->field('comment', $this->getState()->getOrderInfoComment())
            ->field('delivery_method_name', optional($delivery)->name)
            ->field('payment_method_name', optional($payment_method)->name)
            ->newLine()
            ->products($products)
            ->newLine()
            ->field('total', $money->format(
                $this->getCart()->totals()->totalPostTaxes(),
                null,
                Currency::$defaultCurrency
            ));

        if($result = Event::fire('tgmall.order.preconfirm.receipt', [$this], true)) {
            $receipt = $result;
        }

        $k = new YesNoKeyboard([
            'yes' => [
                'handler' => 'confirm_order'
            ],
            'no' => [
                'handler' => 'start'
            ]
        ]);

        $this->sendMessage([
            'text' => $receipt->getText(),
            'parse_mode' => 'html',
            'reply_markup' => $k->getKeyboard()
        ]);
        $this->getState()->setMessageHandler(null);
    }
}
