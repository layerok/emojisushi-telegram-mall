<?php

namespace App\Features\Checkout\Messages;

use App\Classes\Messages\AbstractMessageHandler;
use App\Features\Checkout\Handlers\EnterPhoneHandler;
use Illuminate\Support\Facades\Validator;
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
        if (isset($this->user->phone)) {

            $this->user->state->order->phone = $this->user->phone;
            $this->user->save();

            $this->replyWithMessage([
                'text' => \Lang::get('lang.telegram.texts.right_phone_number') . ' ' . $this->user->phone . '?',
                'reply_markup' => (new Keyboard())->inline()->row([
                    Keyboard::inlineButton([
                        'text' => \Lang::get('lang.telegram.buttons.yes'),
                        'callback_data' => json_encode(['list_payment_methods', []])
                    ]),
                    Keyboard::inlineButton([
                        'text' => \Lang::get('lang.telegram.buttons.no'),
                        'callback_data' => json_encode(['enter_phone', []])
                    ])
                ]),
            ]);

            return;
        }

        $handler = new EnterPhoneHandler($this->user, $this->api);
        $handler->make($this->update, []);
    }
}
