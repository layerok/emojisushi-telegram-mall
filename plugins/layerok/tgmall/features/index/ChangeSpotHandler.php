<?php

namespace Layerok\TgMall\Features\Index;

use Layerok\TgMall\Classes\Callbacks\Handler;
use Layerok\TgMall\Facades\EmojisushiApi;


class ChangeSpotHandler extends Handler
{
    protected string $name = "change_spot";

    public function run()
    {
        $id = $this->arguments[0];

        $appState = $this->user->state->state;
        $appState->spot_id = $id;
        $this->user->state->state = $appState;
        $this->user->state->save();

        $from = $this->getUpdate()->getMessage()
            ->getChat();

        $spot = EmojisushiApi::getSpot([
            'slug_or_id' => $id
        ]);

        $response = $this->replyWithMessage([
            'chat_id' => $from->id,
            'text' => sprintf(
                '%s:%s',
                \Lang::get('layerok.tgmall::lang.telegram.spots.changed'),
                $spot->name
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
