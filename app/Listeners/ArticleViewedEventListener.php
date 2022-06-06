<?php

namespace App\Listeners;

use App\Events\ArticleViewed;
use App\Models\UserView;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class ArticleViewedEventListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  \App\Events\ArticleViewed  $event
     * @return void
     */
    public function handle(ArticleViewed $event): void
    {
        $userView = $event->article->userViews()->create([
            'ipaddress' => $event->ipAddress,
        ]);
    }
}
