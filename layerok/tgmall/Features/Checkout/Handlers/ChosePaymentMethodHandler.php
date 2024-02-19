<?php

namespace Layerok\TgMall\Features\Checkout\Handlers;

use Layerok\TgMall\Classes\Callbacks\Handler;
use Layerok\TgMall\Facades\EmojisushiApi;
use Telegram\Bot\Keyboard\Keyboard;

class ChosePaymentMethodHandler extends Handler
{
    protected string $name = "chose_payment_method";

    public function run()
    {
        $id = $this->arguments['id'];

        $this->user->state->order->payment_method_id = $id;
        $this->user->save();

        $method = EmojisushiApi::getPaymentMethod(['id' => $id]);

        if ($method->code == 'cash') {
            // наличными

            $this->replyWithMessage([
                'text' => \Lang::get('layerok.tgmall::lang.telegram.texts.prepare_change_question'),
                'reply_markup' => (new Keyboard())->inline()->row([
                    Keyboard::inlineButton([
                        'text' => 'Так',
                        'callback_data' => json_encode([
                            'prepare_payment_change',
                            []
                        ])
                    ]),
                    Keyboard::inlineButton([
                        'text' => 'Ні',
                        'callback_data' => json_encode([
                            'list_delivery_methods',
                            []
                        ])
                    ])
                ])
            ]);


            $this->user->state->message_handler = null;
            $this->user->save();

            return;
        }

        $handler = new ListDeliveryMethodsHandler($this->user, $this->getApi());
        $handler->make($this->update, []);
    }
}
