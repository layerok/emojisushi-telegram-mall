<?php
namespace Layerok\TgMall\Stores;

use Illuminate\Support\Collection;
use Layerok\TgMall\Models\User as TelegramUser;
use Telegram\Bot\Objects\CallbackQuery;

class UserStore {

    public function findByChat(Collection $chat): ?TelegramUser {
        return TelegramUser::where('chat_id', '=', $chat->id)
            ->first();
    }

    public function createFromChat(Collection $chat): TelegramUser {
        return TelegramUser::create([
            'firstname' => null,
            'lastname' => null,
            'username' => null,
            'chat_id' => $chat->id,
        ]);

    }

    public function updateFromMessage(TelegramUser $user, $message) {
        $from = $message->from;

        return $user->update([
            'firstname' => $from->getFirstName(),
            'lastname' => $from->getLastName(),
            'username' => $from->getUsername(),
        ]);
    }

    /**
     * @param TelegramUser $user
     * @param CallbackQuery $callbackQuery
     * @return bool
     */
    public function updateFromCallbackQuery(TelegramUser $user, $callbackQuery) {

        return $user->update([
            'firstname' => $callbackQuery->from->firstName,
            'lastname' => $callbackQuery->from->lastName,
            'username' => $callbackQuery->from->username,
        ]);
    }
}
