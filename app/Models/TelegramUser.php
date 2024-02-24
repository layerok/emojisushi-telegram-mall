<?php namespace App\Models;

use App\Casts\AsAppState;
use App\Objects2\AppState;
use Illuminate\Database\Eloquent\Model;

/**
 * @property AppState $state
 */
class TelegramUser extends Model
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
