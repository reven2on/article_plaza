<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Events\ArticleViewed;
use App\Services\ArticleService;
use App\Http\Resources\ArticleResource;
use App\Http\Requests\Article\ArticleRequest;
use App\Exceptions\RatingAlreadyExistException;
use App\Http\Requests\Article\ArticleRatingRequest;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @group Article management
 *
 * @unauthenticated
 * APIs for managing articles
 */
class ArticleController extends Controller
{
    
    /**
     * Display a listing of the articles.
     *
     * @queryParam searchTerm string the phrase that will be searched. Example: test
     * @queryParam filter[date] string filter articles that have been created within a date range. Example: 2022-06-04,2022-06-05
     * @queryParam filter[categories] string filter articles by one or more categories (comma separated string). Example: minus,pluses
     * @queryParam sort[trending] string sort trenging articles by amount of views, where the date of views can be filtered Example: 2022-06-04,2022-06-05
     * @queryParam sort[popularity] string sort articles by popularity based on amount of views or precise article rating (`rating` or `view`) Example: rating
     * @response 200 scenario="Success" {
     *  "message": "The article has been loaded",
     *  "data": [
     *     {
     *         "id": "1",
     *         "title": "test title",
     *         "body": "test body",
     *         "creation_date": "2022-06-04 14:44:02",
     *         "views": 180,
     *         "rating": 4.1
     *     }
     *   ]
     * }
     * @response 429 scenario="Too many requests" {
     *  "message": "Too many attempts, try again later"
     * }
     */

    public function index(ArticleRequest $request): mixed
    {
        $articles=Article::with('ratings', 'userviews')->withCount('ratings', 'userviews')->withSum('ratings', 'rate')->search($request)
                        ->sort($request)
                        ->filter($request)
                        ->cursor();
        $articleResources=ArticleResource::collection($articles);
        return response()->ok(__('messages.articles_loaded'), $articleResources);
    }

    /**
     * Create and store article in storage.
     *
     * @bodyParam title string title of the article. Example: test title
     * @bodyParam body string body of the article. Example: test body
     * @bodyParam categories string[] category or list of categories of the article. Example: ['beatae',]
     * @response 200 scenario="Success" {
     *  "message": "The article has been created successfully",
     * }
     * @response 429 scenario="Too many requests" {
     *  "message": Too many attempts, try again later"
     * }
     */
    public function store(ArticleRequest $request, ArticleService $articleService): mixed
    {

        
        $article = $articleService->createArticle($request);
        return response()->created(__('messages.article_created'));
    }

    /**
     * Display the specific article .
     *
     * by calling this endpoint also a view will be register by IP address
     *
     * @urlParam id int required ID of the article. Example: 1
     * @response 200 scenario="Success" {
     *  "message": "The article has been created successfully",
     *  "data": {
     *      "id": 2,
     *      "title": "test title",
     *      "body": "test body",
     *      "creation_date": "2022-06-04 15:08:04",
     *      "views": 5,
     *      "rating": 3
     *   }
     * }
     * @response 429 scenario="Too many requests" {
     *  "message": "The article has been loaded",
     * }
     */
    public function show(Article $article, Request $request): mixed
    {
        event(new ArticleViewed($article, $request->ip()));
        return response()->ok(__('messages.article_loaded'), new ArticleResource($article->withCount('ratings', 'userviews')->first()));
    }


    /**
     * Register a rating for an article.
     *
     * every IP address can rate an article just once
     * every IP address can rate total number of 10 per day
     *
     * @bodyParam rate int required rate of article (between `1` to `5`). Example: 4
     * @response 200 scenario="Success" {
     *  "message": "The article has been created successfully",
     *  "data": {
     *      "id": 2,
     *      "title": "test title",
     *      "body": "test body",
     *      "creation_date": "2022-06-04 15:08:04",
     *      "views": 5,
     *      "rating": 3
     *   }
     * }
     * @response 429 scenario="Too many requests" {
     *  "message": "The article has been loaded",
     * }
     * @response 409 scenario="Conflict" {
     *  "message": "The article has been rated before by current IP address",
     * }
     * @response 409 scenario="Conflict" {
     *  "message": "Current IP address have exceeded the daily maximum number of rating articles",
     * }
     */
    public function rate(Article $article, ArticleRatingRequest $request, ArticleService $articleService): mixed
    {
        try {
            $articleService->rateArticle($article, $request);
            return response()->created(__('messages.article_rated'));
        } catch (RatingAlreadyExistException $exception) {
            return $exception->render();
        }
    }
}
