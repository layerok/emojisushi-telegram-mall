<?php

namespace App\Features\Index;

use App\Classes\Callbacks\Handler;


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
