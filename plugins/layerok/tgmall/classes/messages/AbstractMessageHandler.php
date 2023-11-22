<?php namespace Layerok\TgMall\Classes\Messages;

use Layerok\TgMall\Models\User;
use Telegram\Bot\Api;
use Telegram\Bot\Objects\Message;
use Telegram\Bot\Objects\Update;

abstract class AbstractMessageHandler implements MessageHandlerInterface
{
    protected Update $update;

    protected Api $api;

    protected User $user;

    protected $chat;

    protected string $text;

    public function __construct(Api $api, Update $update, User $user) {
        $this->update = $update;
        $this->api = $api;
        $this->user = $user;

        $this->chat = $this->update->getChat();
        $this->text = $this->update->getMessage()->text;
    }

    abstract public function handle();

    public function start()
    {
        $this->handle();
    }

    public function replyWithMessage($params): Message {
        return $this->api->sendMessage(
            array_merge(['chat_id' => $this->user->chat_id], $params)
        );
    }
}
