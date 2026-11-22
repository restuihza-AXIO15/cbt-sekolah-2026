<?php

namespace App\Providers;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;
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
