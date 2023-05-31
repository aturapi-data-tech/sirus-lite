<?php

namespace Database\Seeders;

use App\Models\Post;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use Faker\Factory as Faker;

class postSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create('id_ID');
for($i = 1; $i <= 10000; $i++){
	
    Post::updateOrCreate(['id' => $i], [

        'title' => $faker->name,

        'body' => $faker->jobTitle

    ]);

	}
    
    }
}
