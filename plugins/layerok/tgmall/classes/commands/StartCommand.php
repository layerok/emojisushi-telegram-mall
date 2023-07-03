<?php namespace Layerok\TgMall\Classes\Commands;

use Layerok\PosterPos\Models\Spot;
use Layerok\TgMall\Features\Index\MainMenuKeyboard;
use Layerok\TgMall\Classes\Traits\Lang;
use Layerok\TgMall\Features\Index\SpotsKeyboard;
use Layerok\TgMall\Models\User as TelegramUser;
use Telegram\Bot\Commands\Command;

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
        $telegramUser = TelegramUser::where('chat_id', '=', $chat->id)
            ->first();

        $spot_id = $telegramUser->state->getSpotId();
        $spot = Spot::where([
            'id' => $spot_id
        ])->first();

        if(!$spot) {
            $k = new SpotsKeyboard();
            $this->replyWithMessage([
                'text' => self::lang('spots.choose'),
                'reply_markup' => $k->getKeyboard()
            ]);
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
