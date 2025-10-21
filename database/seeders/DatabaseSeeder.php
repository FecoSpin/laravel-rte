<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            ZoneSeeder::class,
            UserSeeder::class,
            SchoolSeeder::class,
            RteSurveySeeder::class,
            MaintenanceRequestSeeder::class,
        ]);
    }
}
