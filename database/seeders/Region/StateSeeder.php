<?php

namespace Database\Seeders\Region;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class StateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $file = public_path('json/region/states.json');
        $json = file_get_contents($file);
        $data = json_decode($json, true);
        \App\Models\Region\State::insert($data);
    }
}
