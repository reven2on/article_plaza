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

class ArticleTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    public function testArticleModelInsertData(): void
    {
        $article = Article::factory()->create();
        $this->assertModelExists($article);
    }

    public function testArticleOneToManyRelationshipWithUserview(): void
    {
        $count = rand(0, 10);
        $article = Article::factory()
                    ->hasUserViews($count)
                    ->create();
        $this->assertCount($count, $article->userViews);
    }

    public function testArticleOneToManyRelationshipWithRating(): void
    {
        $count = rand(0, 10);
        $article = Article::factory()
                    ->hasRatings($count)
                    ->create();
        $this->assertCount($count, $article->ratings);
    }

    public function testArticleManyToManyRelationshipWithCategory(): void
    {

        $article = Article::factory()->create(); 
        $category = Category::factory()->create(); 

        $this->assertInstanceOf('Illuminate\Database\Eloquent\Collection', $article->categories); 
    }

    public function testArticleIndexOutputIsValid(): void
    {
        $this->json('get', 'api/articles')
         ->assertStatus(Response::HTTP_OK)
         ->assertJsonStructure(
             [
                'message',
                'data' => [
                    '*' => [
                        'id',
                        'title',
                        'body',
                        'creation_date',
                        'views',
                        'rating'
                    ]
                ]
             ]
         );
    }
    

    public function testArticleShowWithValidArticleIdOutputIsValid(): void
    {
        $article = Article::factory()->count(1)->create();
        $this->json('get', 'api/articles/'.$article->first()->id)
         ->assertStatus(Response::HTTP_OK)
         ->assertJsonStructure(
             [
                'message',
                'data' =>  [
                        'id',
                        'title',
                        'body',
                        'creation_date',
                        'views',
                        'rating'
                    ]
             ]
         );
    }


    public function testArticleShowWithInvalidArticleIdOutputIsValid(): void
    {

        Article::query()->delete();
        $this->json('get', 'api/articles/1')
         ->assertStatus(Response::HTTP_NOT_FOUND)
         ->assertJsonStructure(
             [
                'message'
             ]
         );
    }


    public function testArticleCreate(): void
    {
        Article::query()->delete();

        $category = Category::factory()->count(1)->create();
        $payload = [
            'title' => $this->faker->realText($maxNbChars = 200, $indexSize = 2),
            'body' => $this->faker->realText($maxNbChars = 200, $indexSize = 2),
            'categories' => [$category->first()->title]
        ];
        
        $this->json('post', 'api/articles', $payload)
             ->assertStatus(Response::HTTP_CREATED)
             ->assertJsonStructure(
                 [
                     'message'
                 ]
             );
         
        $this->assertDatabaseCount('articles', 1);
    }


    public function testArticleRatingWithValidRate(): void
    {
        $article = Article::factory()->count(1)->create();
        $this->json('post', 'api/articles/'.$article->first()->id.'/rate', [ 'rate' => 1])
         ->assertStatus(Response::HTTP_CREATED)
         ->assertJsonStructure(
             [
                'message'
             ]
         );
    }

    public function testArticleRatingWithInvalidRate(): void
    {
        $article = Article::factory()->count(1)->create();
        $this->json('post', 'api/articles/'.$article->first()->id.'/rate', [ 'rate' => 10])
         ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
         ->assertJsonStructure(
             [
                'message',
                'data' => [
                    'rate'
                ]
             ]
         );
    }

    public function testArticleRatingWithInvalidArticleID(): void
    {
        $article = Article::factory()->count(1)->create();
        $this->json('post', 'api/articles/'.($article->first()->id+1).'/rate', [ 'rate' => 1])
         ->assertStatus(Response::HTTP_NOT_FOUND)
         ->assertJsonStructure(
             [
                'message'
             ]
         );
    }

    public function testArticleRatingAlreadyRatedArticle(): void
    {
        $this->serverVariables = ['REMOTE_ADDR' => '192.168.1.1'];
        $article = Article::factory()->count(1)->hasRatings(1, [
            'ipaddress' => '192.168.1.1'
        ])->create();

        $this->json('post', 'api/articles/'.($article->first()->id).'/rate', [ 'rate' => 1])
         ->assertStatus(Response::HTTP_CONFLICT)
         ->assertJsonStructure(
             [
                'message'
             ]
         );
    }

    public function testArticleRatingReachedDailyLimit(): void
    {
        Config::set('article.daily_limit', 1);
        $this->serverVariables = ['REMOTE_ADDR' => '192.168.1.1'];

        $article = Article::factory()->count(1)->create();
        $this->json('post', 'api/articles/'.$article->first()->id.'/rate', [ 'rate' => 1])
         ->assertStatus(Response::HTTP_CREATED)
         ->assertJsonStructure(
             [
                'message'
             ]
         );

        $article = Article::factory()->count(1)->create();
        $this->json('post', 'api/articles/'.$article->first()->id.'/rate', [ 'rate' => 1])
         ->assertStatus(Response::HTTP_CONFLICT)
         ->assertJsonStructure(
             [
                'message'
             ]
         );
    }

}
