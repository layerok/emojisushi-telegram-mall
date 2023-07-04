<?php namespace Layerok\TgMall\Classes\Messages;

use Layerok\TgMall\Classes\Traits\Lang;
use Layerok\TgMall\Models\State;
use OFFLINE\Mall\Models\Cart;
use Telegram\Bot\Api;
use Telegram\Bot\Objects\Update;

abstract class AbstractMessageHandler implements MessageHandlerInterface
{
    use Lang;

    protected Update $update;

    protected Api $api;

    /** @var State */
    protected $state;

    protected $chat;

    protected $text;

    /** @var Cart */
    protected $cart;

    public function validate(): bool {
        return true;
    }

    public function handleErrors(): void { }

    public function __construct(Api $api, Update $update, State $state) {
        $this->update = $update;
        $this->api = $api;
        $this->state = $state;

        $this->chat = $this->update->getChat();
        $this->text = $this->update->getMessage()->text;

        $this->cart = Cart::bySession();
    }


    public function start()
    {
        $isValid = $this->validate();

        if (!$isValid) {
            $this->handleErrors();
            return;
        }

        $this->handle();

    }

    public function getChatId()
    {
        return $this->getTelegramUser()->chat_id;
    }

    public function getTelegramUser() {
        return $this->state->user;
    }

    public function getCart(): Cart
    {
        return $this->cart;
    }

    public function sendMessage($params) {
        $this->api->sendMessage(
            array_merge($params, ['chat_id' => $this->getChatId()])
        );
    }

    abstract public function handle();

}
