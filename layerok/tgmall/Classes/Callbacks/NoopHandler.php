<?php namespace Layerok\TgMall\Classes\Callbacks;


class NoopHandler extends Handler
{

    protected string $name = "noop";

    public function run()
    {
        return;
    }
}
