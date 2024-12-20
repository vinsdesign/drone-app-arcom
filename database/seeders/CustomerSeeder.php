<?php

namespace Database\Seeders;

use App\Models\team;
use Illuminate\Database\Seeder;
use App\Models\Customer;
use Faker\Factory as Faker;
use Illuminate\Support\Facades\DB;

class CustomerSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create();

        for ($i = 0; $i < 10; $i++) {
            // Membuat customer baru
            $customer = Customer::create([
                'name' => $faker->name,
                'phone' => $faker->phoneNumber,
                'email' => $faker->unique()->safeEmail,
                'address' => $faker->address,
                'description' => $faker->sentence,
                'teams_id' => 12,
            ]);
        
            
            $team = team::find(12);
            if ($team) {
                DB::table('customer_team')->insert([
                    'customer_id' => $customer->id,
                    'team_id' => $team->id,
                ]);
            }
        }
    }
}