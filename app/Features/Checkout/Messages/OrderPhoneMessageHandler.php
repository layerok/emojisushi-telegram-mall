<?php

namespace App\Features\Checkout\Messages;

use App\Classes\Messages\AbstractMessageHandler;
use App\Features\Checkout\Handlers\ListPaymentMethodsHandler;
use App\Rules\PhoneUa;
use Illuminate\Support\Facades\Validator;

class OrderPhoneMessageHandler extends AbstractMessageHandler
{
    public function handle()
    {
        $validation = Validator::make([
            'phone' => $this->text
        ], [
            'phone' => ['required', new PhoneUa]
        ], [
            'phone.required' => trans('lang.validation.phone.required'),
        ]);

        if ($validation->fails()) {
            $errors = $validation->errors()->get('phone');
            foreach ($errors as $error) {
                $this->replyWithMessage([
                    'text' => $error . '. ' . \Lang::get('lang.telegram.texts.try_again')
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
