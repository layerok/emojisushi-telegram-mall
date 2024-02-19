<?php

namespace Layerok\TgMall\Features\Index;

use Layerok\TgMall\Classes\Callbacks\Handler;


class WebsiteHandler extends Handler
{
    protected string $name = "website";

    public function run()
    {
        $this->replyWithMessage([
            'text' => 'https://emojisushi.com.ua'
        ]);
    }
}
