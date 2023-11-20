<?php

namespace Layerok\TgMall\Features\Index;

use Layerok\TgMall\Classes\Callbacks\Handler;

class ListCitiesHandler extends Handler
{
    protected string $name = "list_cities";

    public function run()
    {
        $k = new CitiesKeyboard();
        $this->replyWithMessage([
            'text' => \Lang::get('layerok.tgmall::lang.telegram.cities.choose'),
            'reply_markup' => $k->getKeyboard()
        ]);
    }
}
