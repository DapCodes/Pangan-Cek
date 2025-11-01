<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CommoditySeeder extends Seeder
{
    public function run()
    {
        $commodities = [
            ['name' => 'Beras Premium', 'unit' => 'kg', 'category' => 'Bahan Pokok'],
            ['name' => 'Beras Medium', 'unit' => 'kg', 'category' => 'Bahan Pokok'],
            ['name' => 'Gula Pasir', 'unit' => 'kg', 'category' => 'Bahan Pokok'],
            ['name' => 'Minyak Goreng Curah', 'unit' => 'liter', 'category' => 'Bahan Pokok'],
            ['name' => 'Minyak Goreng Kemasan', 'unit' => 'liter', 'category' => 'Bahan Pokok'],
            ['name' => 'Telur Ayam', 'unit' => 'kg', 'category' => 'Protein'],
            ['name' => 'Daging Ayam', 'unit' => 'kg', 'category' => 'Protein'],
            ['name' => 'Daging Sapi', 'unit' => 'kg', 'category' => 'Protein'],
            ['name' => 'Cabai Merah', 'unit' => 'kg', 'category' => 'Sayuran'],
            ['name' => 'Cabai Rawit', 'unit' => 'kg', 'category' => 'Sayuran'],
            ['name' => 'Bawang Merah', 'unit' => 'kg', 'category' => 'Sayuran'],
            ['name' => 'Bawang Putih', 'unit' => 'kg', 'category' => 'Sayuran'],
            ['name' => 'Tomat', 'unit' => 'kg', 'category' => 'Sayuran'],
            ['name' => 'Tepung Terigu', 'unit' => 'kg', 'category' => 'Bahan Pokok'],
        ];

        foreach ($commodities as $commodity) {
            DB::table('commodities')->insert([
                'name' => $commodity['name'],
                'unit' => $commodity['unit'],
                'category' => $commodity['category'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}