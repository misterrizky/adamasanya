<?php

namespace Database\Seeders\Region;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SubdistrictSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $file = public_path('json/region/subdistricts.json');
        $json = file_get_contents($file);
        $data = json_decode($json, true);
        $chunks = array_chunk($data, 1000);
        foreach ($chunks as $key => $chunk) {
            \App\Models\Region\Subdistrict::insert($chunk);
        }
    }
}
