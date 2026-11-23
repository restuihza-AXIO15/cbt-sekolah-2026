<?php

namespace App\Providers;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Force HTTPS in production / Railway proxy to prevent "Form is not secure" mixed content warnings
        if (app()->environment('production') || 
            (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') ||
            str_contains(config('app.url'), 'https://') ||
            (isset($_SERVER['HTTP_HOST']) && str_contains($_SERVER['HTTP_HOST'], 'railway.app'))) {
            URL::forceScheme('https');
        }

        // Auto-migrate & seed database on cloud deployment (Railway/Render) if table does not exist yet
        try {
            if (config('database.default') === 'sqlite') {
                $dbPath = config('database.connections.sqlite.database');
                if ($dbPath && !file_exists($dbPath) && $dbPath !== ':memory:') {
                    @mkdir(dirname($dbPath), 0777, true);
                    @touch($dbPath);
                }
            }

            if (!Schema::hasTable('users')) {
                Artisan::call('migrate', ['--force' => true]);
                Artisan::call('db:seed', ['--force' => true]);
            }
        } catch (\Throwable $e) {
            // Log or ignore if already running in migration context
        }
    }
}
