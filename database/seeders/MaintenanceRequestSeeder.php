<?php

namespace Database\Seeders;

use App\Models\MaintenanceRequest;
use App\Models\User;
use App\Models\Zone;
use Illuminate\Database\Seeder;

class MaintenanceRequestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $zones = Zone::all();
        $users = User::where('active', true)->get();
        $technicians = User::where('role', 'technician')->get();

        foreach ($zones as $zone) {
            $zoneUsers = $users->where('zone_id', $zone->id);
            $zoneTechnicians = $technicians->where('zone_id', $zone->id);

            if ($zoneUsers->count() > 0) {
                // Pending requests
                MaintenanceRequest::factory(3)->pending()->create([
                    'zone_id' => $zone->id,
                    'requested_by' => $zoneUsers->random()->id,
                ]);

                // In progress requests
                if ($zoneTechnicians->count() > 0) {
                    MaintenanceRequest::factory(2)->inProgress()->create([
                        'zone_id' => $zone->id,
                        'requested_by' => $zoneUsers->random()->id,
                        'assigned_to' => $zoneTechnicians->random()->id,
                    ]);

                    // Completed requests
                    MaintenanceRequest::factory(4)->completed()->create([
                        'zone_id' => $zone->id,
                        'requested_by' => $zoneUsers->random()->id,
                        'assigned_to' => $zoneTechnicians->random()->id,
                    ]);
                }

                // High priority requests
                MaintenanceRequest::factory(1)->highPriority()->create([
                    'zone_id' => $zone->id,
                    'requested_by' => $zoneUsers->random()->id,
                ]);
            }
        }
    }
}
