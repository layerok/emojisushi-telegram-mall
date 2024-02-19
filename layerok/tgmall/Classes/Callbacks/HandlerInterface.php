<?php

namespace Layerok\TgMall\Classes\Callbacks;

use Telegram\Bot\Objects\Update;

interface HandlerInterface
{
    public function make(Update $update, $arguments): void;

    public function getArguments(): array;
}
