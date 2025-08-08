<?php

namespace Database\Seeders\Master;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SchoolLevelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $levels = [
            [
                'code' => 'TS',
                'name' => 'Tidak / Belum Sekolah',
            ],
            [
                'code' => 'BTSD',
                'name' => 'Belum Tamat SD / Sederajat',
            ],
            [
                'code' => 'SD',
                'name' => 'Sekolah Dasar / Sederajat',
            ],
            [
                'code' => 'SMP',
                'name' => 'Sekolah Menengah Pertama / Sederajat',
            ],
            [
                'code' => 'SMA',
                'name' => 'Sekolah Menengah Atas / Sederajat',
            ],
            [
                'code' => 'SMK',
                'name' => 'Sekolah Menengah Kejuruan / Sederajat',
            ],
            [
                'code' => 'D1/D2',
                'name' => 'Diploma I / II',
            ],
            [
                'code' => 'D3',
                'name' => 'Akademi / Diploma III / Sarjana Muda',
            ],
            [
                'code' => 'D4/S1',
                'name' => 'Diploma IV / Strata I',
            ],
            [
                'code' => 'S2',
                'name' => 'Strata II',
            ],
            [
                'code' => 'S3',
                'name' => 'Strata III',
            ]
        ];
        foreach ($levels as $level){
            \App\Models\Master\SchoolLevel::insert($level);
        }
    }
}
