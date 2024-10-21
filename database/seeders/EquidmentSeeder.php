<?php

namespace Database\Seeders;

use App\Models\Equidment;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class EquidmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();

        for ($i = 0; $i < 10; $i++) {
            Equidment::create([
                'name' => $faker->word,
                'model' => $faker->word,
                'status' => $faker->randomElement(['airworthy', 'retired', 'maintenance']),
                'inventory_asset' => $faker->randomElement(['Asset', 'Inventory']),
                'serial' => $faker->randomNumber(5),
                'type' => $faker->randomElement(['electronic', 'mechanical', 'sensor']),
                'drones_id' => $faker->numberBetween(1, 10), // Asumsi ID drone antara 1-10
                'users_id' => 1,//$faker->numberBetween(1, 10), // Asumsi ID user antara 1-10
                'purchase_date' => $faker->date(),
                'insurable_value' => $faker->numberBetween(1000, 10000),
                'weight' => $faker->numberBetween(100, 1000), // Berat dalam gram
                'firmware_v' => $faker->randomElement(['1.0', '2.0', '3.1']),
                'hardware_v' => $faker->randomElement(['A', 'B', 'C']),
                'is_loaner' => $faker->boolean,
                'description' => $faker->sentence,
                'teams_id' => 1, // Asumsi ID tim antara 1-5
            ]);
        }
    }
}
