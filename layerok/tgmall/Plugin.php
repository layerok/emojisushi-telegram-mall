<?php
namespace Layerok\TgMall;

use Illuminate\Support\ServiceProvider;
use Layerok\TgMall\Services\EmojisushiApi;
use Layerok\TgMall\Services\Hydrator;

class Plugin extends ServiceProvider
{

    public function register(): void
    {
        $this->app->singleton('hydrator', function() {
            return new Hydrator();
        });
        $this->app->singleton('emojisushi.api', function() {
            return new EmojisushiApi();
        });

        $this->loadTranslationsFrom(__DIR__.'/lang', 'layerok.tgmall');
        $this->loadRoutesFrom(__DIR__ . '/routes.php');
        $this->loadMigrationsFrom(__DIR__ . '/updates');

    }

}
