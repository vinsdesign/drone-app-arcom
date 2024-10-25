<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(FlighLocationSeeder::class);
        // $this->call(DroneSeeder::class);
        // $this->call(EquidmentSeeder::class);
        
        // $this->call(BattreiSeeder::class);
        
    }
    
}
