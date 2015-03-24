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
            $user = $this->app['auth']->user();

            if( $user && method_exists($user, 'watchtower') )
                $user->watchtower()->clearSession();
        });

        $this->app->bind('watchtower', 'Dencker\Watchtower\Watchtower');
    }

    /**
     * Boots the service provider; is done after the registration.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__."/../database/migrations" => base_path('database/migrations'),
            __DIR__."/../config/watchtower.php" => config_path('watchtower.php')
        ]);
    }
}