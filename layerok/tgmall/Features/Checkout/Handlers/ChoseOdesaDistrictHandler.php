<?php

namespace Layerok\TgMall\Features\Checkout\Handlers;

use Layerok\TgMall\Classes\Callbacks\Handler;
use Layerok\TgMall\Facades\EmojisushiApi;
use Layerok\TgMall\Features\Checkout\Messages\OrderDeliveryAddressHandler;
use Layerok\TgMall\Objects\District;

class ChoseOdesaDistrictHandler extends Handler
{
    protected string $name = "chose_odesa_district";

    public function run()
    {
        $city = EmojisushiApi::getCity([
            'slug_or_id' => $this->user->state->city_id
        ]);

        /** @var District $district */
        $district = collect($city->districts)->first(function(District $district) {
            return $district->id === $this->arguments[0];
        });

        $this->user->state->message_handler = OrderDeliveryAddressHandler::class;
        $this->user->state->spot_id = $district->spots[0]->id;
        $this->user->save();

        $this->replyWithMessage([
            'text' => \Lang::get('layerok.tgmall::lang.telegram.texts.type_delivery_address'),
        ]);

        $this->user->save();
    }
}
