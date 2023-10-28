<?php

namespace Layerok\TgMall\Features\Index;

use Layerok\TgMall\Classes\Keyboards\InlineKeyboard;
use Layerok\TgMall\Facades\EmojisushiApi;
use Layerok\TgMall\Objects\Spot;

class SpotsKeyboard extends InlineKeyboard
{
    public function build(): void
    {
        collect(EmojisushiApi::getSpots()->data)->each(function(Spot $spot) {
            $this->append([
                'text' => $spot->name,
                'callback_data' => json_encode([
                    'change_spot',
                    [$spot->id]
                ])
            ])->nextRow();
        });

    }
}
