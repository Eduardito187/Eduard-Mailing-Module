<?php

namespace Eduard\Mailing;

use Illuminate\Support\ServiceProvider;
use Eduard\Mailing\Events\SendMailIndex;
use Eduard\Mailing\Listeners\AfterSendMailIndex;

class MailingServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        SendMailIndex::class => [
            AfterSendMailIndex::class
        ]
    ];

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        // Register package's services here
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // Load routes, migrations, etc.
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        $this->loadRoutesFrom(__DIR__.'/../routes/web.php');

        // Load API routes
        $this->loadRoutesFrom(__DIR__.'/Http/routes/api.php');
    }
}