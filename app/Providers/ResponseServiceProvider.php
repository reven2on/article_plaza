<?php

namespace App\Providers;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Response;

class ResponseServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {

        Response::macro('ok', function ($message, $data) {
            return Response::make(['message' => $message, 'data' => $data], 200);
        });

        Response::macro('created', function ($message) {
            return Response::make(compact('message'), 201);
        });

        Response::macro('unprocessableEntity', function ($message, $data) {
            return Response::make(['message' => $message, 'data' => $data], 422);
        });

        Response::macro('tooManyRequests', function ($message) {
            return Response::make(compact('message'), 429);
        });

        Response::macro('conflict', function ($message) {
            return Response::make(compact('message'), 409);
        });

        Response::macro('notfound', function ($message) {
            return Response::make(compact('message'), 404);
        });
    }
}
