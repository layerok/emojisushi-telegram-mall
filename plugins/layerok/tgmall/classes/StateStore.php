<?php
namespace Layerok\TgMall\Classes;

use Layerok\TgMall\Models\State;
use Layerok\TgMall\Models\User;

class StateStore {
    public function create(User $user): State {
        return State::create(
            [
                'user_id' => $user->id,
            ]
        );
    }
}
