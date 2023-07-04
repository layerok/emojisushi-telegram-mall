<?php namespace Layerok\TgMall\Classes;

use Layerok\PosterPos\Models\Spot;
use Layerok\TgMall\Classes\Callbacks\CallbackQueryBus;
use Layerok\TgMall\Classes\Callbacks\NoopHandler;
use Layerok\TgMall\Classes\Traits\Lang;
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
use \Layerok\TgMall\Models\User as TelegramUser;

use Telegram\Bot\Api;
use Telegram\Bot\Commands\HelpCommand;
use Telegram\Bot\Events\UpdateWasReceived;
use Log;
use Session;

class Webhook
{
    use Lang;

    public ?TelegramUser $user;

    public UserStore $userStore;
    public StateStore $stateStore;

    protected ?Api $api;

    public function __construct($botToken)
    {
        $this->api = new Api($botToken);
        $this->api->addCommands([
            StartCommand::class,
            HelpCommand::class
        ]);
        $this->userStore = new UserStore();
        $this->stateStore = new StateStore();

        CallbackQueryBus::instance()
            ->setTelegram($this->api)
            ->setWebhook($this)
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
        $telegram = $event->telegram;

        try {
            $chat = $update->getChat();
            $this->user = $this->userStore->findByChat($chat);

            if(!$this->user) {
                $this->user = $this->userStore->createFromChat($chat);
            }

            if(!$this->user->state) {
                $this->user->state = $this->stateStore->create($this->user);
            }

            if(!$this->user->state->hasSession()) {
                $sessionId = str_random(100);
                $this->user->state->setSession($sessionId);
            }
            // it is required for the cart to function correctly
            Session::put('cart_session_id', $this->user->state->getSession());

            $message = $update->getMessage();

            if(isset($message->from)) {
                $this->userStore->updateFromMessage($this->user, $message);
            }

            if(Settings::get('is_maintenance_mode', env('TG_MALL_IS_MAINTENANCE_MODE', false))) {
                $this->api->sendMessage([
                    'text' =>  'Просимо вибачення. Над ботом тимчасово ведуться технічні роботи. Поки що Ви можете скористатися нашим сайтом https://emojisushi.com.ua',
                    'chat_id' => $this->user->chat_id
                ]);
                if($update->isType('callback_query')) {
                    $this->api->answerCallbackQuery([
                        'callback_query_id' => $update->getCallbackQuery()->id,
                    ]);
                }
                return;
            }

            CallbackQueryBus::instance()
                ->setTelegram($telegram)
                ->setTelegramUser($this->user)
                ->setUpdate($update);

            if($update->isType('callback_query')) {
                $handlerInfo = CallbackQueryBus::instance()->parse($update);

                $spot = Spot::where([
                    'id' => $this->user->state->getSpotId()
                ])->first();

                if(!$spot && $handlerInfo[0] !== 'change_spot') {
                    CallbackQueryBus::instance()->make('list_spots', []);
                } else {
                    $this->user->state->setMessageHandler(null);

                    CallbackQueryBus::instance()
                        ->handle();
                }

                $this->api->answerCallbackQuery([
                    'callback_query_id' => $update->getCallbackQuery()->id,
                ]);


            } else if($update->isType('message')) {

                if ($update->hasCommand()) {
                    return;
                }

                $message_handler = $this->user->state->getMessageHandler();

                if (!isset($message_handler)) {
                    return;
                }

                if (!class_exists($message_handler)) {
                    throw new \RuntimeException('message handler with [' . $message_handler . '] does not exist');
                }

                $handler = new $message_handler($telegram, $update, $this->user->state);
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
            if($exception->getCode() === 403) {
                Log::error('bot is blocked');
            } else {
                Log::error($update->getMessage() . ' ' . $exception->getMessage() . PHP_EOL . $exception->getTraceAsString());
            }

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
            if($error->getCode() === 403) {
                Log::error('bot is blocked');
            } else {
                Log::error($error->getMessage() . PHP_EOL . $error->getTraceAsString());
            }

        }
    }

}
