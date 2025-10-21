<?php

namespace Database\Seeders;

use App\Models\Zone;
use Illuminate\Database\Seeder;

class ZoneSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create predefined zones
        $zones = [
            [
                'name' => 'Zona Norte',
                'code' => 'ZN001',
                'description' => 'Zona que comprende las escuelas del norte de la ciudad',
                'active' => true,
            ],
            [
                'name' => 'Zona Sur',
                'code' => 'ZS001',
                'description' => 'Zona que comprende las escuelas del sur de la ciudad',
                'active' => true,
            ],
            [
                'name' => 'Zona Centro',
                'code' => 'ZC001',
                'description' => 'Zona que comprende las escuelas del centro de la ciudad',
                'active' => true,
            ],
            [
                'name' => 'Zona Este',
                'code' => 'ZE001',
                'description' => 'Zona que comprende las escuelas del este de la ciudad',
                'active' => true,
            ],
            [
                'name' => 'Zona Oeste',
                'code' => 'ZO001',
                'description' => 'Zona que comprende las escuelas del oeste de la ciudad',
                'active' => true,
            ],
        ];

        foreach ($zones as $zone) {
            Zone::create($zone);
        }

        // Create additional random zones
        Zone::factory(5)->create();
    }
}
