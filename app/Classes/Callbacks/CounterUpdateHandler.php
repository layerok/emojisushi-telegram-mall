<?php

namespace App\Classes\Callbacks;

use App\Classes\Keyboards\CounterKeyboard;

class CounterUpdateHandler extends Handler
{
    protected string $name = "counter_update";

    public function run()
    {
        $count = $this->arguments[0];

        $this->api->editMessageReplyMarkup([
            'message_id' => $this->getUpdate()->getMessage()->message_id,
            'chat_id' => $this->getUpdate()->getChat()->id,
            'reply_markup' => (new CounterKeyboard($count, 'confirm_sticks_count'))->getKeyboard()
        ]);
    }
}
