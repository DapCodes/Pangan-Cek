<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class ReportSeeder extends Seeder
{
    public function run(): void
    {
        $commodityIds = [1, 2, 3];
        $sources = ['USER', 'ENUMERATOR', 'OFFICIAL'];
        $severities = ['LOW', 'MEDIUM', 'HIGH', 'CRITICAL'];

        // Batas koordinat Provinsi Jawa Barat (sekitar)
        $latMin = -7.8;
        $latMax = -5.9;
        $lngMin = 106.2;
        $lngMax = 108.8;

        $faker = \Faker\Factory::create('id_ID');

        $priceReports = [];
        $dearthReports = [];

        for ($i = 0; $i < 100; $i++) {
            $commodityId = $faker->randomElement($commodityIds);
            $latitude = $faker->randomFloat(7, $latMin, $latMax);
            $longitude = $faker->randomFloat(7, $lngMin, $lngMax);
            $date = Carbon::now()->subDays(rand(0, 30));

            // Buat laporan harga
            $priceReports[] = [
                'commodity_id' => $commodityId,
                'price' => match ($commodityId) {
                    1 => rand(12000, 16000), // Beras Premium
                    2 => rand(10000, 14000), // Beras Medium
                    3 => rand(13000, 20000), // Minyak Goreng
                },
                'lat' => $latitude,
                'lng' => $longitude,
                'quantity_unit' => match ($commodityId) {
                    1, 2 => 'kg',
                    3 => 'liter',
                },
                'source' => $faker->randomElement($sources),
                'reported_at' => $date,
                'status' => 'APPROVED',
                'created_at' => $date,
                'updated_at' => $date,
            ];

            // Buat laporan kelangkaan
            $dearthReports[] = [
                'commodity_id' => $commodityId,
                'lat' => $latitude,
                'lng' => $longitude,
                'kabupaten' => $faker->city,
                'kecamatan' => $faker->streetName,
                'severity' => $faker->randomElement($severities),
                'description' => $faker->sentence(8),
                'source' => $faker->randomElement($sources),
                'reported_at' => $date,
                'status' => 'APPROVED',
                'created_at' => $date,
                'updated_at' => $date,
            ];
        }

        DB::table('price_reports')->insert($priceReports);
        DB::table('dearth_reports')->insert($dearthReports);

        $this->command->info('✅ Dummy data laporan harga & kelangkaan pangan berhasil dibuat!');
    }
}
