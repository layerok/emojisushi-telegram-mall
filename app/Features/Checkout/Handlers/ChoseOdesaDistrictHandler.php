<?php

namespace App\Features\Checkout\Handlers;

use App\Classes\Callbacks\Handler;
use App\Facades\EmojisushiApi;
use App\Features\Checkout\Messages\OrderDeliveryAddressHandler;
use App\Objects\District;

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
            'text' => \Lang::get('lang.telegram.texts.type_delivery_address'),
        ]);

        $this->user->save();
    }
}
