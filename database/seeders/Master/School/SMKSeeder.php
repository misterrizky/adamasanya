<?php

namespace Database\Seeders\Master\School;

use App\Models\Master\School;
use App\Models\Region\City;
use Illuminate\Support\Str;
use App\Models\Region\State;
use Illuminate\Database\Seeder;
use App\Models\Region\Subdistrict;
use Illuminate\Support\Facades\Http;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class SMKSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $url = 'https://api-sekolah-indonesia.vercel.app/sekolah/SMK';
        $perPage = 1000; // Ubah sesuai kebutuhan

        // Hitung total halaman berdasarkan perPage
        $response = Http::get("$url?page=1&perPage=1");
        $total = $response['total_data'] ?? 0;

        // Loop untuk setiap halaman
        for ($page = 1; $page <= ceil($total / $perPage); $page++) {
            $response = Http::get("$url?page=$page&perPage=$perPage");
            $dataSekolah = $response['dataSekolah'] ?? [];

            foreach ($dataSekolah as $item) {
                // Cari provinsi
                $province_name = Str::remove(['Prov. ', '.'], $item['propinsi']);
                $province = State::where('name', $province_name)->first();

                // Cari kota
                $city_name = Str::remove(['Kab. ', 'Kota '], $item['kabupaten_kota']);
                $city = null;
                if ($province) {
                    $city = $province->cities()->where('name', $city_name)->first();
                } else {
                    $city = City::where('name', $city_name)->first();
                }

                // Cari kecamatan
                $subdistrict_name = Str::remove('Kec. ', $item['kecamatan']);
                $subdistrict = null;
                if ($city) {
                    $subdistrict = $city->subdistricts()->where('name', $subdistrict_name)->first();
                } else {
                    $subdistrict = Subdistrict::where('name', $subdistrict_name)->first();
                }

                // Simpan data sekolah
                School::insert([
                    'school_level_id' => 4,
                    'code' => $item['id'] ?? null,
                    'npsn' => $item['npsn'],
                    'province_id' => $province ? $province->id : 0,
                    'city_id' => $city ? $city->id : 0,
                    'subdistrict_id' => $subdistrict ? $subdistrict->id : 0,
                    'name' => $item['sekolah'],
                    'address' => $item['alamat_jalan'],
                    'lat' => $item['lintang'],
                    'lng' => $item['bujur'],
                ]);
            }
        }
    }
}
