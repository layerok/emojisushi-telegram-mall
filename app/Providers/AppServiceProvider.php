<?php

namespace App\Providers;

use App\Services\EmojisushiApi;
use App\Services\Hydrator;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('hydrator', function() {
            return new Hydrator();
        });
        $this->app->singleton('emojisushi.api', function() {
            return new EmojisushiApi();
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
