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

        // Datas base para consistência
        $now = now();

        // Create example events (passados, a decorrer e futuros)
        $events = [
            // ------------------ PASSADOS ------------------
            [
                'title'         => 'Workshop Docker Essentials (2024)',
                'start_at'      => $now->copy()->subDays(120)->setTime(9, 0),
                'end_at'        => $now->copy()->subDays(120)->setTime(12, 0),
                'hours'         => 3,
                'event_type_id' => 1, // Workshop
            ],
            [
                'title'         => 'Palestra Segurança de API (2024)',
                'start_at'      => $now->copy()->subDays(45)->setTime(18, 0),
                'end_at'        => $now->copy()->subDays(45)->setTime(20, 0),
                'hours'         => 2,
                'event_type_id' => 3, // Palestra
            ],
            // ------------------ A DECORRER ------------------
            [
                'title'         => 'Formação UX Sprint (a decorrer)',
                'start_at'      => $now->copy()->subHours(2),
                'end_at'        => $now->copy()->addHours(6),
                'hours'         => 8,
                'event_type_id' => 2, // Formação
            ],
            [
                'title'         => 'Curso Intensivo Data Science (a decorrer)',
                'start_at'      => $now->copy()->subDays(2)->setTime(9, 0),
                'end_at'        => $now->copy()->addDays(3)->setTime(17, 0),
                'hours'         => 40,
                'event_type_id' => 5, // Curso
            ],
            // ------------------ FUTUROS (os teus + alguns) ------------------
            [
                'title'         => 'Workshop Laravel',
                'start_at'      => $now->copy()->addDays(5),
                'end_at'        => $now->copy()->addDays(5)->addHours(3),
                'hours'         => 3,
                'event_type_id' => 1,
            ],
            [
                'title'         => 'Palestra sobre Segurança',
                'start_at'      => $now->copy()->addDays(10),
                'end_at'        => $now->copy()->addDays(10)->addHours(2),
                'hours'         => 2,
                'event_type_id' => 3,
            ],
            [
                'title'         => 'Curso de PHP Moderno',
                'start_at'      => $now->copy()->addDays(15),
                'end_at'        => $now->copy()->addDays(20),
                'hours'         => 20,
                'event_type_id' => 5,
            ],
            [
                'title'         => 'Workshop React Avançado',
                'start_at'      => $now->copy()->addDays(7),
                'end_at'        => $now->copy()->addDays(7)->addHours(4),
                'hours'         => 4,
                'event_type_id' => 1,
            ],
            [
                'title'         => 'Formação DevOps Básico',
                'start_at'      => $now->copy()->addDays(12),
                'end_at'        => $now->copy()->addDays(14),
                'hours'         => 16,
                'event_type_id' => 2,
            ],
            [
                'title'         => 'Webinar sobre Cloud',
                'start_at'      => $now->copy()->addDays(8),
                'end_at'        => $now->copy()->addDays(8)->addHours(2),
                'hours'         => 2,
                'event_type_id' => 4,
            ],
            [
                'title'         => 'Curso de Python para Dados',
                'start_at'      => $now->copy()->addDays(18),
                'end_at'        => $now->copy()->addDays(25),
                'hours'         => 30,
                'event_type_id' => 5,
            ],
            [
                'title'         => 'Palestra sobre IA Generativa',
                'start_at'      => $now->copy()->addDays(20),
                'end_at'        => $now->copy()->addDays(20)->addHours(3),
                'hours'         => 3,
                'event_type_id' => 3,
            ],
            [
                'title'         => 'Workshop Git & GitHub',
                'start_at'      => $now->copy()->addDays(3),
                'end_at'        => $now->copy()->addDays(3)->addHours(2),
                'hours'         => 2,
                'event_type_id' => 1,
            ],
            [
                'title'         => 'Webinar Introdução ao Docker',
                'start_at'      => $now->copy()->addDays(6),
                'end_at'        => $now->copy()->addDays(6)->addHours(2),
                'hours'         => 2,
                'event_type_id' => 4,
            ],
            [
                'title'         => 'Formação Segurança em Redes',
                'start_at'      => $now->copy()->addDays(30),
                'end_at'        => $now->copy()->addDays(32),
                'hours'         => 16,
                'event_type_id' => 2,
            ],
            [
                'title'         => 'Curso Frontend Completo',
                'start_at'      => $now->copy()->addDays(40),
                'end_at'        => $now->copy()->addDays(50),
                'hours'         => 40,
                'event_type_id' => 5,
            ],
            // ------------------ FUTUROS SEM PARTICIPANTES ------------------
            [
                'title'         => 'Webinar Observabilidade com OpenTelemetry (sem participantes)',
                'start_at'      => $now->copy()->addDays(9)->setTime(18, 30),
                'end_at'        => $now->copy()->addDays(9)->setTime(20, 0),
                'hours'         => 2,
                'event_type_id' => 4,
            ],
            [
                'title'         => 'Workshop Design Systems (sem participantes)',
                'start_at'      => $now->copy()->addDays(11)->setTime(9, 0),
                'end_at'        => $now->copy()->addDays(11)->setTime(12, 0),
                'hours'         => 3,
                'event_type_id' => 1,
            ],
        ];

        $eventModels = [];
        foreach ($events as $e) {
            $eventModels[] = Event::firstOrCreate(
                ['title' => $e['title']], // chave única por título
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

        // Títulos que NÃO receberão participantes
        $noParticipantsTitles = [
            'Webinar Observabilidade com OpenTelemetry (sem participantes)',
            'Workshop Design Systems (sem participantes)',
        ];

        // Link participants to events (3 a 6 participantes por evento), exceto os sem participantes
        foreach ($eventModels as $event) {
            if (in_array($event->title, $noParticipantsTitles, true)) {
                continue; // deixa sem participantes
            }

            $assigned = collect($participants)->random(rand(3, 6))->pluck('id')->toArray();
            $event->participants()->syncWithoutDetaching($assigned);
        }
    }
}
