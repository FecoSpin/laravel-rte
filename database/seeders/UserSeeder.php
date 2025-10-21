<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Zone;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $zones = Zone::all();

        // Create admin user
        User::create([
            'name' => 'Administrador',
            'email' => 'admin@rte.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'zone_id' => null,
            'active' => true,
        ]);

        // Create supervisor users for each zone
        foreach ($zones->take(5) as $zone) {
            User::create([
                'name' => 'Supervisor ' . $zone->name,
                'email' => 'supervisor' . $zone->id . '@rte.com',
                'password' => Hash::make('password'),
                'role' => 'supervisor',
                'zone_id' => $zone->id,
                'active' => true,
            ]);
        }

        // Create technician users
        foreach ($zones->take(8) as $index => $zone) {
            User::create([
                'name' => 'Técnico ' . ($index + 1),
                'email' => 'tecnico' . ($index + 1) . '@rte.com',
                'password' => Hash::make('password'),
                'role' => 'technician',
                'zone_id' => $zone->id,
                'active' => true,
            ]);
        }

        // Create regular users
        User::factory(10)->create([
            'role' => 'user',
            'zone_id' => fn() => $zones->random()->id,
        ]);

        // Create additional random users with different roles
        User::factory(5)->create([
            'role' => 'technician',
            'zone_id' => fn() => $zones->random()->id,
        ]);
    }
}
