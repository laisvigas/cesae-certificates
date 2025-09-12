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

        // Create example events
        $events = [
            [
                'title' => 'Workshop Laravel',
                'start_at' => now()->addDays(5),
                'end_at'   => now()->addDays(5)->addHours(3),
                'hours'    => 3,
                'event_type_id' => 1,
            ],
            [
                'title' => 'Palestra sobre Segurança',
                'start_at' => now()->addDays(10),
                'end_at'   => now()->addDays(10)->addHours(2),
                'hours'    => 2,
                'event_type_id' => 3,
            ],
            [
                'title' => 'Curso de PHP Moderno',
                'start_at' => now()->addDays(15),
                'end_at'   => now()->addDays(20),
                'hours'    => 20,
                'event_type_id' => 5,
            ],
            [
                'title' => 'Workshop React Avançado',
                'start_at' => now()->addDays(7),
                'end_at'   => now()->addDays(7)->addHours(4),
                'hours'    => 4,
                'event_type_id' => 1,
            ],
            [
                'title' => 'Formação DevOps Básico',
                'start_at' => now()->addDays(12),
                'end_at'   => now()->addDays(14),
                'hours'    => 16,
                'event_type_id' => 2,
            ],
            [
                'title' => 'Webinar sobre Cloud',
                'start_at' => now()->addDays(8),
                'end_at'   => now()->addDays(8)->addHours(2),
                'hours'    => 2,
                'event_type_id' => 4,
            ],
            [
                'title' => 'Curso de Python para Dados',
                'start_at' => now()->addDays(18),
                'end_at'   => now()->addDays(25),
                'hours'    => 30,
                'event_type_id' => 5,
            ],
            [
                'title' => 'Palestra sobre IA Generativa',
                'start_at' => now()->addDays(20),
                'end_at'   => now()->addDays(20)->addHours(3),
                'hours'    => 3,
                'event_type_id' => 3,
            ],
            [
                'title' => 'Workshop Git & GitHub',
                'start_at' => now()->addDays(3),
                'end_at'   => now()->addDays(3)->addHours(2),
                'hours'    => 2,
                'event_type_id' => 1,
            ],
            [
                'title' => 'Webinar Introdução ao Docker',
                'start_at' => now()->addDays(6),
                'end_at'   => now()->addDays(6)->addHours(2),
                'hours'    => 2,
                'event_type_id' => 4,
            ],
            [
                'title' => 'Formação Segurança em Redes',
                'start_at' => now()->addDays(30),
                'end_at'   => now()->addDays(32),
                'hours'    => 16,
                'event_type_id' => 2,
            ],
            [
                'title' => 'Curso Frontend Completo',
                'start_at' => now()->addDays(40),
                'end_at'   => now()->addDays(50),
                'hours'    => 40,
                'event_type_id' => 5,
            ],
        ];

        $eventModels = [];
        foreach ($events as $e) {
            $eventModels[] = Event::firstOrCreate(
                ['title' => $e['title']],
                $e
            );
        }

        // Create participants
        $participantsData = [
            ['Alice Johnson', 'alice@example.com'],
            ['Bob Smith', 'bob@example.com'],
            ['Carlos Silva', 'carlos@example.com'],
            ['Diana Santos', 'diana@example.com'],
            ['Eduardo Costa', 'eduardo@example.com'],
            ['Fernanda Lima', 'fernanda@example.com'],
            ['Gabriel Souza', 'gabriel@example.com'],
            ['Helena Rocha', 'helena@example.com'],
            ['Igor Mendes', 'igor@example.com'],
            ['Julia Ferreira', 'julia@example.com'],
            ['Kevin Almeida', 'kevin@example.com'],
            ['Laura Martins', 'laura@example.com'],
            ['Marcelo Oliveira', 'marcelo@example.com'],
            ['Natália Ribeiro', 'natalia@example.com'],
            ['Otávio Pereira', 'otavio@example.com'],
            ['Paula Azevedo', 'paula@example.com'],
            ['Rafael Gomes', 'rafael@example.com'],
            ['Sofia Carvalho', 'sofia@example.com'],
            ['Thiago Duarte', 'thiago@example.com'],
            ['Vanessa Barros', 'vanessa@example.com'],
            ['William Torres', 'william@example.com'],
            ['Yasmin Costa', 'yasmin@example.com'],
        ];

        $participants = [];
        foreach ($participantsData as [$name, $email]) {
            $participants[] = Participant::firstOrCreate(['email' => $email], ['name' => $name]);
        }

        // Link participants to events
        foreach ($eventModels as $index => $event) {
            $assigned = collect($participants)->random(rand(3, 6))->pluck('id')->toArray();
            $event->participants()->syncWithoutDetaching($assigned);
        }
    }
}
