<?php

namespace Layerok\TgMall\Features\Checkout\Handlers;

use Layerok\TgMall\Classes\Callbacks\Handler;
use Layerok\TgMall\Features\Checkout\Keyboards\DeliveryMethodsKeyboard;


class ListDeliveryMethodsHandler extends Handler
{
    protected string $name = "list_delivery_methods";

    public function run()
    {
        $k = new DeliveryMethodsKeyboard();
        $this->replyWithMessage([
            'chat_id' => $this->user->chat_id,
            'text' => \Lang::get('layerok.tgmall::lang.telegram.texts.chose_delivery_method'),
            'reply_markup' => $k->getKeyboard(),
        ]);
    }
}
