<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\UserView;
use App\Models\Article;
use App\Models\Category;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CategoryTest extends TestCase
{
    public function testCategoryModelInsertData(): void
    {
        $userView = Article::factory()->hasCategories(1)->create();
        $this->assertModelExists($userView);
    }

    public function testCategoryBelongsToManyRelationshipWithArticle()
    {

        $article = Article::factory()->create();  
        $category = Category::factory()->create(); 

        $this->assertInstanceOf('Illuminate\Database\Eloquent\Collection', $article->categories); 

    }
}
