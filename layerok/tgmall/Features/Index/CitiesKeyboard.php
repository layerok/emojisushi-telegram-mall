<?php

namespace Layerok\TgMall\Features\Index;

use Layerok\TgMall\Facades\EmojisushiApi;
use Layerok\TgMall\Objects\City;
use Telegram\Bot\Keyboard\Keyboard;

class CitiesKeyboard
{
    public function getKeyboard(): Keyboard
    {
        $keyboard = (new Keyboard())->inline();

        collect(EmojisushiApi::getCities()->data)->each(function(City $city) use($keyboard) {
            $keyboard->row([
                Keyboard::inlineButton([
                    'text' => $city->name,
                    'callback_data' => json_encode([
                        'change_city',
                        [$city->id]
                    ])
                ])
            ])->row([]);
        });

        return $keyboard;
    }
}
