<?php

namespace Layerok\TgMall\Features\Checkout\Messages;

use Illuminate\Support\Facades\Validator;
use Layerok\TgMall\Classes\Messages\AbstractMessageHandler;
use Layerok\TgMall\Features\Checkout\Handlers\EnterPhoneHandler;
use Layerok\TgMall\Features\Checkout\Keyboards\IsRightPhoneKeyboard;

class OrderNameHandler extends AbstractMessageHandler
{
    protected array $errors;

    public function validate(): bool
    {
        $data = [
            'firstname' => $this->text
        ];

        $rules = [
            'firstname' => 'required|min:2',
        ];

        $messages = [
            'firstname.required' => "Имя обязательно для заполнения",
            'firstname.min' => "Имя должно содержать минимум :min символа"
        ];

        $validation = Validator::make($data, $rules, $messages);

        if ($validation->fails()) {
            $this->errors = $validation->errors()->get('firstname');
            return false;
        }
        return true;
    }

    public function handle()
    {
        // todo: remember user name
        if (isset($this->getTelegramUser()->phone)) {

            $k = new IsRightPhoneKeyboard();
            $this->sendMessage([
                'text' => \Lang::get('layerok.tgmall::lang.telegram.texts.right_phone_number') . ' ' . $this->getTelegramUser()->phone . '?',
                'reply_markup' => $k->getKeyboard(),
            ]);

            return;
        }

        $handler = new EnterPhoneHandler();
        $handler->setTelegramUser($this->getTelegramUser());
        $handler->setTelegram($this->api);
        $handler->make($this->api, $this->update, []);
    }

    public function handleErrors(): void
    {
        foreach ($this->errors as $error) {
            $this->sendMessage([
                'text' => $error . '. Попробуйте снова.',
            ]);
        }
    }
}
