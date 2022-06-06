<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Rating;
use App\Models\Article;
use App\Models\Category;
use App\Models\UserView;
use App\Events\ArticleRated;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RateAccurateSortTest extends TestCase
{
    public function testProofOfAccurateSortByRate(): void
    {
        $articles = Article::factory()->count(2)->create();
        Rating::factory()->create(['article_id' => $articles[0]->id , 'rate' => 5]);

        Rating::factory()->create(['article_id' => $articles[1]->id , 'rate' => 4]);
        Rating::factory()->create(['article_id' => $articles[1]->id , 'rate' => 4]);
        Rating::factory()->create(['article_id' => $articles[1]->id , 'rate' => 4]);

        $articles->each(function ($article) {
            event(new ArticleRated($article));
        });

        $this->assertTrue($articles[1]->calculated_rate>$articles[0]->calculated_rate);
        
    }


}
