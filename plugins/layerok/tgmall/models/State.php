<?php namespace Layerok\TgMall\Models;

use October\Rain\Database\Model;
use Layerok\TgMall\Models\User as TelegramUser;


class State extends Model
{
    protected $table = 'layerok_tgmall_states';
    protected $primaryKey = 'id';

    protected $jsonable = ['state'];

    public $fillable = [
        'user_id',
        'state',
    ];

    public $belongsTo = [
        'user' => TelegramUser::class
    ];

    public $timestamps = true;

    public function setStateValue($key, $value)
    {
        $this->state = array_merge(
            $this->state ?? [],
            [$key => $value]
        );
        $this->save();
    }

    public function getStateValue($key)
    {
        return $this->state[$key] ?? null;
    }
}
