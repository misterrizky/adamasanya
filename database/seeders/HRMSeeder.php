<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Database\Seeders\HRM\RoleSeeder;
use Database\Seeders\HRM\UserSeeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class HRMSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            RoleSeeder::class,
        ]);
    }
}
