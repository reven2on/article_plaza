<?php

namespace App\Listeners;

use App\Models\UserView;
use App\Events\ArticleRated;
use App\Services\ArticleService;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class ArticleRatedEventListener
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
     * @param  \App\Events\ArticleRated  $event
     * @return void
     */
    public function handle(ArticleRated $event): void
    {   
        $articleService = new ArticleService();
        $event->article->update(['calculated_rate' => $articleService->calcArticleRate($event->article)]);
    }
}
