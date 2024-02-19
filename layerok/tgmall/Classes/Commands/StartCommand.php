<?php namespace Layerok\TgMall\Classes\Commands;

use GuzzleHttp\Exception\ClientException;
use Layerok\TgMall\Facades\EmojisushiApi;
use Layerok\TgMall\Features\Index\CitiesKeyboard;
use Layerok\TgMall\Features\Index\MainMenuKeyboard;
use Layerok\TgMall\Models\User as TelegramUser;
use Telegram\Bot\Commands\Command;

class StartCommand extends Command
{
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
        /**
         * @var TelegramUser $user
         */
        $user = TelegramUser::where('chat_id', '=', $chat->id)
            ->first();

        try {
            EmojisushiApi::getCity([
                'slug_or_id' => $user->state->city_id
            ]);
        } catch (ClientException $exception) {
            $k = new CitiesKeyboard();
            $this->replyWithMessage([
                'text' => \Lang::get('layerok.tgmall::lang.telegram.cities.choose'),
                'reply_markup' => $k->getKeyboard()
            ]);
            return;
        }

        $update = $this->getUpdate();
        $from = $update->getMessage()
            ->getChat();

        $text = sprintf(
            \Lang::get('layerok.tgmall::lang.telegram.texts.welcome'),
            $from->firstName
        );

        $markup = new MainMenuKeyboard();


        $this->replyWithMessage([
            'text' => $text,
            'reply_markup' => $markup->getKeyboard()
        ]);
    }
}
