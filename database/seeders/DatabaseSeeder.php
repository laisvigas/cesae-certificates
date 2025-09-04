<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;


class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
                EventTypeSeeder::class,
            ]);

            if (app()->environment('local')) {
                $this->call([
                    DevDemoSeeder::class,
                ]);
            }
    }
}
