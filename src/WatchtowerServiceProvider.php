<?php namespace Dencker\Watchtower;


use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Support\ServiceProvider;

class WatchtowerServiceProvider extends ServiceProvider{

    /**
     * Register the service provider.
     *
     * @return void
     */

    public function register()
    {
        $this->app['events']->listen('auth.logout', function(){
            $this->app['auth']->user()->watchtower()->clearSession();
        });
    }

    /**
     * Boots the service provider; is done after the registration.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__."/../database/migrations" => base_path('database/migrations')
        ]);
    }
}