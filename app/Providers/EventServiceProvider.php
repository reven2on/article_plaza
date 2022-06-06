<?php

namespace App\Providers;

use App\Events\ArticleRated;
use App\Events\ArticleViewed;
use Illuminate\Support\Facades\Event;
use App\Listeners\ArticleRatedEventListener;
use App\Listeners\ArticleViewedEventListener;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        ArticleViewed::class => [
            ArticleViewedEventListener::class,
        ],
        ArticleRated::class => [
            ArticleRatedEventListener::class,
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     *
     * @return bool
     */
    public function shouldDiscoverEvents()
    {
        return false;
    }
}
