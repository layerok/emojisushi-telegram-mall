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

        $this->user->state->order->delivery_method_id = $id;
        $this->user->save();

        $method = EmojisushiApi::getShippingMethod(['id' => $id]);
        if ($method->code === 'courier') {
            // доставка курьером
            $this->replyWithMessage([
                'text' => \Lang::get('layerok.tgmall::lang.telegram.texts.type_delivery_address'),
            ]);
            $this->user->state->message_handler = OrderDeliveryAddressHandler::class;
            $this->user->save();


            return;
        } else if($method->code === 'takeaway') {
            $k = new SticksKeyboard();
            // был выбран самовывоз
            $this->replyWithMessage([
                'text' => \Lang::get('layerok.tgmall::lang.telegram.texts.add_sticks_question'),
                'reply_markup' => $k->getKeyboard()
            ]);

            $this->user->state->message_handler = null;
            $this->user->save();

            return;
        }

        $handler = new WishToLeaveCommentHandler($this->getUser(), $this->getApi());
        $handler->make($this->update, []);
    }
}
