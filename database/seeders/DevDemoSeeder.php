<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Event;
use App\Models\Participant;
use App\Models\EventType;
use App\Models\Certificate;

class DevDemoSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {
            // Admin demo
            User::firstOrCreate(
                ['email' => 'admin@demo.com'],
                [
                    'name' => 'Admin Demo',
                    'password' => bcrypt('password123'),
                    'role' => 'admin'
                ]
            );

            // Tipos
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

            $now = now();

            // Emissor padrão (preenche apenas campos existentes; sem template)
            $issuer = [
                'issuer_institution'    => 'Cesae Digital',
                'issuer_name'           => 'Marcia Santos',
                'issuer_role'           => 'Coordenador de Certificação',
            ];


            $events = [
                // -------- PASSADOS (originais) --------
                [
                    'title'         => 'Workshop: Docker & PokéCaps',
                    'start_at'      => $now->copy()->subDays(120)->setTime(9, 0),
                    'end_at'        => $now->copy()->subDays(120)->setTime(12, 0),
                    'hours'         => 3,
                    'event_type_id' => 1,
                    'description'   => 'Imagens como Pokébolas: build, push e deploy',
                ],
                [
                    'title'         => 'Palestra: Segurança de API no Estilo Survey Corps',
                    'start_at'      => $now->copy()->subDays(45)->setTime(18, 0),
                    'end_at'        => $now->copy()->subDays(45)->setTime(20, 0),
                    'hours'         => 2,
                    'event_type_id' => 3,
                    'description'   => 'Assinaturas, rate limits e camadas defensivas “anti-titã”.',
                ],

                // -------- A DECORRER --------
                [
                    'title'         => 'Formação: UX Sprint com Mapa de Hyrule',
                    'start_at'      => $now->copy()->subHours(2),
                    'end_at'        => $now->copy()->addHours(6),
                    'hours'         => 8,
                    'event_type_id' => 2,
                    'description'   => 'Exploração guiada, side quests de teste e dungeons de usabilidade.',
                ],
                [
                    'title'         => 'Curso Intensivo: Data Science com Pedra Filosofal',
                    'start_at'      => $now->copy()->subDays(2)->setTime(9, 0),
                    'end_at'        => $now->copy()->addDays(3)->setTime(17, 0),
                    'hours'         => 40,
                    'event_type_id' => 5,
                    'description'   => 'EDA, features e ML seguindo a lei da troca equivalente.',
                ],

                // -------- FUTUROS --------
                [
                    'title'         => 'Workshop: Laravel no Reino Cogumelo',
                    'start_at'      => $now->copy()->addDays(5),
                    'end_at'        => $now->copy()->addDays(5)->addHours(3),
                    'hours'         => 3,
                    'event_type_id' => 1,
                    'description'   => 'Pipes, filas e power-ups com Eloquent.',
                ],
                [
                    'title'         => 'Palestra: Defesa de Sistemas com Caçadores de Titãs',
                    'start_at'      => $now->copy()->addDays(10),
                    'end_at'        => $now->copy()->addDays(10)->addHours(2),
                    'hours'         => 2,
                    'event_type_id' => 3,
                ],
                [
                    'title'         => 'Alquimia de Código',
                    'start_at'      => $now->copy()->addDays(15),
                    'end_at'        => $now->copy()->addDays(20),
                    'hours'         => 20,
                    'event_type_id' => 5,
                ],
                [
                    'title'         => 'Workshop: React Avançado com Triforce de Componentes',
                    'start_at'      => $now->copy()->addDays(7),
                    'end_at'        => $now->copy()->addDays(7)->addHours(4),
                    'hours'         => 4,
                    'event_type_id' => 1,
                ],
                [
                    'title'         => 'Formação: DevOps Básico — Forja de Itens em Midgar',
                    'start_at'      => $now->copy()->addDays(12),
                    'end_at'        => $now->copy()->addDays(14),
                    'hours'         => 16,
                    'event_type_id' => 2,
                ],
                [
                    'title'         => 'Webinar: Cloud Summon — Invocações Escaláveis',
                    'start_at'      => $now->copy()->addDays(8),
                    'end_at'        => $now->copy()->addDays(8)->addHours(2),
                    'hours'         => 2,
                    'event_type_id' => 4,
                ],
                [
                    'title'         => 'Curso: Python para Dados com Pokédex Pandas',
                    'start_at'      => $now->copy()->addDays(18),
                    'end_at'        => $now->copy()->addDays(25),
                    'hours'         => 30,
                    'event_type_id' => 5,
                ],
                [
                    'title'         => 'Palestra: IA Generativa com Persona Links',
                    'start_at'      => $now->copy()->addDays(20),
                    'end_at'        => $now->copy()->addDays(20)->addHours(3),
                    'hours'         => 3,
                    'event_type_id' => 3,
                ],
                [
                    'title'         => 'Workshop: Git & GitHub — Save Points e História Ramificada',
                    'start_at'      => $now->copy()->addDays(3),
                    'end_at'        => $now->copy()->addDays(3)->addHours(2),
                    'hours'         => 2,
                    'event_type_id' => 1,
                ],
                [
                    'title'         => 'Webinar: Introdução ao Docker — Kame House Edition',
                    'start_at'      => $now->copy()->addDays(6),
                    'end_at'        => $now->copy()->addDays(6)->addHours(2),
                    'hours'         => 2,
                    'event_type_id' => 4,
                ],
                [
                    'title'         => 'Formação: Segurança em Redes — Firewall dos Sete Reinos',
                    'start_at'      => $now->copy()->addDays(30),
                    'end_at'        => $now->copy()->addDays(32),
                    'hours'         => 16,
                    'event_type_id' => 2,
                ],
                [
                    'title'         => 'Curso: Frontend Completo',
                    'start_at'      => $now->copy()->addDays(40),
                    'end_at'        => $now->copy()->addDays(50),
                    'hours'         => 40,
                    'event_type_id' => 5,
                ],
                [
                    'title'         => 'Webinar: Observabilidade com OpenTelemetry — Byakugan Dev (sem participantes)',
                    'start_at'      => $now->copy()->addDays(9)->setTime(18, 30),
                    'end_at'        => $now->copy()->addDays(9)->setTime(20, 0),
                    'hours'         => 2,
                    'event_type_id' => 4,
                ],
                [
                    'title'         => 'Workshop: Design Systems — Guilda de Piltover (sem participantes)',
                    'start_at'      => $now->copy()->addDays(11)->setTime(9, 0),
                    'end_at'        => $now->copy()->addDays(11)->setTime(12, 0),
                    'hours'         => 3,
                    'event_type_id' => 1,
                ],

                // -------- ADICIONADOS --------
                [
                    'title'         => 'Palestra: Engenharia de Software no Mundo Ninja',
                    'start_at'      => $now->copy()->subYears(3)->setTime(19, 0),
                    'end_at'        => $now->copy()->subYears(3)->setTime(21, 0),
                    'hours'         => 2,
                    'event_type_id' => 3,
                    'description'   => 'Arquiteturas modulares e squads como times ninja; testes como jutsus.',
                ],
                [
                    'title'         => 'Workshop: Save Points e História Ramificada no Git',
                    'start_at'      => $now->copy()->subYears(2)->subMonths(6)->setTime(9, 30),
                    'end_at'        => $now->copy()->subYears(2)->subMonths(6)->setTime(12, 30),
                    'hours'         => 3,
                    'event_type_id' => 1,
                    'description'   => 'Branching strategies, rebase vs merge e releases como “limit breaks”.',
                ],
                [
                    'title'         => 'Inteligência Artificial no Retalho: Da Previsão de Stocks à Experiência Personalizada',
                    'start_at'      => $now->copy()->subYears(2)->subMonths(6)->setTime(9, 30),
                    'end_at'        => $now->copy()->subYears(2)->subMonths(6)->setTime(12, 30),
                    'hours'         => 3,
                    'event_type_id' => 1,
                    'description'   => 'Foco em integração entre loja física, e-commerce e apps.”.',
                ],
                [
                    'title'         => 'Introdução ao Open Banking',
                    'start_at'      => $now->copy()->subYears(2)->subMonths(6)->setTime(9, 30),
                    'end_at'        => $now->copy()->subYears(2)->subMonths(6)->setTime(12, 30),
                    'hours'         => 3,
                    'event_type_id' => 1,
                    'description'   => 'Regulamentação (PSD2), impacto no setor bancário e oportunidades para o cliente',
                ],
            ];

            $eventModels = [];
            foreach ($events as $e) {
                $eventModels[] = Event::firstOrCreate(
                    ['title' => $e['title']],
                    array_merge($e, $issuer)
                );
            }

            // ------------------ PARTICIPANTES ------------------
            $participantsData = [
                ['Naruto Uzumaki', 'naruto@konoha.jp', 'Passaporte', 'JP100001', '+351 910 000 001', 'Rua da Folha, 1, Lisboa', 'Japonesa'],
                ['Sakura Haruno', 'sakura@konoha.jp', 'Passaporte', 'JP100002', '+351 910 000 002', 'Rua das Cerejeiras, 2, Porto', 'Japonesa'],
                ['Sasuke Uchiha', 'sasuke@konoha.jp', 'Passaporte', 'JP100003', '+351 910 000 003', 'Avenida Uchiha, 3, Coimbra', 'Japonesa'],
                ['Kakashi Hatake', 'kakashi@konoha.jp', 'Passaporte', 'JP100004', '+351 910 000 004', 'Alameda Sharingan, 4, Braga', 'Japonesa'],
                ['Levi Ackerman', 'levi@aot.jp', 'Passaporte', 'JP100005', '+351 910 000 005', 'Muralha Maria, 5, Porto', 'Japonesa'],
                ['Mikasa Ackerman', 'mikasa@aot.jp', 'Passaporte', 'JP100006', '+351 910 000 006', 'Distrito Shiganshina, 6, Aveiro', 'Japonesa'],
                ['Eren Yeager', 'eren@aot.jp', 'Passaporte', 'JP100007', '+351 910 000 007', 'Muralha Rose, 7, Lisboa', 'Japonesa'],
                ['Armin Arlert', 'armin@aot.jp', 'Passaporte', 'JP100008', '+351 910 000 008', 'Muralha Sina, 8, Coimbra', 'Japonesa'],
                ['Zelda Hyrule', 'zelda@hyrule.org', 'Passaporte', 'HY100009', '+351 910 000 009', 'Castelo Norte, Hyrule', 'Hyliana'],
                ['Link Hateno', 'link@hyrule.org', 'Passaporte', 'HY100010', '+351 910 000 010', 'Vila Hateno, Hyrule', 'Hyliano'],
                ['Ganondorf Dragmire', 'ganon@hyrule.org', 'Passaporte', 'HY100011', '+351 910 000 011', 'Deserto Gerudo, Hyrule', 'Gerudo'],
                ['Cloud Strife', 'cloud@midgar.ff', 'Passaporte', 'FF100012', '+351 910 000 012', 'Setor 7, Midgar', 'Americana'],
                ['Tifa Lockhart', 'tifa@midgar.ff', 'Passaporte', 'FF100013', '+351 910 000 013', 'Setor 7, Midgar', 'Americana'],
                ['Aerith Gainsborough', 'aerith@midgar.ff', 'Passaporte', 'FF100014', '+351 910 000 014', 'Igreja do Setor 5, Midgar', 'Americana'],
                ['Barret Wallace', 'barret@midgar.ff', 'Passaporte', 'FF100015', '+351 910 000 015', 'Setor 7, Midgar', 'Americana'],
                ['Geralt de Rívia', 'geralt@witcher.pl', 'Passaporte', 'PL100016', '+351 910 000 016', 'Kaer Morhen, PL', 'Polaca'],
                ['Ciri', 'ciri@witcher.pl', 'Passaporte', 'PL100017', '+351 910 000 017', 'Cintra, PL', 'Polaca'],
                ['Yennefer Vengerberg', 'yennefer@witcher.pl', 'Passaporte', 'PL100018', '+351 910 000 018', 'Vengerberg, PL', 'Polaca'],
                ['Ellie Williams', 'ellie@lastofus.us', 'Passaporte', 'US100019', '+351 910 000 019', 'Rua Fireflies, 19, Lisboa', 'Americana'],
                ['Joel Miller', 'joel@lastofus.us', 'Passaporte', 'US100020', '+351 910 000 020', 'Av. Boston QZ, 20, Porto', 'Americana'],
                ['Kratos', 'kratos@olympus.gr', 'Passaporte', 'GR100021', '+351 910 000 021', 'Monte Olimpo, GR', 'Grega'],
                ['Atreus', 'atreus@olympus.gr', 'Passaporte', 'GR100022', '+351 910 000 022', 'Florestas Nórdicas, GR', 'Grega'],
                ['Ash Ketchum', 'ash@pallet.jp', 'Passaporte', 'JP100023', '+351 910 000 023', 'Cidade Pallet, Kanto', 'Japonesa'],
                ['Misty', 'misty@cerulean.jp', 'Passaporte', 'JP100024', '+351 910 000 024', 'Cerulean, Kanto', 'Japonesa'],
                ['Brock', 'brock@pewter.jp', 'Passaporte', 'JP100025', '+351 910 000 025', 'Pewter, Kanto', 'Japonesa'],
                ['Pikachu', 'pikachu@pallet.jp', 'Registro', 'PK100026', '+351 910 000 026', 'Pokébola do Ash', 'Pokémon'],
                ['Mario', 'mario@mushroom.it', 'Passaporte', 'IT100027', '+351 910 000 027', 'Reino Cogumelo, IT', 'Italiana'],
                ['Luigi', 'luigi@mushroom.it', 'Passaporte', 'IT100028', '+351 910 000 028', 'Reino Cogumelo, IT', 'Italiana'],
                ['Princess Peach', 'peach@mushroom.it', 'Passaporte', 'IT100029', '+351 910 000 029', 'Castelo Peach, IT', 'Italiana'],
                ['Bowser', 'bowser@koopas.it', 'Passaporte', 'IT100030', '+351 910 000 030', 'Castelo Koopa, IT', 'Italiana'],
                ['Lara Croft', 'lara@croft.uk', 'Passaporte', 'UK100031', '+351 910 000 031', 'Mansão Croft, UK', 'Britânica'],
                ['Jill Valentine', 'jill@raccoon.us', 'Passaporte', 'US100032', '+351 910 000 032', 'Raccoon City, US', 'Americana'],
                ['Claire Redfield', 'claire@raccoon.us', 'Passaporte', 'US100033', '+351 910 000 033', 'Raccoon City, US', 'Americana'],
                ['Leon Kennedy', 'leon@raccoon.us', 'Passaporte', 'US100034', '+351 910 000 034', 'Raccoon City, US', 'Americana'],
                ['Satoru Gojo', 'gojo@jujutsu.jp', 'Passaporte', 'JP100035', '+351 910 000 035', 'Tóquio Jujutsu, JP', 'Japonesa'],
                ['Yuji Itadori', 'itadori@jujutsu.jp', 'Passaporte', 'JP100036', '+351 910 000 036', 'Tóquio Jujutsu, JP', 'Japonesa'],
                ['Aloy', 'aloy@nora.tribe', 'Passaporte', 'NL100037', '+351 910 000 037', 'Embrasina, Tribo Nora', 'Nora'],
                ['Jinx', 'jinx@zaun.lol', 'Passaporte', 'ZA100038', '+351 910 000 038', 'Distrito de Zaun', 'Zaunita'],
                ['Vi', 'vi@piltover.lol', 'Passaporte', 'PLT100039', '+351 910 000 039', 'Piltover', 'Piltovense'],
                ['Geralt (Skellige)', 'geralt.sk@witcher.pl', 'Passaporte', 'PL100040', '+351 910 000 040', 'Skellige, PL', 'Polaca'],
            ];

            $participants = [];
            foreach ($participantsData as [$name, $email, $docType, $docNumber, $phone, $address, $nationality]) {
                $participants[] = Participant::firstOrCreate(
                    ['email' => strtolower($email)],
                    [
                        'name'             => $name,
                        'phone'            => $phone,
                        'address'          => $address,
                        'document_type'    => $docType,
                        'document_number'  => $docNumber,
                        'nationality'      => $nationality,
                    ]
                );
            }

            // Eventos que devem FICAR sem participantes (títulos geek)
            $noParticipantsTitles = [
                'Webinar: Observabilidade com OpenTelemetry — Byakugan Dev (sem participantes)',
                'Workshop: Design Systems — Guilda de Piltover (sem participantes)',
            ];

            // Vincular 4–10 participantes por evento (exceto os que ficam vazios)
            foreach ($eventModels as $event) {
                if (in_array($event->title, $noParticipantsTitles, true)) {
                    continue;
                }
                $assigned = collect($participants)->shuffle()->take(rand(4, 10))->pluck('id')->toArray();
                $event->participants()->syncWithoutDetaching($assigned);
            }

            // Emitir certificados para eventos passados
            $pastEvents = collect($eventModels)->filter(fn ($e) => $e->end_at && $e->end_at->lt($now));
            foreach ($pastEvents as $event) {
                foreach ($event->participants as $p) {
                    $exists = Certificate::where('event_id', $event->id)
                        ->where('participant_id', $p->id)->exists();
                    if ($exists) continue;

                    $ref = sprintf(
                        'EVT-%s-%s-%04d',
                        optional($event->end_at)->format('Y') ?? $now->format('Y'),
                        Str::of($event->title)->slug()->limit(16, ''),
                        random_int(0, 9999)
                    );

                    Certificate::create([
                        'event_id'       => $event->id,
                        'participant_id' => $p->id,
                        'ref'            => strtoupper($ref),
                        'issued_at'      => ($event->end_at ?? $now)->copy()->addDay()->setTime(12, 0),
                    ]);
                }
            }
        });
    }
}
