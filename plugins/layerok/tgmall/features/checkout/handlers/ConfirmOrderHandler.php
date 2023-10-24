<?php

namespace Layerok\TgMall\Features\Checkout\Handlers;

use Layerok\Basecode\Classes\Receipt;
use Layerok\PosterPos\Classes\PosterProducts;
use Layerok\PosterPos\Classes\PosterUtils;
use Layerok\TgMall\Classes\Callbacks\Handler;
use Layerok\TgMall\Facades\EmojisushiApi;
use Layerok\TgMall\Features\Checkout\Keyboards\OrderConfirmedKeyboard;
use poster\src\PosterApi;
use Telegram\Bot\Api;


class ConfirmOrderHandler extends Handler
{
    public string $name = "confirm_order";

    public function run()
    {
        $state = $this->getUser()->state;
        $user = $this->getUser();

        $phone = $user->phone;
        $firstName = $user->firstname;
        $lastName = $user->lastname;
        $address = $state->getOrderInfoAddress();
        $change = $state->getOrderInfoChange();
        $comment = $state->getOrderInfoComment();

        $payment_method = EmojisushiApi::getPaymentMethod(['id' => $state->getOrderInfoPaymentMethodId()]);
        $shipping_method = EmojisushiApi::getShippingMethod(['id' => $state->getOrderInfoDeliveryMethodId()]);


        $sticks = $state->getOrderInfoSticksCount();
        $spot = EmojisushiApi::getSpot(['slug_or_id' => $state->getSpotId()]);


        $cart = EmojisushiApi::getCart();
        if (!count($cart['data']) > 0) {
            $this->replyWithMessage([
                'text' => \Lang::get('layerok.tgmall::lang.telegram.texts.cart_is_empty'),
            ]);
            return false;
        }

        $posterProducts = new PosterProducts();

        $posterProducts
            ->addCartProducts($cart['data'])
            ->addProduct(
                492,
                \Lang::get('layerok.tgmall::lang.telegram.receipt.sticks_name'),
                $sticks
            );

        $poster_comment = PosterUtils::getComment([
            'comment' => $comment,
            'payment_method_name' => $payment_method['name'] ?? null,
            'delivery_method_name' => $shipping_method['name'] ?? null,
            'change' => $change
        ],  function($key) {
            return \Lang::get("layerok.tgmall::lang.telegram.receipt." . $key);
        });

        $tablet_id = $spot->tablet->tablet_id ?? env('POSTER_FALLBACK_TABLET_ID');

        PosterApi::init(config('poster'));
        $result = (object)PosterApi::incomingOrders()
            ->createIncomingOrder([
                'spot_id' => $tablet_id,
                'phone' => $phone,
                'products' => $posterProducts->all(),
                'first_name' => $firstName,
                'last_name' => $lastName,
                'comment' => $poster_comment,
                'address' => $address
            ]);

        if (isset($result->error)) {
            $poster_err =  $result->message;

            $this->replyWithMessage([
                'text' => $poster_err
            ]);

            \Log::error($poster_err);
            return false;
        }

        $token = $spot['bot']['token'] ?? env('TELEGRAM_FALLBACK_BOT_TOKEN');
        $chat_id = $spot['chat']['internal_id'] ?? env('TELEGRAM_FALLBACK_CHAT_ID');
        $api = new Api($token);

        $receipt = new Receipt();
        $receipt
            ->headline(\Lang::get('layerok.tgmall::lang.telegram.receipt.new_order'))
            ->field(\Lang::get('layerok.tgmall::lang.telegram.receipt.first_name'), $firstName)
            ->field(\Lang::get('layerok.tgmall::lang.telegram.receipt.last_name'), $lastName)
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
            ->field(\Lang::get('layerok.tgmall::lang.telegram.receipt.spot'), $spot['name'])
            ->field(\Lang::get('layerok.tgmall::lang.telegram.receipt.target'), \Lang::get('layerok.tgmall::lang.telegram.receipt.bot'));

        $api->sendMessage([
            'text' => $receipt->getText(),
            'parse_mode' => "html",
            'chat_id' => $chat_id
        ]);

        $k = new OrderConfirmedKeyboard();

        EmojisushiApi::clearCart();

        $this->replyWithMessage([
            'text' => \Lang::get('layerok.tgmall::lang.telegram.texts.thank_you'),
            'reply_markup' => $k->getKeyboard()
        ]);
    }


}
