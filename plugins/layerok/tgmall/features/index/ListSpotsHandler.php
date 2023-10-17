<?php

namespace Layerok\TgMall\Features\Index;

use Layerok\TgMall\Classes\Callbacks\Handler;
use Layerok\TgMall\Classes\Traits\Lang;


class ListSpotsHandler extends Handler
{
    use Lang;

    protected string $name = "list_spots";

    public function run()
    {
        $k = new SpotsKeyboard();
        $this->replyWithMessage([
            'text' => self::lang('spots.choose'),
            'reply_markup' => $k->getKeyboard()
        ]);
    }
}
