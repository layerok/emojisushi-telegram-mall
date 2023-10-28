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
        $state = $this->getUser()->state;

        $address =  $state->getStateValue('order_info.address') ?? null;
        $phone =  $state->getStateValue('order_info.phone') ?? null;
        $firstName =  $state->getStateValue('order_info.first_name') ?? null;
        $change = $state->getStateValue('order_info.change') ?? null;
        $comment = $state->getStateValue('order_info.comment') ?? null;

        $payment_method = EmojisushiApi::getPaymentMethod(['id' => $state->getStateValue('order_info.payment_method_id') ?? null]);
        $shipping_method = EmojisushiApi::getShippingMethod(['id' => $state->getStateValue('order_info.delivery_method_id') ?? null]);

        $spot = EmojisushiApi::getSpot(['slug_or_id'=> $state->getStateValue('spot_id')]);

        $cart = EmojisushiApi::getCart();
        $posterProducts = new PosterProducts();

        $posterProducts
            ->addCartProducts($cart['data'])
            ->addProduct(
                492,
                \Lang::get('layerok.tgmall::lang.telegram.receipt.sticks_name'),
                $state->getStateValue('order_info.sticks_count') ?? null
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
        $this->getUser()->state->setStateValue('message_handler',null);
    }
}
