<?php
namespace Layerok\TgMall;

use Layerok\TgMall\Classes\Boot\Events;

use Layerok\TgMall\Services\EmojisushiApi;
use System\Classes\PluginBase;


class Plugin extends PluginBase
{
    public $require = ['OFFLINE.Mall', 'Layerok.BaseCode'];

    public function boot() {
        Events::boot();
    }

    public function register()
    {
        $this->registerConsoleCommand('create:tg.mall.handler', \Layerok\TgMall\Console\CreateCallbackHandler::class);
        $this->registerConsoleCommand('create:tg.mall.keyboard', \Layerok\TgMall\Console\CreateKeyboard::class);

        $this->app->singleton('emojisushi.api', function() {
            return new EmojisushiApi();
        });

    }

    public function registerSettings()
    {
        return [
            'settings' => [
                'label' => 'EmojiSushiBot settings',
                'description' => 'Manage bot settings.',
                'category' => 'Telegram',
                'icon' => 'icon-cog',
                'class' => \Layerok\TgMall\Models\Settings::class,
                'order' => 500,
                'keywords' => 'telegram bot',
            ]
        ];
    }


}
