<?php

namespace Layerok\TgMall\Features\Checkout\Handlers;

use Layerok\TgMall\Classes\Callbacks\Handler;
use Layerok\TgMall\Facades\EmojisushiApi;
use Layerok\TgMall\Features\Checkout\Keyboard\WishToAddSticksKeyboard;
use Layerok\TgMall\Features\Checkout\Messages\OrderDeliveryAddressHandler;
use Layerok\TgMall\Objects\City;
use Layerok\TgMall\Objects\District;
use Layerok\TgMall\Objects\ShipmentMethod;
use Layerok\TgMall\Objects\Spot;
use Telegram\Bot\Keyboard\Keyboard;

class ChoseDeliveryMethodHandler extends Handler
{
    protected string $name = "chose_delivery_method";

    public function run()
    {
        $id = $this->arguments['id'];

        $this->user->state->order->delivery_method_id = $id;
        $this->user->save();

        $method = EmojisushiApi::getShippingMethod(['id' => $id]);

        $city = EmojisushiApi::getCity([
            'slug_or_id' => $this->user->state->city_id
        ]);

        switch($city->slug) {
            case "odesa": {
                $this->handleOdesa($method, $city);
                break;
            }
            case "chorno": {
                $this->handleChernomorsk($method, $city);
            }
        }
    }

    public function handleOdesa(ShipmentMethod $method, City $city) {
        switch ($method->code) {
            case "courier": {
                $keyboard = (new Keyboard())->inline();
                collect($city->districts)->each(function(District $district) use($keyboard) {
                    $keyboard->row([
                        [
                            'text' => $district->name,
                            'callback_data' => json_encode(['chose_odesa_district', [$district->id]])
                        ]
                    ]);
                });
                $this->replyWithMessage([
                    'text' => \Lang::get('layerok.tgmall::lang.telegram.districts.choose'),
                    'reply_markup' => $keyboard
                ]);
                break;
            }
            default: {
                $keyboard = (new Keyboard())->inline();
                collect($city->spots)->each(function(Spot $spot) use($keyboard) {
                    $keyboard->row([
                        [
                            'text' => $spot->name,
                            'callback_data' => json_encode(['chose_odesa_spot', [$spot->id]])
                        ]
                    ]);
                });
                $this->replyWithMessage([
                    'text' => \Lang::get('layerok.tgmall::lang.telegram.spots.choose'),
                    'reply_markup' => $keyboard
                ]);
                break;
            }
        }
    }

    public function handleChernomorsk(ShipmentMethod $method, City $city) {
        switch ($method->code) {
            case "courier": {
                $this->replyWithMessage([
                    'text' => \Lang::get('layerok.tgmall::lang.telegram.texts.type_delivery_address'),
                ]);
                $this->user->state->message_handler = OrderDeliveryAddressHandler::class;
                $this->user->state->spot_id = $city->spots[0]->id;
                $this->user->save();
                break;
            }
            default: {
                $this->replyWithMessage([
                    'text' => \Lang::get('layerok.tgmall::lang.telegram.texts.add_sticks_question'),
                    'reply_markup' => (new WishToAddSticksKeyboard())->getKeyboard()
                ]);

                $this->user->state->message_handler = null;
                $this->user->state->spot_id = $city->spots[0]->id;
                $this->user->save();
                break;
            }
        }

    }
}
