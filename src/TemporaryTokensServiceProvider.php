<?php

namespace Zirsakht\TemporaryTokens;

use Illuminate\Support\ServiceProvider;
use Zirsakht\TemporaryTokens\Console\Commands\PruneExpired;

class TemporaryTokensServiceProvider extends ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->loadMigrationsFrom(__DIR__ . '/../database/migrations/');

            $this->commands([
                PruneExpired::class,
            ]);
        }

        if (!class_exists('CreateTemporaryTokensTable')) {
            $timestamp = date('Y_m_d_His', time());

            $this->publishes([
                __DIR__ . '/../database/migrations/create_temporary_tokens_table.php.stub' => database_path("migrations/{$timestamp}_create_temporary_tokens_table.php")
            ]);
        }
    }

    /**
     * Register any package services.
     *
     * @return void
     */
    public function register(): void
    {
        /**
         * Bind to service container.
         */
        $this->app->bind('laravel-temporary-tokens-builder', function () {
            return new TokenBuilder();
        });
    }
}
