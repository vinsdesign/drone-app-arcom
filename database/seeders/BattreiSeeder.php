<?php

namespace Database\Seeders;

use App\Models\battrei;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class BattreiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();

        for ($i = 0; $i < 10; $i++) {
            battrei::create([
                'name' => $faker->word,
                'model' => $faker->word,
                'status' => $faker->randomElement(['airworthy', 'retired', 'maintenance']),
                'asset_inventory' => $faker->randomElement(['Asset', 'Inventory']),
                'serial_P' => $faker->randomNumber(5),
                'serial_I' => $faker->randomNumber(5),
                'cellCount' => $faker->numberBetween(4, 12),
                'nominal_voltage' => $faker->numberBetween(3, 12),
                'capacity' => $faker->numberBetween(1000, 6000),
                'initial_Cycle_count' => $faker->numberBetween(0, 100),
                'life_span' => $faker->numberBetween(200, 1000),
                'flaight_count' => $faker->numberBetween(0, 200),
                'for_drone' => $faker->numberBetween(1,10), // Assuming drones exist with IDs between 1 and 10
                'purchase_date' => $faker->date(),
                'insurable_value' => $faker->numberBetween(1000, 5000),
                'wight' => $faker->numberBetween(100, 500),
                'firmware_version' => $faker->randomElement(['1.0', '2.0', '3.0']),
                'hardware_version' => $faker->randomElement(['A', 'B', 'C']),
                'is_loaner' => $faker->boolean,
                'description' => $faker->sentence,
                'users_id' => 1,
                'teams_id' =>1, 
            ]);
        }
    }
}
