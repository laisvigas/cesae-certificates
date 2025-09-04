<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Event;
use App\Models\Participant;
use App\Models\EventType;

class DevDemoSeeder extends Seeder
{
    public function run(): void
    {
        // Admin demo
        User::firstOrCreate(
            ['email' => 'admin@demo.com'],
            [
                'name' => 'Admin Demo',
                'password' => bcrypt('password123'),
                'role' => 'admin'
            ]
        );

        // Ensure types exist
        $types = [
            'Workshop'  => 1,
            'Formação'  => 2,
            'Palestra'  => 3,
            'Webinar'   => 4,
            'Curso'     => 5,
        ];

        foreach ($types as $name => $id) {
            EventType::firstOrCreate(['id' => $id], ['name' => $name]);
        }

        // Create some example events
        $event1 = Event::firstOrCreate([
            'title' => 'Workshop Laravel',
            'start_at' => now()->addDays(5),
            'end_at' => now()->addDays(5)->addHours(3),
            'hours' => 3,
            'event_type_id' => 1
        ]);

        $event2 = Event::firstOrCreate([
            'title' => 'Palestra sobre Segurança',
            'start_at' => now()->addDays(10),
            'end_at' => now()->addDays(10)->addHours(2),
            'hours' => 2,
            'event_type_id' => 3
        ]);

        // Participant examples
        $p1 = Participant::firstOrCreate(['email' => 'alice@example.com'], ['name' => 'Alice']);
        $p2 = Participant::firstOrCreate(['email' => 'bob@example.com'], ['name' => 'Bob']);

        // Link participants to events
        $event1->participants()->syncWithoutDetaching([$p1->id, $p2->id]);
        $event2->participants()->syncWithoutDetaching([$p2->id]);
    }
}
