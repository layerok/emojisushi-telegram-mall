<?php

namespace Layerok\TgMall\Classes\Callbacks;

use Layerok\TgMall\Models\State;
use Telegram\Bot\Api;
use Telegram\Bot\Objects\Message;
use Telegram\Bot\Objects\Update;
use \Layerok\TgMall\Models\User;

abstract class Handler implements HandlerInterface
{
    protected Api $api;

    protected User $user;

    protected Update $update;

    protected array $arguments = [];

    abstract public function run();

    public function __construct(User $user, Api $telegram) {
        $this->user = $user;
        $this->api = $telegram;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getArguments(): array
    {
        return $this->arguments;
    }

    public function setArguments(array $arguments): self
    {
        $this->arguments = $arguments;

        return $this;
    }

    public function make(Update $update, $arguments): void
    {
        $this->update = $update;
        $this->arguments = $arguments;

        call_user_func_array([$this, 'run'], array_values($this->getArguments()));
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function getApi(): Api
    {
        return $this->api;
    }

    public function getUpdate(): Update {
        return $this->update;
    }

    public function replyWithMessage($params): Message {
        return $this->api->sendMessage(array_merge([
            'chat_id' => $this->getUpdate()->getChat()->id
        ],$params));
    }

    public function replyWithPhoto($params): Message {
        return $this->api->sendPhoto(array_merge([
            'chat_id' => $this->getUpdate()->getChat()->id
        ],$params));
    }

    public function getState(): State {
        return $this->user->state;
    }
}
