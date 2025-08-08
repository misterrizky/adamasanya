<?php

namespace Database\Seeders\Master;

use Illuminate\Database\Seeder;
use Database\Seeders\Master\School\SDSeeder;
use Database\Seeders\Master\School\SMASeeder;
use Database\Seeders\Master\School\SMKSeeder;
use Database\Seeders\Master\School\SMPSeeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class SchoolSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->call([
            SDSeeder::class,
            SMPSeeder::class,
            SMASeeder::class,
            SMKSeeder::class,
        ]);
    }
}
