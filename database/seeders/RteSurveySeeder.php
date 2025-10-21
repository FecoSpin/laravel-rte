<?php

namespace Database\Seeders;

use App\Models\RteSurvey;
use App\Models\User;
use App\Models\Zone;
use Illuminate\Database\Seeder;

class RteSurveySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $zones = Zone::all();
        $technicians = User::where('role', 'technician')->get();
        $supervisors = User::where('role', 'supervisor')->get();

        // Create surveys with different statuses
        foreach ($zones as $zone) {
            $zoneTechnicians = $technicians->where('zone_id', $zone->id);
            $zoneSupervisor = $supervisors->where('zone_id', $zone->id)->first();

            if ($zoneTechnicians->count() > 0) {
                // Draft surveys
                RteSurvey::factory(2)->draft()->create([
                    'zone_id' => $zone->id,
                    'user_id' => $zoneTechnicians->random()->id,
                ]);

                // Submitted surveys
                RteSurvey::factory(3)->submitted()->create([
                    'zone_id' => $zone->id,
                    'user_id' => $zoneTechnicians->random()->id,
                ]);

                // Approved surveys
                if ($zoneSupervisor) {
                    RteSurvey::factory(2)->approved()->create([
                        'zone_id' => $zone->id,
                        'user_id' => $zoneTechnicians->random()->id,
                        'approved_by' => $zoneSupervisor->id,
                    ]);
                }

                // Rejected surveys
                RteSurvey::factory(1)->create([
                    'zone_id' => $zone->id,
                    'user_id' => $zoneTechnicians->random()->id,
                    'status' => 'rejected',
                    'rejection_reason' => 'Información incompleta. Favor de revisar los datos proporcionados.',
                ]);
            }
        }
    }
}
