<?php

namespace Database\Seeders\Master;

use Illuminate\Database\Seeder;
use LevelUp\Experience\Models\Level;
use LevelUp\Experience\Models\Achievement;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class LevelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Achievement::create([
            'name' => 'Classic',
            'is_secret' => false,
            'description' => 'Konsumen baru daftarkan akun',
            'image' => 'storage/app/achievements/classic.png',
        ]);
        Achievement::create([
            'name' => 'Bronze',
            'is_secret' => false,
            'description' => 'Konsumen telah mendapatkan 100 XP',
            'image' => 'storage/app/achievements/bronze.png',
        ]);
        Achievement::create([
            'name' => 'Silver',
            'is_secret' => false,
            'description' => 'Konsumen telah mendapatkan 1.000 XP',
            'image' => 'storage/app/achievements/silver.png',
        ]);
        Achievement::create([
            'name' => 'Gold',
            'is_secret' => false,
            'description' => 'Konsumen telah mendapatkan 10.000 XP',
            'image' => 'storage/app/achievements/gold.png',
        ]);
        Achievement::create([
            'name' => 'Platinum',
            'is_secret' => false,
            'description' => 'Konsumen telah mendapatkan 100.000 XP',
            'image' => 'storage/app/achievements/platinum.png',
        ]);
        Level::add(
            ['level' => 1, 'next_level_experience' => null],
            ['level' => 2, 'next_level_experience' => 100],
            ['level' => 3, 'next_level_experience' => 1000],
            ['level' => 4, 'next_level_experience' => 10000],
            ['level' => 5, 'next_level_experience' => 100000],
        );
    }
}
