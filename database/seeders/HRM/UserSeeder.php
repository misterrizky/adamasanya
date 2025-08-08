<?php

namespace Database\Seeders\HRM;

use App\Models\User;
use App\Models\UserBank;
use App\Models\UserFamily;
use App\Models\UserAddress;
use App\Models\UserProfile;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use LevelUp\Experience\Models\Level;
use Spatie\Permission\Models\Permission;
use LevelUp\Experience\Models\Achievement;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            'Super Admin' => ['*'], // Akses penuh
            'Owner' => [
                'manage bank', 'manage banner',
                'manage blood type', 'manage brand',
                'manage coupon', 'manage category',
                'manage faq category', 'manage faq',
                'manage profession', 'manage religion',
                'manage branch', 'manage branch schedule',
                'manage product', 'manage product rent', 'manage product sell',
                'manage regional', 'manage currency', 'manage language', 'manage timezone',
                'manage customer', 'manage employee',
                'manage transaction rent', 'manage transaction sell'
            ],
            'Cabang' => [
                'manage product rent', 'manage product sell',
                'view customer', 'edit customer', 'verify customer', 'resend verification', 'ban customer',
                'edit branch schedule', 'manage branch products', 'view product rating',
                'manage branch transactions'
            ],
            'Pegawai' => [
                'view product', 'create product','edit product',
                'view product rent', 'create product rent','edit product rent',
                'view product sell', 'create product sell', 'edit product sell',
                'view customer', 'edit customer', 'verify customer', 'resend verification',
                'edit branch schedule', 'manage branch products', 'view product rating',
                'manage branch transactions'
            ],
            'Konsumen' => [
                'register', 'kyc', 'view product', 'view product details',
                'delete account', 'edit profile', 'edit address', 'edit family', 'checkout',
                'print invoice', 'view transaction', 'view transaction details',
                'view wallet', 'withdraw wallet',
                'view achievement', 'view level', 'view coupon',
            ]
        ];
        $permission = [
            'manage bank', 'manage banner',
            'manage blood type', 'manage brand',
            'manage coupon', 'manage category',
            'manage faq category', 'manage faq',
            'manage profession', 'manage religion',
            'manage branch', 'manage branch schedule',
            'manage product', 'manage product rent', 'manage product sell',
            'manage regional', 'manage currency', 'manage language', 'manage timezone',
            'manage customer', 'manage employee',
            'manage transaction rent', 'manage transaction sell',
            'manage product rent', 'manage product sell',
            'view customer', 'edit customer', 'verify customer', 'resend verification', 'ban customer',
            'edit branch schedule', 'manage branch products', 'view product rating',
            'manage branch transactions',
            'view product', 'create product','edit product',
            'view product rent', 'create product rent','edit product rent',
            'view product sell', 'create product sell', 'edit product sell',
            'view customer', 'edit customer', 'verify customer', 'resend verification',
            'edit branch schedule', 'manage branch products', 'view product rating',
            'manage branch transactions',
            'register', 'kyc', 'view product', 'view product details',
            'delete account', 'edit profile', 'edit address', 'edit family', 'checkout',
            'print invoice', 'view transaction', 'view transaction details',
            'view wallet', 'withdraw wallet',
            'view achievement', 'view level', 'view coupon',
        ];
        foreach ($permission as $perm) {
            if (!Permission::where('name', $perm)->exists()) {
                Permission::create(['name' => $perm, 'guard_name' => 'web']);
            }
        }
        foreach ($roles as $roleName => $permissions) {
            $role = Role::create(['name' => $roleName, 'guard_name' => 'web']);
            if($roleName != 'Super Admin') {
                $role->syncPermissions($permissions);
                continue;
            }
        }

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
        $file = database_path('data/hrm/users.json');
        $json = file_get_contents($file);
        $data = json_decode($json, true);
        User::insert($data);
        
        $file = database_path('data/hrm/user_addresses.json');
        $json = file_get_contents($file);
        $data = json_decode($json, true);
        UserAddress::insert($data);

        $file = database_path('data/hrm/user_banks.json');
        $json = file_get_contents($file);
        $data = json_decode($json, true);
        UserBank::insert($data);

        $file = database_path('data/hrm/user_families.json');
        $json = file_get_contents($file);
        $data = json_decode($json, true);
        UserFamily::insert($data);

        $file = database_path('data/hrm/user_profiles.json');
        $json = file_get_contents($file);
        $data = json_decode($json, true);
        UserProfile::insert($data);
        
        foreach (User::where('id', '<', 16)->get() as $user) {
            if($user->id == 1) {
                $user->assignRole('Super Admin');
                continue;
            }
            if($user->id == 2 || $user->id == 3) {
                $user->assignRole('Owner');
                continue;
            }
            if($user->id >= 4 || $user->id <= 15) {
                $user->assignRole('Cabang');
                continue;
            }
            if($user->id > 15) {
                $user->assignRole('Konsumen');
                continue;
            }
        }
    }
}
