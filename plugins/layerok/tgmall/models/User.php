<?php namespace Layerok\TgMall\Models;

use Layerok\TgMall\Casts\AsAppState;
use Layerok\TgMall\Objects2\AppState;
use October\Rain\Database\Model;

/**
 * @property AppState $state
 */
class User extends Model
{
    protected $table = 'layerok_tgmall_users';
    protected $primaryKey = 'id';

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
