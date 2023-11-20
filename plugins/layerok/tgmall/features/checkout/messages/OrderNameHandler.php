<?php

namespace Layerok\TgMall\Features\Checkout\Messages;

use Illuminate\Support\Facades\Validator;
use Layerok\TgMall\Classes\Messages\AbstractMessageHandler;
use Layerok\TgMall\Features\Checkout\Handlers\EnterPhoneHandler;
use Telegram\Bot\Keyboard\Keyboard;

class OrderNameHandler extends AbstractMessageHandler
{
    public function handle()
    {
        $validation = Validator::make([
            'firstname' => $this->text
        ], [
            'firstname' => 'required|min:2',
        ], [
            'firstname.required' => "Имя обязательно для заполнения",
            'firstname.min' => "Имя должно содержать минимум :min символа"
        ]);

        if ($validation->fails()) {
            $errors = $validation->errors()->get('firstname');
            foreach ($errors as $error) {
                $this->replyWithMessage([
                    'text' => $error . '. Попробуйте снова.',
                ]);
            }
        }

        $this->user->state->order->first_name = $this->text;
        $this->user->save();

        // todo: remember user name
        if (isset($this->getUser()->phone)) {

            $this->user->state->order->phone = $this->getUser()->phone;
            $this->user->save();


            $this->replyWithMessage([
                'text' => \Lang::get('layerok.tgmall::lang.telegram.texts.right_phone_number') . ' ' . $this->getUser()->phone . '?',
                'reply_markup' => (new Keyboard())->inline()->row([
                    Keyboard::inlineButton([
                        'text' => \Lang::get('layerok.tgmall::lang.telegram.buttons.yes'),
                        'callback_data' => json_encode(['list_payment_methods', []])
                    ]),
                    Keyboard::inlineButton([
                        'text' => \Lang::get('layerok.tgmall::lang.telegram.buttons.no'),
                        'callback_data' => json_encode(['enter_phone', []])
                    ])
                ]),
            ]);

            return;
        }

        $handler = new EnterPhoneHandler($this->getUser(), $this->api);
        $handler->make($this->update, []);
    }


}
