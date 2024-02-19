<?php namespace Layerok\TgMall\Models;

use Illuminate\Database\Eloquent\Model;
use Layerok\TgMall\Casts\AsAppState;
use Layerok\TgMall\Objects2\AppState;

/**
 * @property AppState $state
 */
class User extends Model
{
    protected $table = 'telegram_users';

    protected $casts = [
        'state' => AsAppState::class
    ];

    public $fillable = [
        'username',
        'firstname',
        'lastname',
        'chat_id',
        'phone',
        'state'
    ];
}
