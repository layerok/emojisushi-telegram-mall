<?php

namespace Layerok\TgMall\Features\Checkout\Handlers;

use Event;
use Layerok\Basecode\Classes\Receipt;
use Layerok\PosterPos\Classes\PosterProducts;
use Layerok\TgMall\Classes\Callbacks\Handler;
use Layerok\TgMall\Classes\Keyboards\YesNoKeyboard;
use Layerok\TgMall\Classes\StateKeys;
use Layerok\TgMall\Facades\EmojisushiApi;


class PreConfirmOrderHandler extends Handler
{
    protected string $name = "pre_confirm_order";

    public function run()
    {
        $state = $this->getUser()->state;

        $firstName =  $state->getStateValue(StateKeys::ORDER_FIRST_NAME) ?? null;
        $phone =  $state->getStateValue(StateKeys::ORDER_PHONE) ?? null;
        $address =  $state->getStateValue(StateKeys::ORDER_ADDRESS) ?? null;
        $change = $state->getStateValue(StateKeys::ORDER_CHANGE) ?? null;
        $comment = $state->getStateValue(StateKeys::ORDER_COMMENT) ?? null;

        $payment_method = EmojisushiApi::getPaymentMethod(['id' => $state->getStateValue(StateKeys::ORDER_PAYMENT_METHOD_ID) ?? null]);
        $shipping_method = EmojisushiApi::getShippingMethod(['id' => $state->getStateValue(StateKeys::ORDER_DELIVERY_METHOD_ID) ?? null]);

        // todo: handle 404 not found error
        $spot = EmojisushiApi::getSpot(['slug_or_id'=> $state->getStateValue(StateKeys::SPOT_ID)]);

        $cart = EmojisushiApi::getCart();
        $posterProducts = new PosterProducts();

        $posterProducts
            ->addCartProducts($cart['data'])
            ->addProduct(
                492,
                \Lang::get('layerok.tgmall::lang.telegram.receipt.sticks_name'),
                $state->getStateValue(StateKeys::ORDER_STICKS_COUNT) ?? null
            );

        $receipt = new Receipt();
        $receipt
            ->headline(\Lang::get('layerok.tgmall::lang.telegram.receipt.confirm_order_question'))
            ->field(\Lang::get('layerok.tgmall::lang.telegram.receipt.first_name'), $firstName)
            ->field(\Lang::get('layerok.tgmall::lang.telegram.receipt.phone'), $phone)
            ->field(\Lang::get('layerok.tgmall::lang.telegram.receipt.delivery_method_name'), $shipping_method['name'] ?? null)
            ->field(\Lang::get('layerok.tgmall::lang.telegram.receipt.address'), $address)
            ->field(\Lang::get('layerok.tgmall::lang.telegram.receipt.payment_method_name'), $payment_method['name'] ?? null)
            ->field(\Lang::get('layerok.tgmall::lang.telegram.receipt.change'), $change)
            ->field(\Lang::get('layerok.tgmall::lang.telegram.receipt.comment'), $comment)
            ->newLine()
            ->map($posterProducts->all(), function($item) {
                $this->hyphen()
                    ->space()
                    ->p($item['name'])
                    ->space()
                    ->p("x")
                    ->p( $item['count'])->newLine();
            })
            ->newLine()
            ->field(\Lang::get('layerok.tgmall::lang.telegram.receipt.total'),  $cart['total'])
            ->field(\Lang::get('layerok.tgmall::lang.telegram.receipt.spot'), $spot['name']);


        $k = new YesNoKeyboard([
            'yes' => [
                'handler' => 'confirm_order'
            ],
            'no' => [
                'handler' => 'start'
            ]
        ]);

        $this->replyWithMessage([
            'text' => $receipt->getText(),
            'parse_mode' => 'html',
            'reply_markup' => $k->getKeyboard()
        ]);
        $this->getUser()->state->setStateValue(StateKeys::MESSAGE_HANDLER, null);
    }
}
