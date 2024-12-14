<?php

namespace App\Providers;

use App\Listeners\DBSqlListener;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        //注册sql执行监听器
        QueryExecuted::class => [
            DBSqlListener::class
        ],
//        'App\Events\Thread' => [
//            'App\Listeners\ThreadListener',
//        ],
        'App\Events\ThreadCreatedEvent' => [
            'App\Listeners\ThreadCreatedListener',
            'App\Listeners\SyncThreadExternalListener',
        ],
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        //
    }
}
