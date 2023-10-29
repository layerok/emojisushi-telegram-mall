<?php

namespace Layerok\TgMall\Features\Checkout\Handlers;

use Layerok\Basecode\Classes\Receipt;
use Layerok\TgMall\Classes\Callbacks\Handler;
use Layerok\TgMall\Classes\Keyboards\YesNoKeyboard;
use Layerok\TgMall\Facades\EmojisushiApi;
use Layerok\TgMall\Objects\CartProduct;


class PreConfirmOrderHandler extends Handler
{
    protected string $name = "pre_confirm_order";

    public function run()
    {
        $appState = $this->user->state->state;

        $payment_method = EmojisushiApi::getPaymentMethod(['id' => $appState->order->payment_method_id]);
        $shipping_method = EmojisushiApi::getShippingMethod(['id' => $appState->order->delivery_method_id]);

        // todo: handle 404 not found error
        $spot = EmojisushiApi::getSpot(['slug_or_id' => $appState->spot_id]);

        $cart = EmojisushiApi::getCart();

        $posterProducts = collect($cart->data)->map(function (CartProduct $cartProduct) {
            $posterProduct = [
                'name' => $cartProduct->product->name,
                'product_id' => $cartProduct->product->poster_id,
                'count' => $cartProduct->quantity,
            ];
            if (isset($cartProduct->variant->poster_id)) {
                $posterProduct['modificator_id'] = $cartProduct->variant->poster_id;
            }

            return $posterProduct;
        })->values()->toArray();

        if($appState->order->sticks_count) {
            $posterProducts[] = [
                'name' => \Lang::get('layerok.tgmall::lang.telegram.receipt.sticks_name'),
                'count' => $appState->order->sticks_count,
                'product_id' => 492
            ];
        }

        $receipt = new Receipt();
        $receipt
            ->headline(\Lang::get('layerok.tgmall::lang.telegram.receipt.confirm_order_question'))
            ->field(\Lang::get('layerok.tgmall::lang.telegram.receipt.first_name'), $appState->order->first_name)
            ->field(\Lang::get('layerok.tgmall::lang.telegram.receipt.phone'), $appState->order->phone)
            ->field(\Lang::get('layerok.tgmall::lang.telegram.receipt.delivery_method_name'), $shipping_method->name)
            ->field(\Lang::get('layerok.tgmall::lang.telegram.receipt.address'), $appState->order->address)
            ->field(\Lang::get('layerok.tgmall::lang.telegram.receipt.payment_method_name'), $payment_method->name)
            ->field(\Lang::get('layerok.tgmall::lang.telegram.receipt.change'), $appState->order->change)
            ->field(\Lang::get('layerok.tgmall::lang.telegram.receipt.comment'), $appState->order->comment)
            ->newLine()
            ->map($posterProducts, function($item) {
                $this->hyphen()
                    ->space()
                    ->p($item['name'])
                    ->space()
                    ->p("x")
                    ->p( $item['count'])->newLine();
            })
            ->newLine()
            ->field(\Lang::get('layerok.tgmall::lang.telegram.receipt.total'),  $cart->total)
            ->field(\Lang::get('layerok.tgmall::lang.telegram.receipt.spot'), $spot->name);


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

        $appState = $this->user->state->state;
        $appState->message_handler = null;
        $this->user->state->state = $appState;
        $this->user->state->save();

    }
}
