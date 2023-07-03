<?php

namespace Layerok\TgMall\Features\Checkout\Handlers;
use Layerok\TgMall\Classes\Callbacks\CallbackQueryBus;
use Layerok\TgMall\Classes\Callbacks\Handler;
use Layerok\TgMall\Classes\Traits\Lang;
use Event;
use Layerok\TgMall\Features\Checkout\Keyboards\SticksKeyboard;
use Layerok\TgMall\Features\Checkout\Messages\OrderDeliveryAddressHandler;
use OFFLINE\Mall\Models\ShippingMethod;

class ChoseDeliveryMethodHandler extends Handler
{
    use Lang;

    protected $name = "chose_delivery_method";


    public function run()
    {
        $id = $this->arguments['id'];
        $this->getState()->setOrderInfoDeliveryMethodId($id);

        $method = ShippingMethod::find($id);
        if ($method->code === 'courier') {
            // доставка курьером
            $this->sendMessage([
                'text' => self::lang('texts.type_delivery_address'),
            ]);
            $this->getState()->setMessageHandler(OrderDeliveryAddressHandler::class);

            return;
        } else if($method->code === 'pickup') {
            $k = new SticksKeyboard();
            // был выбран самовывоз
            $this->sendMessage([
                'text' => self::lang('texts.add_sticks_question'),
                'reply_markup' => $k->getKeyboard()
            ]);
            $this->getState()->setMessageHandler(null);

            return;
        }

        CallbackQueryBus::instance()->make('wish_to_leave_comment', []);
        
    }
}
