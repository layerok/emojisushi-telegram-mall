<?php

namespace Layerok\TgMall\Features\Checkout\Messages;

use Illuminate\Support\Facades\Validator;
use Layerok\TgMall\Classes\Messages\AbstractMessageHandler;
use Layerok\TgMall\Features\Checkout\Handlers\ListPaymentMethodsHandler;

class OrderPhoneMessageHandler extends AbstractMessageHandler
{
    public function handle()
    {
        $validation = Validator::make([
            'phone' => $this->text
        ], [
            'phone' => 'required|phoneUa',
        ], [
            'phone.required' => trans('layerok.posterpos::lang.validation.phone.required'),
            'phone.phone_ua' => trans('layerok.posterpos::lang.validation.phone.ua')
        ]);

        if ($validation->fails()) {
            $errors = $validation->errors()->get('phone');
            foreach ($errors as $error) {
                $this->replyWithMessage([
                    'text' => $error . '. ' . \Lang::get('layerok.tgmall::lang.telegram.texts.try_again')
                ]);
            }
            return;
        }

        $this->user->state->order->phone = $this->text;
        $this->user->save();

        $this->user->phone = $this->text;
        $this->user->save();

        $handler = new ListPaymentMethodsHandler($this->user, $this->api);
        $handler->make($this->update, []);

        $this->user->state->message_handler = null;
        $this->user->save();
    }


}
