<?php

namespace App\Features\Index;

use App\Classes\Callbacks\Handler;
use App\Facades\EmojisushiApi;
use Illuminate\Support\Str;


class ChangeCityHandler extends Handler
{
    protected string $name = "change_city";

    public function run()
    {
        $id = $this->arguments[0];

        $this->user->state->city_id = $id;
        $this->user->state->session = Str::random(100);
        $this->user->save();

        $from = $this->getUpdate()->getMessage()
            ->getChat();



        $city = EmojisushiApi::getCity([
            'slug_or_id' => $id
        ]);

        $response = $this->replyWithMessage([
            'chat_id' => $from->id,
            'text' => sprintf(
                '%s:%s',
                \Lang::get('lang.telegram.cities.changed'),
                $city->name
            )
        ]);

        $this->api->pinChatMessage([
            'chat_id' => $from->id,
            'message_id' => $response->messageId
        ]);


        $text = sprintf(
            \Lang::get('lang.telegram.texts.welcome'),
            $from->getFirstName()
        );

        $markup = new MainMenuKeyboard();

        $this->replyWithMessage([
            'text' => $text,
            'reply_markup' => $markup->getKeyboard()
        ]);
    }
}
