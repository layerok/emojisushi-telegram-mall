<?php namespace Layerok\TgMall\Classes\Messages;

use Layerok\TgMall\Models\State;
use Telegram\Bot\Api;
use Telegram\Bot\Objects\Message;
use Telegram\Bot\Objects\Update;

abstract class AbstractMessageHandler implements MessageHandlerInterface
{
    protected Update $update;

    protected Api $api;

    protected State $state;

    protected $chat;

    protected string $text;

    public function __construct(Api $api, Update $update, State $state) {
        $this->update = $update;
        $this->api = $api;
        $this->state = $state;

        $this->chat = $this->update->getChat();
        $this->text = $this->update->getMessage()->text;
    }

    abstract public function handle();

    public function start()
    {
        $this->handle();
    }

    public function getUser() {
        return $this->state->user;
    }

    public function replyWithMessage($params): Message {
        return $this->api->sendMessage(
            array_merge(['chat_id' => $this->getUser()->chat_id], $params)
        );
    }
}
