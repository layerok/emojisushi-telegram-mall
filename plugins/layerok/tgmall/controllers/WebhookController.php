<?php namespace Layerok\TgMall\Controllers;

use Layerok\TgMall\Classes\Callbacks\CallbackQueryBus;
use Layerok\TgMall\Classes\Callbacks\NoopHandler;
use Layerok\TgMall\Facades\EmojisushiApi;
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

class WebhookController
{
    public UserStore $userStore;
    public StateStore $stateStore;

    protected ?Api $api;

    public function __invoke()
    {
        $bot_token = \Config::get('layerok.tgmall::credentials.bot_token');
        $this->api = new Api($bot_token);
        $this->api->addCommands([
            StartCommand::class,
            HelpCommand::class
        ]);
        $this->userStore = new UserStore();
        $this->stateStore = new StateStore();

        CallbackQueryBus::instance()
            ->addHandlers([
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
            ]);

        $this->api->on(UpdateWasReceived::class, function($event) {
            $this->handleUpdate($event);
        });
        try {
            $this->api->commandsHandler(true);
        } catch (\Exception $exception) {
            Log::error($exception->getMessage() . $exception->getTraceAsString());
        }
    }

    public function handleUpdate(UpdateWasReceived $event) {
        $update = $event->update;

        try {
            $user = $this->userStore->findByChat($update->getChat()) ??
                $this->userStore->createFromChat($update->getChat());

            if(!$user->state) {
                $user->state = $this->stateStore->create($user);
            }

            $sessionId = $user->state->hasSession() ?
                $user->state->getSession() :
                str_random(100);

            EmojisushiApi::init([
                'sessionId' => $sessionId,
            ]);
            $user->state->setSession($sessionId);
            // it is required for the cart to function correctly
            Session::put('cart_session_id', $sessionId);


            if($this->isMaintenance()) {
                $this->api->sendMessage([
                    'text' =>  \Lang::get('layerok.tgmall::lang.telegram.maintenance_msg'),
                    'chat_id' => $user->chat_id
                ]);
                if($update->isType('callback_query')) {
                    $this->api->answerCallbackQuery([
                        'callback_query_id' => $update->getCallbackQuery()->id,
                    ]);
                }
                return;
            }


            if($update->isType('callback_query')) {
                $this->userStore->updateFromCallbackQuery($user, $update->getCallbackQuery());
                $handlerInfo = CallbackQueryBus::instance()->parse($update);

                $spot = EmojisushiApi::getSpot([
                    'slug_or_id' => $user->state->getSpotId()
                ]);

                if(!$spot && $handlerInfo[0] !== 'change_spot') {
                    CallbackQueryBus::instance()->make(
                        'list_spots',
                        [],
                        $user,
                        $update, $this->api
                    );
                } else {
                    $user->state->setMessageHandler(null);

                    CallbackQueryBus::instance()
                        ->handle($user, $update, $this->api);
                }

                $this->api->answerCallbackQuery([
                    'callback_query_id' => $update->getCallbackQuery()->id,
                ]);


            } else if($update->isType('message')) {
                $this->userStore->updateFromMessage($user, $update->getMessage());
                if ($update->hasCommand()) {
                    return;
                }

                $message_handler = $user->state->getMessageHandler();

                if (!isset($message_handler)) {
                    return;
                }

                if (!class_exists($message_handler)) {
                    throw new \RuntimeException('message handler with [' . $message_handler . '] does not exist');
                }

                $handler = new $message_handler($event->telegram, $update, $user->state);
                $handler->start();
            } else {
               // do nothing
            }

        } catch (\Exception $exception) {
            if($update->isType('callback_query')) {
                try {
                    $this->api->answerCallbackQuery([
                        'callback_query_id' => $update->getCallbackQuery()->id,
                    ]);
                } catch (\Exception $e) {
                    Log::error($e);
                }
            }
            Log::error($exception->getMessage() . PHP_EOL . $exception->getTraceAsString());

        } catch (\Error $error) {

            if($update->isType('callback_query')) {
                try {
                    $this->api->answerCallbackQuery([
                        'callback_query_id' => $update->getCallbackQuery()->id,
                    ]);
                } catch (\Exception $e) {
                    Log::error($e);
                }
            }
            Log::error($error->getMessage() . PHP_EOL . $error->getTraceAsString());
        }
    }

    public function isMaintenance(): bool {
        return Settings::get('is_maintenance_mode', env('TG_MALL_IS_MAINTENANCE_MODE', false));
    }

}
