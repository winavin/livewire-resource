<?php

namespace Winavin\LivewireResource;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Winavin\LivewireResource\Console\Commands\DeleteLivewireResource;
use Winavin\LivewireResource\Console\Commands\MakeLivewireResource;
use Winavin\LivewireResource\Routing\LivewireResourceRoute;

class LivewireResourceServiceProvider extends ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot(): void
    {
        $this->publishes([
                             __DIR__.'/../config/courier.php' => config_path('courier.php'),
                         ], 'livewire-resource.config');

        if ($this->app->runningInConsole()) {
            $this->bootForConsole();
        }

        Route::macro('livewireResource', function ( string $name, ?string $componentBase = null, array $options = [] )
        {
            return new LivewireResourceRoute($name, $componentBase, $options);
        });
    }

    /**
     * Register any package services.
     *
     * @return void
     */
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/livewire-resource.php', 'livewire-resource');
    }

    /**
     * Console-specific booting.
     *
     * @return void
     */
    protected function bootForConsole(): void
    {
        $this->commands([
                            DeleteLivewireResource::class,
                            MakeLivewireResource::class
                        ]);
    }
}
