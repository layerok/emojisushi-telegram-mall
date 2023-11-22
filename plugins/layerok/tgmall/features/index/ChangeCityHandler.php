<?php

namespace Layerok\TgMall\Features\Index;

use Layerok\TgMall\Classes\Callbacks\Handler;
use Layerok\TgMall\Facades\EmojisushiApi;


class ChangeCityHandler extends Handler
{
    protected string $name = "change_city";

    public function run()
    {
        $id = $this->arguments[0];

        $this->user->state->city_id = $id;
        $this->user->state->session = str_random(100);
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
                \Lang::get('layerok.tgmall::lang.telegram.cities.changed'),
                $city->name
            )
        ]);

        $this->api->pinChatMessage([
            'chat_id' => $from->id,
            'message_id' => $response->messageId
        ]);


        $text = sprintf(
            \Lang::get('layerok.tgmall::lang.telegram.texts.welcome'),
            $from->getFirstName()
        );

        $markup = new MainMenuKeyboard();

        $this->replyWithMessage([
            'text' => $text,
            'reply_markup' => $markup->getKeyboard()
        ]);
    }
}
