<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Database\Seeders\Region\CitySeeder;
use Database\Seeders\Region\StateSeeder;
use Database\Seeders\Region\CountrySeeder;
use Database\Seeders\Region\VillageSeeder;
use Database\Seeders\Region\CurrencySeeder;
use Database\Seeders\Region\LanguageSeeder;
use Database\Seeders\Region\TimezoneSeeder;
use Database\Seeders\Region\SubdistrictSeeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class RegionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->call([
            CountrySeeder::class,
            StateSeeder::class,
            CitySeeder::class,
            SubdistrictSeeder::class,
            VillageSeeder::class,
            CurrencySeeder::class,
            LanguageSeeder::class,
            TimezoneSeeder::class,
        ]);
    }
}
