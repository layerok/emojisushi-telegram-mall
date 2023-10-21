<?php

namespace Layerok\TgMall\Features\Index;

use Layerok\TgMall\Classes\Callbacks\Handler;

class ListSpotsHandler extends Handler
{
    protected string $name = "list_spots";

    public function run()
    {
        $k = new SpotsKeyboard();
        $this->replyWithMessage([
            'text' => \Lang::get('layerok.tgmall::lang.telegram.spots.choose'),
            'reply_markup' => $k->getKeyboard()
        ]);
    }
}
