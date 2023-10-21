<?php

namespace Layerok\TgMall\Classes\Callbacks;

use Layerok\TgMall\Classes\Traits\Warn;
use Telegram\Bot\Answers\Answerable;
use Telegram\Bot\Api;
use Telegram\Bot\Objects\Update;
use \Layerok\TgMall\Models\User as TelegramUser;

abstract class Handler implements HandlerInterface
{
    use Answerable;
    use Warn;

    protected ?Api $telegram = null;

    protected ?TelegramUser $user;

    /**
     * @var Update
     */
    protected Update $update;

    protected array $arguments = [];

    protected array $errors = [];

    abstract public function run();

    public function validate(): bool
    {
        return true;
    }

    public function onValidationFailed() {
        foreach($this->errors as $error) {
            $this->error($error);
        }
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

    public function make(Api $telegram, Update $update, $arguments): void
    {
        $this->telegram = $telegram;
        $this->update = $update;
        $this->arguments = $arguments;

        $isValid = $this->validate();

        if (!$isValid) {
            $this->onValidationFailed();
            return;
        }

        call_user_func_array([$this, 'run'], array_values($this->getArguments()));
    }


    public function setTelegramUser(TelegramUser $user): Handler
    {
        $this->user = $user;
        return $this;
    }

    public function getTelegramUser(): TelegramUser
    {
        return $this->user;
    }

    public function getState()
    {
        return $this->getTelegramUser()->state;
    }

    public function getChatId()
    {
        $update = $this->telegram->getWebhookUpdate(false);
        $chat = $update->getChat();
        return $chat->id;
    }

    public function deleteMessage($msg_id)
    {
        $this->telegram->deleteMessage([
            'chat_id' => $this->getChatId(),
            'message_id' => $msg_id
        ]);
    }

    public function editMessageText($msg_id, $params = [])
    {
        $base = [
            'message_id' => $msg_id,
            'chat_id' => $this->getChatId()
        ];

        $params = array_merge($base, $params);
        $this->telegram->editMessageText($params);
    }

    public function editMessageReplyMarkup($msg_id, $params = [])
    {
        $base = [
            'message_id' => $msg_id,
            'chat_id' => $this->getChatId()
        ];

        $params = array_merge($base, $params);

        $this->telegram->editMessageReplyMarkup($params);

    }

    /**
     * Id of message that contains `trigger` (inline button) of this handler
     */
    public function getTriggerMessageId()
    {
        return $this->getUpdate()->getMessage()->message_id;
    }

    public function sendMessage($params) {
        $this->telegram->sendMessage(array_merge($params, [
            'chat_id' => $this->user->chat_id
        ]));
    }
}
