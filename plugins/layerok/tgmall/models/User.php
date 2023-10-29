<?php namespace Layerok\TgMall\Models;

use October\Rain\Database\Model;

/**
 * @property State $state
 */
class User extends Model
{
    protected $table = 'layerok_tgmall_users';
    protected $primaryKey = 'id';

    public $fillable = [
        'username',
        'firstname',
        'lastname',
        'chat_id',
        'phone'
    ];

    public $hasOne = [
        'state' => State::class,
    ];
}
