<?php

namespace Layerok\TgMall\Features\Index;

use Layerok\TgMall\Classes\Keyboards\InlineKeyboard;
use Layerok\TgMall\Facades\EmojisushiApi;

class SpotsKeyboard extends InlineKeyboard
{
    public function build(): void
    {
        $spots = EmojisushiApi::getSpots()['data'];

        array_map(function($spot) {
            $this->append([
                'text' => $spot['name'],
                'callback_data' => json_encode([
                    'change_spot',
                    [$spot['id']]
                ])
            ])->nextRow();
        }, $spots);

    }
}
