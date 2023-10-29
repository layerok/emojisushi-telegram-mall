<?php namespace Layerok\TgMall\Models;

use Layerok\TgMall\Casts\AsAppState;
use Layerok\TgMall\Objects2\AppState;
use October\Rain\Database\Model;
use Layerok\TgMall\Models\User as TelegramUser;

/**
 * @property AppState $state
 */
class State extends Model
{
    protected $table = 'layerok_tgmall_states';
    protected $primaryKey = 'id';

    protected $casts = [
        'state' => AsAppState::class
    ];

    public $fillable = [
        'user_id',
        'state',
    ];

    public $belongsTo = [
        'user' => TelegramUser::class
    ];

    public $timestamps = true;
}
