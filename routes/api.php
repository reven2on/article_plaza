<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ArticleController;

/**
 * Custom rate limiter with limit of 10 request per hour.
 */

Route::middleware(['throttle:rate-limiter'])->group(function () {

    Route::resource('articles', ArticleController::class)->only([
        'index', 'show', 'store'
    ]);
    Route::post('/articles/{article}/rate', [ArticleController::class, 'rate'])->name('rate_article');
});
