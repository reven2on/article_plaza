<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Database\Seeders\RatingSeeder;
use Database\Seeders\ArticleSeeder;
use Database\Seeders\CategorySeeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            ArticleSeeder::class,
            RatingSeeder::class,
            UserViewSeeder::class,
            CategorySeeder::class
        ]);
    }
}
