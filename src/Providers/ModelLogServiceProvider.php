<?php

namespace TPenaranda\ModelLog\Providers;

use Illuminate\Support\ServiceProvider;
use TPenaranda\ModelLog\{ModelLog, Commands\CreateLogTableCommand};

class ModelLogServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
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
        $this->app['tpenaranda-model-log'] = $this->app->singleton(ModelLog::class, function($app) {
            return new ModelLog;
        });
    }
}
