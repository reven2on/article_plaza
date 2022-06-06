<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\UserView;
use App\Models\Article;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserViewTest extends TestCase
{
    public function testUserviewModelInsertData(): void
    {
        $userView = Article::factory()->hasUserviews(1)->create();
        $this->assertModelExists($userView);
    }

    public function testUserviewBelongsToRelationshipWithArticle()
    {

        $article = Article::factory()->create(); 
        $userView = UserView::factory()->create(['article_id' => $article->id]); 

        $this->assertEquals(1, $article->userViews()->count());
        $this->assertInstanceOf(Article::class, $userView->article);

    }
}
