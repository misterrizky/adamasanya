<?php

namespace Database\Seeders\Master;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProfessionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $file = public_path('json/master/professions.json');
        $json = file_get_contents($file);
        $data = json_decode($json, true);
        \App\Models\Master\Profession::insert($data);
    }
}
