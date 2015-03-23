<?php namespace Dencker\Watchtower;


use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Support\ServiceProvider;

class WatchtowerServiceProvider extends ServiceProvider{
    /**
     * @var Dispatcher
     */
    private $event;
    /**
     * @var Authenticatable
     */
    private $auth;

    function __construct($app, Dispatcher $event, Authenticatable $auth)
    {
        parent::__construct($app);
        $this->event = $event;
        $this->auth = $auth;
    }

    /**
     * Register the service provider.
     *
     * @return void
     */

    public function register()
    {
        $this->event->listen('auth.logout', function(){
            $this->auth->user()->watchtower()->clearSession();
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