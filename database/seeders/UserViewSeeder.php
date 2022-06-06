<?php

namespace Database\Seeders;

use App\Models\Article;
use App\Models\UserView;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
class UserViewSeeder extends Seeder
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

        $this->faker = Faker::create();  
        for($i=0;$i< 100000 ; $i++) {
        $data[]=[
            'article_id' => $articles->random(),
            'ipaddress' => $this->faker->localIpv4(),
            'created_at' => $this->faker->dateTimeBetween('-1 week', 'now'),
            'updated_at' => $this->faker->dateTimeBetween('-1 week', 'now')
        ];
        }

        $chunks = array_chunk($data, 500);
        foreach($chunks as $chunk) {
            UserView::insert($chunk);
        }
    }
}
