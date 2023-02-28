<?php

namespace Database\Seeders;

use App\Models\Tache;
use Illuminate\Database\Seeder;

class TacheSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Tache::factory()
            ->count(50)
            ->create();
    }
}