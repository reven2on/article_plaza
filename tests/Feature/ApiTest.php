<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Article;
use App\Models\Category;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Testing\Fluent\AssertableJson;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ApiTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    public function testApiCustomRateLimit(): void
    {
        Config::set('article.hourly_api_throttle', 1);
        $this->serverVariables = ['REMOTE_ADDR' => '192.168.1.1'];

        $this->json('get', 'api/articles')
         ->assertStatus(Response::HTTP_OK);

        $this->json('get', 'api/articles')
        ->assertStatus(Response::HTTP_TOO_MANY_REQUESTS);
    }
}
