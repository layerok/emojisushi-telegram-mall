<?php

namespace Layerok\TgMall\Features\Checkout\Handlers;

use Event;
use Layerok\Basecode\Classes\Receipt;
use Layerok\PosterPos\Classes\PosterProducts;
use Layerok\TgMall\Classes\Callbacks\Handler;
use Layerok\TgMall\Classes\Keyboards\YesNoKeyboard;
use Layerok\TgMall\Facades\EmojisushiApi;


class PreConfirmOrderHandler extends Handler
{
    protected string $name = "pre_confirm_order";

    public function run()
    {
        $user = $this->getTelegramUser();
        $cart = EmojisushiApi::getCart();

        $phone = $user->phone;
        $firstName = $user->firstname;
        $lastName = $user->lastname;

        $payment_method = EmojisushiApi::getPaymentMethod([
            'id' => $this->getState()->getOrderInfoPaymentMethodId()
        ]);
        $shipping_method = EmojisushiApi::getShippingMethod([
            'id' => $this->getState()->getOrderInfoDeliveryMethodId()
        ]);

        $receipt = $this->getReceipt();
        $posterProducts = new PosterProducts();
        $posterProducts
            ->addCartProducts($cart['data'])
            ->addProduct(
                492,
                \Lang::get('layerok.tgmall::lang.telegram.receipt.sticks_name'),
                $this->getState()->getOrderInfoSticksCount()
            );

        $receipt
            ->headline(\Lang::get('layerok.tgmall::lang.telegram.receipt.confirm_order_question'))
            ->field('first_name', $firstName)
            ->field('last_name', $lastName)
            ->field('phone', $phone)
            ->field('comment', $this->getState()->getOrderInfoComment())
            ->field('delivery_method_name', $shipping_method['name'] ?? null)
            ->field('payment_method_name', $payment_method['name'] ?? null)
            ->newLine()
            ->products($posterProducts->all())
            ->newLine()
            ->field('total', $cart['total']);

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

    public function getReceipt(): Receipt
    {
        $receipt = new Receipt();

        $receipt->setProductNameResolver(function(array $cartProduct) {
            return $cartProduct['name'];
        });
        $receipt->setProductCountResolver(function(array $cartProduct) {
            return $cartProduct['count'];
        });

        $receipt->setTransResolver(function($key) {
            return \Lang::get('layerok.tgmall::lang.telegram.receipt.' . $key);
        });

        return $receipt;
    }
}
