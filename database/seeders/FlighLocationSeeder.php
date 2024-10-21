<?php

namespace Database\Seeders;

use App\Models\fligh_location;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class FlighLocationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();

        for ($i = 0; $i < 10; $i++) {
            fligh_location::create([
                'name' => $faker->city . ' Flight Location',
                'description' => $faker->sentence,
                'draw' => $faker->boolean,
                'address' => $faker->address,
                'city' => $faker->city,
                'state' => $faker->state,
                'country' => $faker->country,
                'pos_code' => $faker->numberBetween(1000, 8000),
                'latitude' => $faker->latitude(-90, 90),
                'longitude' => $faker->longitude(-180, 180),
                'altitude' => $faker->numberBetween(50, 3000), // Altitude dalam meter
                'teams_id' => 1, // Asumsi ID tim antara 1-5
                'projects_id' => $faker->numberBetween(1, 10), // Asumsi ID project antara 1-10, nullable
                'customers_id' => $faker->numberBetween(1, 10), // Asumsi ID customer antara 1-10, nullable
            ]);
        }
    }
}
