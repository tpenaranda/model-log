<?php

namespace TPenaranda\ModelLog\Providers;

use Illuminate\Support\ServiceProvider;
use TPenaranda\ModelLog\ModelLogEntry;
use TPenaranda\ModelLog\Commands\CreateLogTableCommand;

class ModelLogServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        if (method_exists($this, 'loadMigrationsFrom')) {
            $this->loadMigrationsFrom(__DIR__ . '/../Migrations');
        }

        if ($this->app->runningInConsole()) {
            $this->commands([CreateLogTableCommand::class]);
        }
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app['tpenaranda-model-log'] = $this->app->singleton(ModelLogEntry::class, function ($app) {
            return new ModelLogEntry;
        });
    }
}
