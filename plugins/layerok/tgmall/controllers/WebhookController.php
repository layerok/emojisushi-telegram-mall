<?php namespace Layerok\TgMall\Controllers;

use GuzzleHttp\Exception\ClientException;
use Layerok\TgMall\Classes\Callbacks\NoopHandler;
use Layerok\TgMall\Facades\EmojisushiApi;
use Layerok\TgMall\Models\User;
use Layerok\TgMall\Stores\StateStore;
use Layerok\TgMall\Stores\UserStore;
use Layerok\TgMall\Features\Checkout\Handlers\ConfirmOrderHandler;
use Layerok\Tgmall\Features\Cart\CartHandler;
use Layerok\Tgmall\Features\Category\CategoryItemHandler;
use Layerok\Tgmall\Features\Category\CategoryItemsHandler;
use Layerok\TgMall\Features\Checkout\Handlers\CheckoutHandler;
use Layerok\TgMall\Features\Checkout\Handlers\ChoseDeliveryMethodHandler;
use Layerok\TgMall\Features\Checkout\Handlers\ChosePaymentMethodHandler;
use Layerok\TgMall\Features\Checkout\Handlers\EnterPhoneHandler;
use Layerok\TgMall\Features\Checkout\Handlers\LeaveCommentHandler;
use Layerok\TgMall\Features\Checkout\Handlers\ListDeliveryMethodsHandler;
use Layerok\TgMall\Features\Checkout\Handlers\ListPaymentMethodsHandler;
use Layerok\TgMall\Features\Checkout\Handlers\PreConfirmOrderHandler;
use Layerok\TgMall\Features\Checkout\Handlers\PreparePaymentChangeHandler;
use Layerok\TgMall\Features\Checkout\Handlers\UpdateSticksCounterHandler;
use Layerok\TgMall\Features\Checkout\Handlers\WishToLeaveCommentHandler;
use Layerok\TgMall\Features\Checkout\Handlers\YesSticksHandler;
use Layerok\TgMall\Features\Index\ChangeSpotHandler;
use Layerok\TgMall\Features\Index\ListSpotsHandler;
use Layerok\TgMall\Features\Index\WebsiteHandler;
use Layerok\Tgmall\Features\Product\AddProductHandler;
use Layerok\TgMall\Features\Index\StartHandler;
use Layerok\TgMall\Classes\Commands\StartCommand;
use Layerok\TgMall\Models\Settings;

use Telegram\Bot\Api;
use Telegram\Bot\Commands\HelpCommand;
use Telegram\Bot\Events\UpdateWasReceived;
use Log;
use Session;
use Telegram\Bot\Objects\Update;


class WebhookController
{
    public UserStore $userStore;
    public StateStore $stateStore;

    public ?Api $api;

    public array $handlers = [
        StartHandler::class,
        WebsiteHandler::class,
        CategoryItemsHandler::class,
        CategoryItemHandler::class,
        AddProductHandler::class,
        CartHandler::class,
        CheckoutHandler::class,
        NoopHandler::class,
        EnterPhoneHandler::class,
        ChosePaymentMethodHandler::class,
        ChoseDeliveryMethodHandler::class,
        ListPaymentMethodsHandler::class,
        ListDeliveryMethodsHandler::class,
        LeaveCommentHandler::class,
        PreConfirmOrderHandler::class,
        ConfirmOrderHandler::class,
        PreparePaymentChangeHandler::class,
        YesSticksHandler::class,
        UpdateSticksCounterHandler::class,
        WishToLeaveCommentHandler::class,
        ChangeSpotHandler::class,
        ListSpotsHandler::class,
    ];

    public function __invoke()
    {
        try {
            $this->userStore = new UserStore();
            $this->stateStore = new StateStore();

            $this->api = new Api(\Config::get('layerok.tgmall::credentials.bot_token'));
            $this->api->addCommands([
                StartCommand::class,
                HelpCommand::class
            ]);

            $this->api->on(UpdateWasReceived::class, function (UpdateWasReceived $event) {
                try {
                    if ($this->isMaintenance()) {
                        $this->api->sendMessage([
                            'text' => \Lang::get('layerok.tgmall::lang.telegram.maintenance_msg'),
                            'chat_id' => $event->update->getChat()->id
                        ]);
                    } else {
                        $this->handleUpdate($event);
                    }
                }  catch (\Throwable $e) {
                    Log::error($e->getMessage() . PHP_EOL . $e->getTraceAsString());
                }

                if ($event->update->isType('callback_query')) {
                    $this->api->answerCallbackQuery([
                        'callback_query_id' => $event->update->getCallbackQuery()->id,
                    ]);
                }
            });
            $this->api->commandsHandler(true);
        } catch (\Throwable $e) {
            Log::error($e->getMessage() . PHP_EOL . $e->getTraceAsString());
        }

    }

    public function handleUpdate(UpdateWasReceived $event)
    {
        $update = $event->update;

        $user = $this->userStore->findByChat($update->getChat()) ??
            $this->userStore->createFromChat($update->getChat());

        // actualize user information
        switch ($update->detectType()) {
            case "callback_query":
            {
                $this->userStore->updateFromCallbackQuery($user, $update->getCallbackQuery());
                break;
            }
            case "message":
            {
                $this->userStore->updateFromMessage($user, $update->getMessage());
                break;
            }
        }

        $sessionId = !!$user->state->getStateValue('session') ?
            $user->state->getStateValue('session') :
            str_random(100);

        EmojisushiApi::init([
            'sessionId' => $sessionId,
        ]);
        $user->state->setStateValue('session', $sessionId);
        // it is required for the cart to function correctly
        Session::put('cart_session_id', $sessionId);

        switch ($update->detectType()) {
            case "callback_query":
            {
                $this->handleCallbackQuery($update, $user);
                break;
            }
            case "message":
            {
                $this->handleMessage($update, $user);
                break;
            }
        }
    }

    public function handleMessage(Update $update, User $user)
    {
        if ($update->hasCommand()) {
            return;
        }

        $message_handler = $user->state->getStateValue('message_handler');

        if (!isset($message_handler)) {
            return;
        }

        if (!class_exists($message_handler)) {
            throw new \RuntimeException(
                sprintf(
                    'message handler with [%s] does not exist',
                    $message_handler
                )
            );
        }

        $handler = new $message_handler($this->api, $update, $user->state);
        $handler->start();
    }

    public function handleCallbackQuery(Update $update, User $user)
    {
        [$handlerName, $arguments] = json_decode($update->getCallbackQuery()->getData(), true);

        if ($handlerName !== 'change_spot') {
            try {
                EmojisushiApi::getSpot([
                    'slug_or_id' => $user->state->getStateValue('spot_id')
                ]);
            } catch (ClientException) {
                $handler = new ListSpotsHandler($user, $this->api);
                $handler->make($update, []);
                return;
            }
        }

        $user->state->setStateValue('message_handler',null);

        $handler = collect($this->handlers)->mapWithKeys(function ($handler) use($user) {
            $inst = new $handler($user, $this->api);
            return [$inst->getName() => $inst];
        })->get($handlerName);

        if (!isset($handler)) {
            throw new \RuntimeException('Handler [' . $handlerName . '] is not found');
        }

        $handler->make($update, $arguments);
    }

    public function isMaintenance(): bool
    {
        return Settings::get('is_maintenance_mode', env('TG_MALL_IS_MAINTENANCE_MODE', false));
    }
}
