<?php

namespace Sobhansgh\MellatApi;

use Illuminate\Support\ServiceProvider;

class MellatApiServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/hiveweb-mellat-api.php', 'hiveweb-mellat-api');

        $this->app->singleton(MellatClient::class, function ($app) {
            return new MellatClient(
                config('hiveweb-mellat-api.terminal_id'),
                config('hiveweb-mellat-api.username'),
                config('hiveweb-mellat-api.password'),
                config('hiveweb-mellat-api.wsdl'),
                (bool) config('hiveweb-mellat-api.convert_to_rial', true),
                url(config('hiveweb-mellat-api.callback_url'))
            );
        });
    }

    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__.'/../routes/mellat-api.php');

        $this->publishes([
            __DIR__.'/../config/hiveweb-mellat-api.php' => config_path('hiveweb-mellat-api.php'),
        ], 'hiveweb-mellat-api-config');

        $this->publishes([
            __DIR__.'/../database/migrations/2025_01_01_000000_create_mellat_api_logs_table.php'
                => database_path('migrations/'.date('Y_m_d_His').'_create_mellat_api_logs_table.php'),
        ], 'hiveweb-mellat-api-migrations');
    }
}
