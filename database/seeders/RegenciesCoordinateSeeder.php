<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RegenciesCoordinateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $coordinates = [
            // Kabupaten
            ['code' => '3201-KB', 'lat' => -6.5944, 'lng' => 106.7892],   // Kabupaten Bogor
            ['code' => '3202-KS', 'lat' => -6.9278, 'lng' => 106.9271],   // Kabupaten Sukabumi
            ['code' => '3203-KC', 'lat' => -6.8186, 'lng' => 107.1425],   // Kabupaten Cianjur
            ['code' => '3204-KB', 'lat' => -7.0051, 'lng' => 107.5619],   // Kabupaten Bandung
            ['code' => '3205-KG', 'lat' => -7.2245, 'lng' => 107.8991],   // Kabupaten Garut
            ['code' => '3206-KT', 'lat' => -7.3506, 'lng' => 108.2170],   // Kabupaten Tasikmalaya
            ['code' => '3207-KC', 'lat' => -7.3257, 'lng' => 108.3534],   // Kabupaten Ciamis
            ['code' => '3208-KK', 'lat' => -6.9761, 'lng' => 108.4841],   // Kabupaten Kuningan
            ['code' => '3209-KC', 'lat' => -6.7063, 'lng' => 108.5571],   // Kabupaten Cirebon
            ['code' => '3210-KM', 'lat' => -6.8396, 'lng' => 108.2278],   // Kabupaten Majalengka
            ['code' => '3211-KS', 'lat' => -6.8571, 'lng' => 107.9238],   // Kabupaten Sumedang
            ['code' => '3212-KI', 'lat' => -6.3274, 'lng' => 108.3200],   // Kabupaten Indramayu
            ['code' => '3213-KS', 'lat' => -6.5694, 'lng' => 107.7606],   // Kabupaten Subang
            ['code' => '3214-KP', 'lat' => -6.5569, 'lng' => 107.4431],   // Kabupaten Purwakarta
            ['code' => '3215-KK', 'lat' => -6.3384, 'lng' => 107.3001],   // Kabupaten Karawang
            ['code' => '3216-KB', 'lat' => -6.2349, 'lng' => 107.1537],   // Kabupaten Bekasi
            ['code' => '3217-KBB', 'lat' => -6.8414, 'lng' => 107.4847],  // Kabupaten Bandung Barat
            ['code' => '3218-KP', 'lat' => -7.6840, 'lng' => 108.6500],   // Kabupaten Pangandaran

            // Kota
            ['code' => '3271-KB', 'lat' => -6.5971, 'lng' => 106.8060],   // Kota Bogor
            ['code' => '3272-KS', 'lat' => -6.9175, 'lng' => 106.9270],   // Kota Sukabumi
            ['code' => '3273-KB', 'lat' => -6.9175, 'lng' => 107.6191],   // Kota Bandung
            ['code' => '3274-KC', 'lat' => -6.7063, 'lng' => 108.5571],   // Kota Cirebon
            ['code' => '3275-KB', 'lat' => -6.2383, 'lng' => 106.9756],   // Kota Bekasi
            ['code' => '3276-KD', 'lat' => -6.4025, 'lng' => 106.7942],   // Kota Depok
            ['code' => '3277-KC', 'lat' => -6.8723, 'lng' => 107.5425],   // Kota Cimahi
            ['code' => '3278-KT', 'lat' => -7.3506, 'lng' => 108.2170],   // Kota Tasikmalaya
            ['code' => '3279-KB', 'lat' => -7.3686, 'lng' => 108.5390],   // Kota Banjar
        ];

        foreach ($coordinates as $coordinate) {
            DB::table('regencies')
                ->where('code', $coordinate['code'])
                ->update([
                    'lat' => $coordinate['lat'],
                    'lng' => $coordinate['lng'],
                    'updated_at' => now(),
                ]);
        }

        $this->command->info('Koordinat untuk '.count($coordinates).' kabupaten/kota di Jawa Barat berhasil ditambahkan!');
    }
}
