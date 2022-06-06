<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Rating;
use App\Models\Article;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RatingTest extends TestCase
{
    public function testRatingModelInsertData(): void
    {
        $rating = Article::factory()->hasRatings(1)->create();
        $this->assertModelExists($rating);
    }

    public function testRatingBelongsToRelationshipWithArticle()
    {

        $article = Article::factory()->create(); 
        $rating = Rating::factory()->create(['article_id' => $article->id]); 

        $this->assertEquals(1, $article->ratings()->count());
        $this->assertInstanceOf(Article::class, $rating->article);

    }
}
