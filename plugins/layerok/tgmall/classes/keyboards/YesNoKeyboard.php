<?php

namespace Layerok\TgMall\Classes\Keyboards;

use Telegram\Bot\Keyboard\Keyboard;

class YesNoKeyboard
{

    public function __construct(public array $yes, public array $no)
    {

    }

    public function getKeyboard(): Keyboard
    {
        return (new Keyboard())->inline()->row([
            Keyboard::inlineButton([
                'text' => \Lang::get('layerok.tgmall::lang.telegram.buttons.yes'),
                'callback_data' => json_encode([$this->yes['handler'], []]),
            ]),
            Keyboard::inlineButton([
                'text' => \Lang::get('layerok.tgmall::lang.telegram.buttons.no'),
                'callback_data' => json_encode([$this->no['handler'], []])
            ])
        ]);
    }
}
