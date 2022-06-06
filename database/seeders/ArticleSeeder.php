<?php

namespace Database\Seeders;

use App\Models\Article;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class ArticleSeeder extends Seeder
{

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->faker = Faker::create();  
        for($i=0;$i< 1000 ; $i++) {
            $data[]=[
                'title' => $this->faker->realText($maxNbChars = 50, $indexSize = 2),
                'body' => $this->faker->realText($maxNbChars = 100, $indexSize = 2),
                'created_at' => $this->faker->dateTimeBetween('-1 week', 'now'),
                'updated_at' => $this->faker->dateTimeBetween('-1 week', 'now')
            ];
        }

        $chunks = array_chunk($data, 50);
        foreach($chunks as $chunk) {
            Article::insert($chunk);
        }


        
    }
}
