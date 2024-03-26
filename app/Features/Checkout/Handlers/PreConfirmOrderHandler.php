<?php

namespace App\Features\Checkout\Handlers;

use App\Classes\Callbacks\Handler;
use App\Classes\Keyboards\YesNoKeyboard;
use App\Classes\Receipt;
use App\Facades\EmojisushiApi;
use App\Objects\CartProduct;


class PreConfirmOrderHandler extends Handler
{
    protected string $name = "pre_confirm_order";

    public function run()
    {
        $appState = $this->user->state;

        $payment_method = EmojisushiApi::getPaymentMethod(['id' => $appState->order->payment_method_id]);
        $shipping_method = EmojisushiApi::getShippingMethod(['id' => $appState->order->delivery_method_id]);

        // todo: handle 404 not found error
        $spot = EmojisushiApi::getSpot(['slug_or_id' => $appState->spot_id]);

        $cart = EmojisushiApi::getCart();

        $posterProducts = collect($cart->data)->map(function (CartProduct $cartProduct) {
            return [
                'name' => $cartProduct->product->name,
                'count' => $cartProduct->quantity,
            ];
        })->values()->toArray();

        $receipt = new Receipt();
        $receipt
            ->headline(\Lang::get('lang.telegram.receipt.confirm_order_question'))
            ->field(\Lang::get('lang.telegram.receipt.first_name'), $appState->order->first_name)
            ->field(\Lang::get('lang.telegram.receipt.phone'), $appState->order->phone)
            ->field(\Lang::get('lang.telegram.receipt.delivery_method_name'), $shipping_method->name)
            ->field(\Lang::get('lang.telegram.receipt.address'), $appState->order->address)
            ->field(\Lang::get('lang.telegram.receipt.payment_method_name'), $payment_method->name)
            ->field(\Lang::get('lang.telegram.receipt.change'), $appState->order->change)
            ->field(\Lang::get('lang.telegram.receipt.persons_amount'), $appState->order->sticks_count)
            ->field(\Lang::get('lang.telegram.receipt.comment'), $appState->order->comment)
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
            ->field(\Lang::get('lang.telegram.receipt.total'),  $cart->total)
            ->field(\Lang::get('lang.telegram.receipt.spot'), $spot->name);

        $this->replyWithMessage([
            'text' => $receipt->getText(),
            'parse_mode' => 'html',
            'reply_markup' => (new YesNoKeyboard(
                yes: ['handler' => 'confirm_order'],
                no: ['handler' => 'start']
            ))->getKeyboard()
        ]);

        $this->user->state->message_handler = null;
        $this->user->save();

    }
}
