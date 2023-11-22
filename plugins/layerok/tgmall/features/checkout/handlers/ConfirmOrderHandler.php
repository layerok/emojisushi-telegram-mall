<?php

namespace Layerok\TgMall\Features\Checkout\Handlers;

use Layerok\TgMall\Classes\Callbacks\Handler;
use Layerok\TgMall\Facades\EmojisushiApi;
use Telegram\Bot\Keyboard\Keyboard;

class ConfirmOrderHandler extends Handler
{
    public string $name = "confirm_order";

    public function run()
    {
        $appState = $this->user->state;

        $payment_method = EmojisushiApi::getPaymentMethod(['id' => $appState->order->payment_method_id]);
        $shipping_method = EmojisushiApi::getShippingMethod(['id' => $appState->order->delivery_method_id]);

        // todo: handle 404 not found error
        $spot = EmojisushiApi::getSpot(['slug_or_id' => $appState->spot_id]);

        $cart = EmojisushiApi::getCart();

        if (!count($cart->data) > 0) {
            $this->replyWithMessage([
                'text' => \Lang::get('layerok.tgmall::lang.telegram.texts.cart_is_empty'),
            ]);
            return false;
        }

        EmojisushiApi::placeOrder([
            'phone' => $appState->order->phone,
            'firstname' => $appState->order->first_name,
            //'lastname' => $appState->order->last_name,
            // 'email' => $appState->order->email,
            'address' => $appState->order->address,
            'spot_id' => $spot->id,
            'payment_method_id' => $payment_method->id,
            'shipping_method_id' => $shipping_method->id,
            'change' => $appState->order->change,
            'sticks' => $appState->order->sticks_count,
            'comment' => $appState->order->comment
        ]);


        EmojisushiApi::clearCart();

        $this->replyWithMessage([
            'text' => \Lang::get('layerok.tgmall::lang.telegram.texts.thank_you'),
            'reply_markup' => (new Keyboard())->inline()->row([
                Keyboard::inlineButton([
                    'text' => \Lang::get('layerok.tgmall::lang.telegram.buttons.in_menu_main'),
                    'callback_data' => json_encode([
                        'start'
                    ])
                ])
            ])->row([])
        ]);
    }


}
