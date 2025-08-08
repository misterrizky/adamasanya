<?php

namespace Database\Seeders\Master;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BloodTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $file = public_path('json/master/blood_types.json');
        $json = file_get_contents($file);
        $data = json_decode($json, true);
        \App\Models\Master\BloodType::insert($data);
    }
}
