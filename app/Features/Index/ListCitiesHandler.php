<?php

namespace App\Features\Index;

use App\Classes\Callbacks\Handler;

class ListCitiesHandler extends Handler
{
    protected string $name = "list_cities";

    public function run()
    {
        $k = new CitiesKeyboard();
        $this->replyWithMessage([
            'text' => \Lang::get('lang.telegram.cities.choose'),
            'reply_markup' => $k->getKeyboard()
        ]);
    }
}
