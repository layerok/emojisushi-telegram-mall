<?php

namespace Layerok\TgMall\Features\Checkout\Handlers;

use Layerok\TgMall\Classes\Callbacks\Handler;

class ChoseOdesaSpotHandler extends Handler
{
    protected string $name = "chose_odesa_spot";

    public function run()
    {
        $spot_id = $this->arguments[0];

        $this->user->state->spot_id = $spot_id;
        $this->user->save();


        $handler = new ListPaymentMethodsHandler($this->getUser(), $this->getApi());
        $handler->make($this->update, []);
    }
}
