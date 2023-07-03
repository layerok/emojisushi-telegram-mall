<?php namespace Layerok\TgMall\Classes\Commands;

use Layerok\TgMall\Features\Index\MainMenuKeyboard;
use Layerok\TgMall\Classes\Traits\Lang;
use Telegram\Bot\Commands\Command;
use Event;

class StartCommand extends Command
{
    use Lang;

    protected string $name = "start";

    /**
     * @var string Command Description
     */
    protected string $description = "Команда для начала работы";

    /**
     * @inheritdoc
     */
    public function handle()
    {
        $update = $this->getUpdate();
        $message = $update->getMessage();
        $chat = $message->getChat();

        if(!$this->hasSpot($chat->id)) {
            // if user haven't selected spot, we will display a list of spots to him
            return;
        }

        $update = $this->getUpdate();
        $from = $update->getMessage()
            ->getChat();

        $text = sprintf(
            self::lang('texts.welcome'),
            $from->firstName
        );

        $markup = new MainMenuKeyboard();

        $this->replyWithMessage([
            'text' => $text,
            'reply_markup' => $markup->getKeyboard()
        ]);
    }
}
