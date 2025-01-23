<?php

namespace Developervkindia\ModuleGenerator\Providers;

use Illuminate\Support\ServiceProvider;

class ModuleGeneratorProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        // Automatically load migrations
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        $this->loadRoutesFrom(__DIR__.'/../routes/web.php');
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'views');
        // Automatically publish resources
        $this->publishes([
            __DIR__.'/../resources/views' => resource_path('views/vendor/module-generator'),
            __DIR__.'/../config/module-generator.php' => config_path('module-generator.php'),
        ], 'module-generator');
    }
}
