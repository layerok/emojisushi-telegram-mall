<?php

namespace Layerok\TgMall\Features\Index;

use Layerok\TgMall\Classes\Callbacks\Handler;
use Layerok\TgMall\Classes\Traits\Lang;
use Layerok\TgMall\Facades\EmojisushiApi;


class ChangeSpotHandler extends Handler
{
    use Lang;

    protected string $name = "change_spot";

    public function run()
    {
        $id = $this->arguments[0];
        $this->getState()->setSpotId($id);
        $update = $this->getUpdate();
        $from = $update->getMessage()
            ->getChat();

        $spot = EmojisushiApi::getSpot([
            'slug_or_id' => $id
        ]);

        $response = $this->replyWithMessage([
            'chat_id' => $from->id,
            'text' => self::lang('spots.changed') . ': ' . $spot['name']
        ]);

        $this->telegram->pinChatMessage([
            'chat_id' => $from->id,
            'message_id' => $response['message_id']
        ]);


        $text = sprintf(
            self::lang('texts.welcome'),
            $from->getFirstName()
        );

        $markup = new MainMenuKeyboard();

        $this->replyWithMessage([
            'text' => $text,
            'reply_markup' => $markup->getKeyboard()
        ]);
    }
}
