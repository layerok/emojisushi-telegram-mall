<?php

namespace Layerok\TgMall\Features\Checkout\Handlers;

use Layerok\TgMall\Classes\Callbacks\Handler;

use Layerok\TgMall\Facades\EmojisushiApi;
use Layerok\TgMall\Features\Checkout\Keyboards\SticksKeyboard;
use Layerok\TgMall\Features\Checkout\Messages\OrderDeliveryAddressHandler;

class ChoseDeliveryMethodHandler extends Handler
{
    protected string $name = "chose_delivery_method";

    public function run()
    {
        $id = $this->arguments['id'];
        $this->getUser()->state->setOrderInfoDeliveryMethodId($id);

        $method = EmojisushiApi::getShippingMethod(['id' => $id]);
        if ($method['code'] === 'courier') {
            // доставка курьером
            $this->replyWithMessage([
                'text' => \Lang::get('layerok.tgmall::lang.telegram.texts.type_delivery_address'),
            ]);
            $this->getUser()->state->setMessageHandler(OrderDeliveryAddressHandler::class);

            return;
        } else if($method['code'] === 'takeaway') {
            $k = new SticksKeyboard();
            // был выбран самовывоз
            $this->replyWithMessage([
                'text' => \Lang::get('layerok.tgmall::lang.telegram.texts.add_sticks_question'),
                'reply_markup' => $k->getKeyboard()
            ]);
            $this->getUser()->state->setMessageHandler(null);

            return;
        }

        $handler = new WishToLeaveCommentHandler($this->getUser(), $this->getApi());
        $handler->make($this->update, []);
    }
}
