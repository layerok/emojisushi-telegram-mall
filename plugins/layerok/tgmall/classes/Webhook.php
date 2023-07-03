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
use Layerok\TgMall\Features\Index\SpotsKeyboard;
use Layerok\TgMall\Features\Index\WebsiteHandler;
use Layerok\Tgmall\Features\Product\AddProductHandler;
use Layerok\TgMall\Features\Index\StartHandler;
use Layerok\TgMall\Classes\Commands\StartCommand;
use Layerok\TgMall\Classes\Traits\HasMaintenanceMode;
use Layerok\TgMall\Models\State;
use \Layerok\TgMall\Models\User as TelegramUser;
use Telegram\Bot\Api;
use Telegram\Bot\Commands\HelpCommand;
use Telegram\Bot\Events\UpdateWasReceived;
use Log;
use Telegram\Bot\Exceptions\TelegramResponseException;
use Telegram\Bot\Objects\Update;
use Session;

class Webhook
{
    use HasMaintenanceMode;
    use Lang;

    /** @var TelegramUser */
    public $telegramUser;

    /** @var State */
    public $state;

    public $handlerInfo;

    /** @var Api */
    protected $api;

    public function __construct($botToken)
    {
        $this->api = new Api($botToken);
        $this->api->addCommands([
            StartCommand::class,
            HelpCommand::class
        ]);
        $handlers = [
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

        CallbackQueryBus::instance()
            ->setTelegram($this->api)
            ->setWebhook($this)
            ->addHandlers($handlers);

        $this->api->on(UpdateWasReceived::class, function($event) {
            $this->handleUpdate($event);
        });
        $this->api->commandsHandler(true);
    }

    public function handleUpdate(UpdateWasReceived $event) {
        $update = $event->update;
        $telegram = $event->telegram;

        try {
            $this->telegramUser = $this->createUser($update);

            if(!isset($this->telegramUser)) {
                throw new \RuntimeException("User was not created. We can't go further without this");
            }

            CallbackQueryBus::instance()
                ->setTelegram($telegram)
                ->setTelegramUser($this->telegramUser)
                ->setUpdate($update);


            $this->state = $this->createState();

            if(!$this->state->getSession()) {
                $sessionId = str_random(100);
                $this->state->setSession($sessionId);
            }
            // it is required for the cart to function correctly
            Session::put('cart_session_id', $this->state->getSession());

            if($update->isType('callback_query')) {
                $this->handlerInfo = CallbackQueryBus::instance()->parse($update);
            }

            $is_maintenance = $this->checkMaintenanceMode(
                $this->api,
                $update,
                $this->telegramUser
            );

            if ($is_maintenance) {
                $this->answerCallbackQuery($update);
                return;
            }

            $spot_id = $this->state->getSpotId();
            $spot = Spot::where([
                'id' => $spot_id
            ])->first();

            if(!$spot && $this->handlerInfo[0] !== 'change_spot') {
                $k = new SpotsKeyboard();
                $this->sendMessage([
                    'text' => self::lang('spots.choose'),
                    'reply_markup' => $k->getKeyboard()
                ]);
                $this->answerCallbackQuery($update);
                return;
            }

            if ($update->isType('callback_query')) {

                $this->state->setMessageHandler(null);

                CallbackQueryBus::instance()
                    ->handle();

                $this->answerCallbackQuery($update);

            }

            if ($update->isType('message')) {

                if ($update->hasCommand()) {
                    return;
                }

                $message_handler = $this->state->getMessageHandler();

                if (!isset($message_handler)) {
                    return;
                }

                if (!class_exists($message_handler)) {
                    throw new \RuntimeException('message handler with [' . $message_handler . '] does not exist');
                }

                $handler = new $message_handler($telegram, $update, $this->state);
                $handler->start();
            }
        } catch (\Exception $exception) {
            Log::error('Answered exception' . PHP_EOL . $exception->getMessage() . PHP_EOL . $exception->getTraceAsString());
            $this->sendMessage([
                'text' => 'Вибачте сталася неочікувана помилка'
            ]);
            $this->answerCallbackQuery($update);
        } catch (\Error $error) {
            Log::error('Answered error' . PHP_EOL . $error->getMessage() . PHP_EOL . $error->getTraceAsString());
            $this->sendMessage([
                'text' => 'Вибачте сталася неочікувана помилка'
            ]);
            $this->answerCallbackQuery($update);
        }
    }

    public function createUser(Update $update) {
        $chat = $update->getChat();


        if($update->isType('callback_query')) {
            $from = $update->getCallbackQuery()
                ->getFrom();
        } else if($update->isType('message')) {

            $from = $update->getMessage()
                ->getFrom();
        } else {
            Log::error('Update type is: ' . $update->detectType());
            return null;
        }

        $telegramUser = TelegramUser::where('chat_id', '=', $chat->id)
            ->first();

        if(isset($telegramUser)) {
            return $telegramUser;
        }

        return TelegramUser::create([
            'firstname' => $from->getFirstName(),
            'lastname' => $from->getLastName(),
            'username' => $from->getUsername(),
            'chat_id' => $chat->id
        ]);
    }

    public function createState(): State
    {
        if (isset($this->telegramUser->state)) {
            return $this->telegramUser->state;
        }
        return State::create(
            [
                'user_id' => $this->telegramUser->id,
            ]
        );
    }

    public function answerCallbackQuery($update)
    {
        try {
            if($update->isType('callback_query')) {
                $this->api->answerCallbackQuery([
                    'callback_query_id' => $update->getCallbackQuery()->id,
                ]);
            }
        } catch (\Exception $e) {
            Log::error($e);
        }
    }

    public function sendMessage($params) {
        $this->api->sendMessage(
            array_merge($params, ['chat_id' => $this->getChatId()])
        );
    }

    public function getChatId()
    {
        return $this->telegramUser->chat_id;
    }



}
