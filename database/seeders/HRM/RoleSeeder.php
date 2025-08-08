<?php

namespace Database\Seeders\HRM;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $file = public_path('json/hrm/role.json');
        $json = file_get_contents($file);
        $data = json_decode($json, true);
        Role::insert($data);
    }
}
