<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Fligh; // Pastikan nama model sesuai dengan casing
use Faker\Factory as Faker;
use App\Models\Customer; // Model Customer
use App\Models\fligh_location; // Model FlighLocation
use App\Models\Project; // Model Projects
use App\Models\kits; // Model Kit
use App\Models\User; // Model User
use App\Models\Drone; // Model Drone
use App\Models\battrei; // Model Bluetooth
use App\Models\equidment; // Model

class FlighSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create();

        for ($i = 0; $i < 10; $i++) {
            $fligh = Fligh::create([
                'name' => $faker->word,
                'date_flight' => $faker->dateTimeBetween('-1 year', 'now'),
                'duration' => $faker->time(), // Menggunakan waktu sebagai durasi
                'type' => $faker->word,
                'ops' => $faker->word,
                'landings' => $faker->numberBetween(1, 10),
                'customers_id' => Customer::inRandomOrder()->first()->id, // Ambil ID customer secara acak
                'location_id' => fligh_location::inRandomOrder()->first()->id, // Ambil ID lokasi secara acak
                'projects_id' => Project::inRandomOrder()->first()->id, // Ambil ID proyek secara acak
                'kits_id' => kits::inRandomOrder()->first()->id, // Ambil ID kits secara acak
                'users_id' => User::inRandomOrder()->first()->id, // Ambil ID pengguna secara acak
                'vo' => $faker->word,
                'po' => $faker->word,
                'instructor' => $faker->name,
                'drones_id' => Drone::inRandomOrder()->first()->id, // Ambil ID drone secara acak
                'battreis_id' => Battrei::inRandomOrder()->first()->id, // Ambil ID baterai secara acak
                'equidments_id' => Equidment::inRandomOrder()->first()->id, // Ambil ID peralatan secara acak
                'pre_volt' => $faker->numberBetween(10, 20), // Tegangan sebelum penerbangan
                'fuel_used' => $faker->numberBetween(1, 100), // Bahan bakar yang digunakan
                'teams_id' => 1, // Ambil ID tim secara acak
            ]);
        }
    }
}