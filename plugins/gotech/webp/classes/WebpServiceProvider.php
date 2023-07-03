<?php namespace GoTech\Webp\Classes;

use Illuminate\Support\ServiceProvider;

class WebpServiceProvider extends ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../config/webp.php' => config_path('webp.php'),
        ], 'config');
    }

    /**
     * Register any package services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('webp', function () {
            return new Webp();
        });
    }
}
