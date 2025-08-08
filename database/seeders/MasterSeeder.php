<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Database\Seeders\Master\BankSeeder;
use Database\Seeders\Master\BrandSeeder;
use Database\Seeders\Master\LevelSeeder;
use Database\Seeders\Master\BranchSeeder;
use Database\Seeders\Master\CategorySeeder;
use Database\Seeders\Master\ReligionSeeder;
use Database\Seeders\Master\BloodTypeSeeder;
use Database\Seeders\Master\ProfessionSeeder;
use Database\Seeders\Master\SchoolLevelSeeder;
use Database\Seeders\Master\BranchScheduleSeeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class MasterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->call([
            BankSeeder::class,
            BloodTypeSeeder::class,
            BranchSeeder::class,
            BranchScheduleSeeder::class,
            BrandSeeder::class,
            CategorySeeder::class,
            LevelSeeder::class,
            ProfessionSeeder::class,
            ReligionSeeder::class,
            SchoolLevelSeeder::class,
        ]);
    }
}
