<?php

namespace Database\Seeders;

use App\Models\School;
use App\Models\Zone;
use Illuminate\Database\Seeder;

class SchoolSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $zones = Zone::all();

        // Create schools for each zone
        foreach ($zones as $zone) {
            School::factory(rand(3, 8))->create([
                'zone_id' => $zone->id,
            ]);
        }
    }
}
