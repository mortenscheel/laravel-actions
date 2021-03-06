<?php

namespace Lorisleiva\Actions;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Lorisleiva\Actions\Commands\MakeActionCommand;
use Illuminate\Bus\Dispatcher as IlluminateBusDispatcher;
use Illuminate\Contracts\Queue\Factory as QueueFactoryContract;

class ActionServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->app->extend('events', function ($dispatcher, $app) {
            return new EventDispatcherDecorator($dispatcher, $app);
        });

        $this->app->extend(IlluminateBusDispatcher::class, function ($dispatcher, $app) {
            return new BusDispatcher($app, function ($connection = null) use ($app) {
                return $app->make(QueueFactoryContract::class)->connection($connection);
            });
        });

        Route::macro('actions', function ($group) {
            $this->namespace('\App\Actions')->group($group);
        });

        if ($this->app->runningInConsole()) {
            $this->commands([
                MakeActionCommand::class,
            ]);
        }
    }
}
