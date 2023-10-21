<?php

namespace Layerok\TgMall\Features\Index;

use Layerok\TgMall\Classes\Callbacks\Handler;


class StartHandler extends Handler
{
    protected string $name = "start";

    public function run()
    {
        $update = $this->getUpdate();
        $from = $update->getMessage()
            ->getChat();

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
