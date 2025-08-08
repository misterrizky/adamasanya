<?php

namespace Database\Seeders\Region;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $file = public_path('json/region/cities.json');
        $json = file_get_contents($file);
        $data = json_decode($json, true);
        \App\Models\Region\City::castAndCreate($data);
    }
}
