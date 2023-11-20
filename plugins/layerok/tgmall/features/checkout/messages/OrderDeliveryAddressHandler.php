<?php

namespace Layerok\TgMall\Features\Checkout\Messages;

use Layerok\TgMall\Classes\Messages\AbstractMessageHandler;
use Telegram\Bot\Keyboard\Keyboard;

class OrderDeliveryAddressHandler extends AbstractMessageHandler
{
    public function handle()
    {
        $this->user->state->order->address = $this->text;
        $this->user->save();

        // был выбран самовывоз
        $this->replyWithMessage([
            'text' => \Lang::get('layerok.tgmall::lang.telegram.texts.add_sticks_question'),
            'reply_markup' => (new Keyboard())->inline()->row([
                Keyboard::inlineButton([
                    'text' => \Lang::get('layerok.tgmall::lang.telegram.buttons.yes'),
                    'callback_data' => json_encode([
                        'add_sticks', []
                    ])
                ]),
                Keyboard::inlineButton([
                    'text' => \Lang::get('layerok.tgmall::lang.telegram.buttons.no'),
                    'callback_data' => json_encode([
                        'wish_to_leave_comment', []
                    ])
                ])
            ])
        ]);

        $this->user->state->message_handler = null;
        $this->user->save();
    }
}
