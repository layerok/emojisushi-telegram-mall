<?php
namespace Layerok\TgMall\Features\Index;

use Layerok\TgMall\Classes\Keyboards\InlineKeyboard;
use Layerok\TgMall\Classes\Traits\CallbackData;
use Layerok\TgMall\Classes\Traits\Lang;
use Layerok\TgMall\Facades\EmojisushiApi;

class SpotsKeyboard extends InlineKeyboard
{
    use Lang;
    use CallbackData;

    public function build(): void
    {
        $spots = EmojisushiApi::getSpots()['data'];

        array_map(function($spot) {
            $this->append([
                'text' => $spot['name'],
                'callback_data' => self::prepareCallbackData(
                    'change_spot',
                    [$spot['id']]
                )
            ])->nextRow();
        }, $spots);

    }
}
