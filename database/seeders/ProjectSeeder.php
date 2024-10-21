<?php

namespace Database\Seeders;

use App\Models\Project;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class ProjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();

        for ($i = 0; $i < 10; $i++) {
            Project::create([
                'case' => $faker->word,
                'revenue' => $faker->numberBetween(10000, 100000),
                'currency' => $faker->randomElement(['USD', 'EUR', 'IDR', 'JPY', 'GBP']),
                'customers_id' => $faker->numberBetween(1, 10), // Asumsi ID customer antara 1-10
                'description' => $faker->sentence,
                'teams_id' =>1, // Asumsi ID tim antara 1-5
            ]);
        }
    }
}
