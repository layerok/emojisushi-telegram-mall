<?php

namespace Layerok\TgMall\Classes\Middleware;

use Telegram\Bot\Api;
use Telegram\Bot\Objects\Update;

abstract class AbstractMiddleware implements MiddlewareInterface
{
    /**
     * @var Api
     */
    protected $telegram;
    /**
     * @var Update
     */
    protected $update;


    public function make(Api $telegram, Update $update)
    {
        $this->update = $update;
        $this->telegram = $telegram;
        $chat = $update->getChat();
    }

    abstract public function run():bool;
}
