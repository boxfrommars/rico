<?php
/**
 * @author Dmitry Groza <boxfrommars@gmail.com>
 */

namespace Rico\Notifications;

use Illuminate\Support\ServiceProvider;

class FlashServiceProvider extends ServiceProvider
{

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bindShared('flash', function () {
            return $this->app->make('Rico\Notifications\FlashNotifier');
        });
    }
}
