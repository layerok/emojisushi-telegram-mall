<?php

namespace Layerok\TgMall\Features\Checkout\Handlers;

use Layerok\Basecode\Classes\Receipt;
use Layerok\TgMall\Classes\Callbacks\Handler;
use Layerok\TgMall\Facades\EmojisushiApi;
use Layerok\TgMall\Objects\CartProduct;
use poster\src\PosterApi;
use Telegram\Bot\Api;
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

        $posterProducts = collect($cart->data)->map(function (CartProduct $cartProduct) {
            $posterProduct = [
                'name' => $cartProduct->product->name,
                'product_id' => $cartProduct->product->poster_id,
                'count' => $cartProduct->quantity,
                'modificator_id' => $cartProduct->variant->poster_id
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

        $poster_comment = collect([
            ['', $appState->order->comment],
            [\Lang::get("layerok.tgmall::lang.telegram.receipt.change"), $appState->order->change],
            [\Lang::get("layerok.tgmall::lang.telegram.receipt.payment_method_name"), $payment_method->name],
            [\Lang::get("layerok.tgmall::lang.telegram.receipt.delivery_method_name"), $shipping_method->name],
        ])->filter(fn($part) => !empty($part[1]))
            ->map(fn($part) => ($part[0] ? $part[0] . ': ' : '') . $part[1])
            ->join(' || ');

        PosterApi::init(config('poster'));
        $result = (object)PosterApi::incomingOrders()
            ->createIncomingOrder([
                'spot_id' => $spot->tablet->tablet_id ?? env('POSTER_FALLBACK_TABLET_ID'),
                'phone' => $appState->order->phone,
                'products' => $posterProducts,
                'first_name' => $appState->order->first_name,
                'comment' => $poster_comment,
                'address' => $appState->order->address
            ]);

        if (isset($result->error)) {
            $poster_err = $result->message;

            $this->replyWithMessage([
                'text' => $poster_err
            ]);

            \Log::error($poster_err);
            return false;
        }

        // todo: api doesn't include tokens
        $token = env('TELEGRAM_FALLBACK_BOT_TOKEN');
        $chat_id = env('TELEGRAM_FALLBACK_CHAT_ID');
        $api = new Api($token);

        $receipt = new Receipt();
        $receipt
            ->headline(\Lang::get('layerok.tgmall::lang.telegram.receipt.new_order'))
            ->field(\Lang::get('layerok.tgmall::lang.telegram.receipt.first_name'), $appState->order->first_name)
            ->field(\Lang::get('layerok.tgmall::lang.telegram.receipt.phone'), $appState->order->phone)
            ->field(\Lang::get('layerok.tgmall::lang.telegram.receipt.delivery_method_name'), $shipping_method->name)
            ->field(\Lang::get('layerok.tgmall::lang.telegram.receipt.address'), $appState->order->address)
            ->field(\Lang::get('layerok.tgmall::lang.telegram.receipt.payment_method_name'), $payment_method->name)
            ->field(\Lang::get('layerok.tgmall::lang.telegram.receipt.change'), $appState->order->change)
            ->field(\Lang::get('layerok.tgmall::lang.telegram.receipt.comment'), $appState->order->comment)
            ->newLine()
            ->map($posterProducts, function ($item) {
                $this->hyphen()
                    ->space()
                    ->p($item['name'])
                    ->space()
                    ->p("x")
                    ->p($item['count'])->newLine();
            })
            ->newLine()
            ->field(\Lang::get('layerok.tgmall::lang.telegram.receipt.total'), $cart->total)
            ->field(\Lang::get('layerok.tgmall::lang.telegram.receipt.spot'), $spot->name)
            ->field(\Lang::get('layerok.tgmall::lang.telegram.receipt.target'), \Lang::get('layerok.tgmall::lang.telegram.receipt.bot'));

        $api->sendMessage([
            'text' => $receipt->getText(),
            'parse_mode' => "html",
            'chat_id' => $chat_id
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
