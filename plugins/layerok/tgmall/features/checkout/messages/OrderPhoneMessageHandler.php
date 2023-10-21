<?php

namespace Layerok\TgMall\Features\Checkout\Messages;

use Illuminate\Support\Facades\Validator;
use Layerok\TgMall\Classes\Callbacks\CallbackQueryBus;
use Layerok\TgMall\Classes\Messages\AbstractMessageHandler;

class OrderPhoneMessageHandler extends AbstractMessageHandler
{
    protected array $errors;

    public function validate(): bool
    {
        $data = [
            'phone' => $this->text
        ];

        $rules = [
            'phone' => 'required|phoneUa',
        ];

        $messages = [
            'phone.required' => trans('layerok.posterpos::lang.validation.phone.required'),
            'phone.phone_ua' => trans('layerok.posterpos::lang.validation.phone.ua')
        ];

        $validation = Validator::make($data, $rules, $messages);

        if ($validation->fails()) {
            $this->errors = $validation->errors()->get('phone');
            return false;
        }
        return true;
    }

    public function handle()
    {
        $isValid = $this->validate();

        if (!$isValid) {
            $this->handleErrors();
            return;
        }

        $this->state->setOrderInfoPhone($this->text);

        $this->getTelegramUser()->phone = $this->text;
        $this->getTelegramUser()->save();


        CallbackQueryBus::instance()->make('list_payment_methods', [], $this->getTelegramUser(), $this->update, $this->api);

        $this->state->setMessageHandler(null);
    }

    public function handleErrors(): void
    {
        foreach ($this->errors as $error) {
            $this->sendMessage([
                'text' => $error . '. ' . \Lang::get('layerok.tgmall::lang.telegram.texts.try_again')
            ]);
        }
    }
}
