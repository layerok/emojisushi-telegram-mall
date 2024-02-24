<?php namespace App\Classes\Callbacks;


class NoopHandler extends Handler
{

    protected string $name = "noop";

    public function run()
    {
        return;
    }
}
