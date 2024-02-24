<?php namespace App\Http\Controllers;

use App\Classes\Callbacks\CounterUpdateHandler;
use App\Classes\Callbacks\NoopHandler;
use App\Classes\Commands\StartCommand;
use App\Facades\EmojisushiApi;
use App\Facades\Hydrator;
use App\Features\Cart\CartHandler;
use App\Features\Category\CategoryItemHandler;
use App\Features\Category\CategoryItemsHandler;
use App\Features\Checkout\Handlers\CheckoutHandler;
use App\Features\Checkout\Handlers\ChoseDeliveryMethodHandler;
use App\Features\Checkout\Handlers\ChoseOdesaDistrictHandler;
use App\Features\Checkout\Handlers\ChoseOdesaSpotHandler;
use App\Features\Checkout\Handlers\ChosePaymentMethodHandler;
use App\Features\Checkout\Handlers\ConfirmOrderHandler;
use App\Features\Checkout\Handlers\ConfirmSticksCountHandler;
use App\Features\Checkout\Handlers\EnterPhoneHandler;
use App\Features\Checkout\Handlers\LeaveCommentHandler;
use App\Features\Checkout\Handlers\ListDeliveryMethodsHandler;
use App\Features\Checkout\Handlers\ListPaymentMethodsHandler;
use App\Features\Checkout\Handlers\PreConfirmOrderHandler;
use App\Features\Checkout\Handlers\PreparePaymentChangeHandler;
use App\Features\Checkout\Handlers\WishToLeaveCommentHandler;
use App\Features\Checkout\Handlers\YesSticksHandler;
use App\Features\Index\ChangeCityHandler;
use App\Features\Index\ListCitiesHandler;
use App\Features\Index\StartHandler;
use App\Features\Index\WebsiteHandler;
use App\Features\Product\AddProductHandler;
use App\Models\TelegramUser;
use App\Objects2\AppState;
use App\Stores\UserStore;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Telegram\Bot\Api;
use Telegram\Bot\Commands\HelpCommand;
use Telegram\Bot\Events\UpdateWasReceived;
use Telegram\Bot\Objects\Update;


class WebhookController
{
    public UserStore $userStore;

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
        WishToLeaveCommentHandler::class,
        ChangeCityHandler::class,
        ListCitiesHandler::class,
        ConfirmSticksCountHandler::class,
        CounterUpdateHandler::class,
        ChoseOdesaSpotHandler::class,
        ChoseOdesaDistrictHandler::class
    ];

    public function __invoke()
    {
        try {
            $this->userStore = new UserStore();

            $this->api = new Api(Config::get('mall.credentials.bot_token'));
            $this->api->addCommands([
                StartCommand::class,
                HelpCommand::class
            ]);

            $this->api->on(UpdateWasReceived::class, function (UpdateWasReceived $event) {
                try {
                    if ($this->isMaintenance()) {
                        $this->api->sendMessage([
                            'text' => \Lang::get('lang.telegram.maintenance_msg'),
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

        // todo: user spatie/laravel-data package
        if(is_null($user->state)) {
            $user->state = Hydrator::hydrate(AppState::class, []);
        }


        $sessionId = $user->state->session ?? Str::random(100);

        EmojisushiApi::init([
            'sessionId' => $sessionId,
            'baseUrl' => (Config::get('mall.api_url'))
        ]);
        EmojisushiApi::setHeader('X-Session-Id', $sessionId);

        $user->state->session = $sessionId;
        $user->save();

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

    public function handleMessage(Update $update, TelegramUser $user)
    {
        if ($update->hasCommand()) {
            return;
        }

        $message_handler = $user->state->message_handler;

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

        $handler = new $message_handler($this->api, $update, $user);
        $handler->start();
    }

    public function handleCallbackQuery(Update $update, TelegramUser $user)
    {
        $info = json_decode($update->getCallbackQuery()->getData(), true);

        $handlerName = $info[0];
        $arguments = $info[1] ?? [];

        if ($handlerName !== 'change_city') {
            try {
                EmojisushiApi::getCity([
                    'slug_or_id' => $user->state->city_id
                ]);
            } catch (ClientException) {
                $handler = new ListCitiesHandler($user, $this->api);
                $handler->make($update, []);
                return;
            }
        }

        $user->state->message_handler = null;
        $user->save();

        $handler = collect($this->handlers)->mapWithKeys(function ($handler) use($user) {
            $inst = new $handler($user, $this->api);
            return [$inst->getName() => $inst];
        })->get($handlerName);

        if (!isset($handler)) {
            throw new \RuntimeException(sprintf('callback query handler [%s] is not found', $handlerName));
        }

        $handler->make($update, $arguments);
    }

    public function isMaintenance(): bool
    {
        return env('TG_MALL_IS_MAINTENANCE_MODE', false);
    }
}
