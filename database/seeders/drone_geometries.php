<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;


class drone_geometries extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('drone_geometries')->insert([
            [
                'name' => 'Dual Rotor Coaxial',
            ],
            [
                'name' => 'Fixed Wing 1',
            ],
            [
                'name' => 'Fixed Wing 2',
            ],
            [
                'name' => 'Fixed Wing 3',
            ],
            [
                'name' => 'Hexa +',
            ],
            [
                'name' => 'Hexa x',
            ],
            [
                'name' => 'Octa  +',
            ],
            [
                'name' => 'Octa V',
            ],
            [
                'name' => 'Octa X',
            ],
            [
                'name' => 'Quad +',
            ],
            [
                'name' => 'Quad X',
            ],
            [
                'name' => 'Quad X DJI',
            ],
            [
                'name' => 'Single Rotor',
            ],
            [
                'name' => 'Tri',
            ],
            [
                'name' => 'VTOL 1',
            ],
            [
                'name' => 'VTOL 2',
            ],
            [
                'name' => 'VTOL 3',
            ],
            [
                'name' => 'VTOL 4',
            ],
            [
                'name' => 'X8 Coaxial',
            ],
            [
                'name' => 'X6 Coaxial',
            ],
        ]);
    }
}
