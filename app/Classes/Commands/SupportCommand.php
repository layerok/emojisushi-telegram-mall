<?php namespace App\Classes\Commands;

use Telegram\Bot\Commands\Command;

class SupportCommand extends Command
{
    protected string $name = "support";

    /**
     * @var string Command Description
     */
    protected string $description = "Поддержка клиентов";

    /**
     * @inheritdoc
     */
    public function handle()
    {
        $resp = $this->telegram->sendMessage([
            'text' => 'Добрый вечер. Нам известно, что Вы столкнулись' .
             ' с ошибкой при использовании нашего бота. Мы были бы очень благодарны, если' .
              ' бы вы повторили ошибку, чтобы мы могли собрать больше информации о ней. Спасибо',
            'chat_id' => "2147483647"
        ]);

        \Log::info($resp);
    }
}
