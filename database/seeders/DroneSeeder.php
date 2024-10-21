<?php

namespace Database\Seeders;

use App\Models\Drone;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class DroneSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();

        for ($i = 0; $i < 10; $i++) {
            Drone::create([
                'name' => $faker->word,
                'status' => $faker->randomElement(['airworthy', 'retired', 'maintenance']),
                'idlegal' => $faker->uuid,
                'brand' => $faker->company,
                'model' => $faker->word,
                'type' => $faker->randomElement(['quad', 'hexa', 'octo']),
                'inventory_asset' => $faker->randomElement(['Asset', 'Inventory']),
                'serial_p' => $faker->randomNumber(5),
                'serial_i' => $faker->randomNumber(5),
                'flight_c' => $faker->word,
                'remote_c' => $faker->word,
                'remote_cc' => $faker->word,
                'geometry' => $faker->randomElement(['X', 'H', 'Y']),
                'description' => $faker->sentence,
                'users_id' => 1, // Asumsi ID user antara 1-10
                'firmware_v' => $faker->randomElement(['1.0', '2.1', '3.0']),
                'hardware_v' => $faker->randomElement(['A', 'B', 'C']),
                'propulsion_v' => $faker->randomElement(['ProA', 'ProB', 'ProC']),
                'color' => $faker->safeColorName(),
                'remote' => $faker->word,
                'conn_card' => $faker->word,
                'teams_id' => 1, // Asumsi ID tim antara 1-5
            ]);
        }
    }
}
