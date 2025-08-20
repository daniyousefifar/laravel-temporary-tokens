<?php

namespace MyDaniel\TemporaryTokens;

use Illuminate\Support\ServiceProvider;
use MyDaniel\TemporaryTokens\Commands\PruneExpired;

/**
 * Service provider for the Laravel Temporary Tokens package.
 */
class TemporaryTokensServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any package services.
     *
     * This method is called after all other service providers have been registered.
     * It's used to publish assets, load migrations, and register commands.
     *
     * @return void
     */
    public function boot(): void
    {
        // Load package migrations
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');

        if ($this->app->runningInConsole()) {
            // Register console commands
            $this->commands([
                PruneExpired::class,
            ]);

            // Make the config file publishable
            $this->publishes([
                __DIR__.'/../config/temporary-tokens.php' => config_path('temporary-tokens.php'),
            ], 'config');

            // Make migrations publishable
            $this->publishes([
                __DIR__.'/../database/migrations/' => database_path('migrations'),
            ], 'migrations');
        }
    }

    /**
     * Register any application services.
     *
     * This method is called before the boot method. It's used to bind
     * services into the service container.
     *
     * @return void
     */
    public function register(): void
    {
        // Merge the package's default config with the user's published config
        $this->mergeConfigFrom(
            __DIR__.'/../config/temporary-tokens.php', 'temporary-tokens'
        );

        // Bind the TokenBuilder to the service container
        $this->app->bind('laravel-temporary-tokens-builder', function () {
            return new TokenBuilder();
        });
    }
}
