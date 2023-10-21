<?php namespace Layerok\TgMall\Classes\Callbacks;

use October\Rain\Support\Traits\Singleton;
use Telegram\Bot\Api;
use Telegram\Bot\Objects\Update;
use Exception;

class CallbackQueryBus
{
    use Singleton;

    protected array $handlers;

    public function handle($user, Update $update, Api $telegram): void
    {
        [$name, $arguments] = $this->parse($update);

        $this->make($name, $arguments, $user, $update, $telegram);
    }

    public function make($name, $arguments, $user, $update, Api $telegram) {
        $handler = $this->find($name);

        if(!isset($handler)) {
            \Log::error('Handler [' . $name . '] is not found');
            return;
        }
        $handler->setTelegramUser($user);
        $handler->make($telegram, $update, $arguments);
    }

    public function parse(Update $update): array
    {
        $data = $update->getCallbackQuery()
            ->getData();

        return $this->parseCallbackData($data);

    }

    public function find($name)
    {
        return $this->handlers[$name] ??
            collect($this->handlers)->filter(function ($command) use ($name) {
                return $command instanceof $name;
            })->first() ?? null;
    }


    public function addHandlers($handlers) {
        foreach($handlers as $handler) {
            $this->addHandler($handler);
        }
    }

    public function addHandler($handler): void {
        if (!is_object($handler)) {
            if (! class_exists($handler)) {
                throw new Exception(
                    sprintf(
                        'Handler class "%s" not found! Please make sure the class exists.',
                        $handler
                    )
                );
            }

            $handler = new $handler();
        }

        if (! ($handler instanceof HandlerInterface)) {
            throw new Exception(
                sprintf(
                    'Handler class "%s" should be an instance of "Layerok\TgMall\Classes\Callbacks\HandlerInterface"',
                    get_class($handler)
                )
            );
        }

        $this->handlers[$handler->getName()] = $handler;
    }

    public static function parseCallbackData($data): array
    {
        $data = json_decode($data, true);
        try {
            $name = $data[0];
            $arguments = $data[1] ?? [];
            return [$name, $arguments];
        } catch (\ErrorException $e) {
            \Log::error('Cannot parse callback query data. Error happened inside [' . self::class . ']');
            \Log::error($e);
            return ['noop', []];
        }

    }


}
