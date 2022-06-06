<?php

namespace Database\Seeders;

use App\Models\Rating;
use App\Models\Article;
use Faker\Factory as Faker;
use App\Events\ArticleRated;
use Illuminate\Database\Seeder;

class RatingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $data=[];
        $articles = collect(Article::all()->modelKeys());
        $articles_raw = collect(Article::all());
        $this->faker = Faker::create();
        for ($i=0; $i< 10000; $i++) {
            $data[]=[
            'rate' => $this->faker->numberBetween(1, 5),
            'article_id' => $articles->random(),
            'ipaddress' => $this->faker->localIpv4(),
            'created_at' => $this->faker->dateTimeBetween('-1 week', 'now'),
            'updated_at' => $this->faker->dateTimeBetween('-1 week', 'now')
            ];
        }

        $chunks = array_chunk($data, 100);
        foreach ($chunks as $chunk) {
            Rating::insert($chunk);
        }

        $articles_raw->each(function ($article) {
            event(new ArticleRated($article));
        });
    }
}
