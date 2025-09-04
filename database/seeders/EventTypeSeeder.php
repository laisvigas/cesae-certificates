<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\EventType; 

class EventTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach (['Workshop','Formação','Palestra','Webinar','Curso'] as $name) {
            EventType::firstOrCreate(['name' => $name]);
        }
    }
}
