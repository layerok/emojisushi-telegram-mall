<?php
namespace Layerok\TgMall\Stores;

use Illuminate\Support\Collection;
use Layerok\TgMall\Models\User as TelegramUser;

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
            'chat_id' => $chat->id
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
}
