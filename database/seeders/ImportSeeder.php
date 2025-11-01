<?php

namespace Database\Seeders;

use Exception;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

class ImportSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('🚀 Starting Master Wilayah import...');

        try {
            // === Path file masterdata ===
            $files = [
                'provinces' => database_path('seeders/masterdata/provinces-masterdata.xlsx'),
                'regencies' => database_path('seeders/masterdata/regencies-masterdata.xlsx'),
                'districts' => database_path('seeders/masterdata/districts-masterdata.xlsx'),
                'villages' => database_path('seeders/masterdata/villages-masterdata.xlsx'),
            ];

            // === Validasi file ada semua ===
            foreach ($files as $name => $path) {
                if (! file_exists($path)) {
                    throw new Exception("File Excel untuk {$name} tidak ditemukan: {$path}");
                }
            }

            /*
            |--------------------------------------------------------------------------
            | 1️⃣ PROVINCES
            |--------------------------------------------------------------------------
            | Kolom: id, name, code
            */
            $this->command->info('Importing Provinces...');
            $provinces = Excel::toArray([], $files['provinces'])[0];
            $countProvinces = 0;

            foreach (array_slice($provinces, 1) as $row) {
                $id = $row[0] ?? null;
                $name = $row[1] ?? null;
                $code = $row[2] ?? null;

                if (! $id || ! $name || ! $code) {
                    continue;
                }

                DB::table('provinces')->updateOrInsert(
                    ['id' => (int) $id],
                    [
                        'name' => trim($name),
                        'code' => trim($code),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]
                );
                $countProvinces++;
            }

            $this->command->info("✓ Imported {$countProvinces} provinces.");

            /*
            |--------------------------------------------------------------------------
            | 2️⃣ REGENCIES
            |--------------------------------------------------------------------------
            | Kolom: id, province_id, name, type, code, lat, lng
            */
            $this->command->info('Importing Regencies...');
            $regencies = Excel::toArray([], $files['regencies'])[0];
            $countRegencies = 0;

            foreach (array_slice($regencies, 1) as $row) {
                [$id, $province_id, $name, $type, $code, $lat, $lng] = array_pad($row, 7, null);

                if (! $id || ! $province_id || ! $name) {
                    continue;
                }

                // Tentukan type otomatis bila kosong
                $cleanType = strtolower(trim($type ?? ''));
                if (empty($cleanType) || str_starts_with($cleanType, '=')) {
                    $nameLower = strtolower($name);
                    $cleanType = str_contains($nameLower, 'kota') ? 'kota' : 'kabupaten';
                }

                // Bersihkan & perbaiki lat/lng (auto-swap jika terdeteksi)
                [$cleanLat, $cleanLng] = $this->smartCleanLatLng($lat, $lng, $id, 'Regency');

                // Validasi province_id
                $existsProvince = DB::table('provinces')->where('id', (int) $province_id)->exists();
                if (! $existsProvince) {
                    $this->command->warn("⚠ Regency ID {$id}: Province ID {$province_id} tidak ditemukan, skip.");

                    continue;
                }

                DB::table('regencies')->updateOrInsert(
                    ['id' => (int) $id],
                    [
                        'province_id' => (int) $province_id,
                        'name' => trim($name),
                        'type' => $cleanType,
                        'code' => trim($code ?? ''),
                        'lat' => $cleanLat,
                        'lng' => $cleanLng,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]
                );
                $countRegencies++;
            }

            $this->command->info("✓ Imported {$countRegencies} regencies.");

            /*
            |--------------------------------------------------------------------------
            | 3️⃣ DISTRICTS
            |--------------------------------------------------------------------------
            | Kolom: id, regency_id, name, code, lat, lng
            */
            $this->command->info('Importing Districts...');
            $districts = Excel::toArray([], $files['districts'])[0];
            $countDistricts = 0;

            foreach (array_slice($districts, 1) as $row) {
                [$id, $regency_id, $name, $code, $lat, $lng] = array_pad($row, 6, null);

                if (! $id || ! $regency_id || ! $name) {
                    continue;
                }

                [$cleanLat, $cleanLng] = $this->smartCleanLatLng($lat, $lng, $id, 'District');

                // Validasi regency_id
                $existsRegency = DB::table('regencies')->where('id', (int) $regency_id)->exists();
                if (! $existsRegency) {
                    $this->command->warn("⚠ District ID {$id}: Regency ID {$regency_id} tidak ditemukan, skip.");

                    continue;
                }

                DB::table('districts')->updateOrInsert(
                    ['id' => (int) $id],
                    [
                        'regency_id' => (int) $regency_id,
                        'name' => trim($name),
                        'code' => trim($code ?? ''),
                        'lat' => $cleanLat,
                        'lng' => $cleanLng,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]
                );
                $countDistricts++;
            }

            $this->command->info("✓ Imported {$countDistricts} districts.");

            /*
            |--------------------------------------------------------------------------
            | 4️⃣ VILLAGES
            |--------------------------------------------------------------------------
            | Kolom: id, district_id, name, code, lat, lng
            */
            $this->command->info('Importing Villages...');
            $villages = Excel::toArray([], $files['villages'])[0];
            $countVillages = 0;

            foreach (array_slice($villages, 1) as $row) {
                [$id, $district_id, $name, $code, $lat, $lng] = array_pad($row, 6, null);

                if (! $id || ! $district_id || ! $name) {
                    continue;
                }

                [$cleanLat, $cleanLng] = $this->smartCleanLatLng($lat, $lng, $id, 'Village');

                // Validasi district_id
                $existsDistrict = DB::table('districts')->where('id', (int) $district_id)->exists();
                if (! $existsDistrict) {
                    $this->command->warn("⚠ Village ID {$id}: District ID {$district_id} tidak ditemukan, skip.");

                    continue;
                }

                DB::table('villages')->updateOrInsert(
                    ['id' => (int) $id],
                    [
                        'district_id' => (int) $district_id,
                        'name' => trim($name),
                        'code' => trim($code ?? ''),
                        'lat' => $cleanLat,
                        'lng' => $cleanLng,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]
                );
                $countVillages++;
            }

            $this->command->info("✓ Imported {$countVillages} villages.");
            $this->command->info('✅ Master Wilayah import completed successfully!');

        } catch (Exception $e) {
            Log::error('❌ Import failed: '.$e->getMessage());
            $this->command->error('❌ Import failed: '.$e->getMessage());
        }
    }

    /**
     * Versi "pintar" untuk membersihkan dan memperbaiki lat/lng.
     * Mendeteksi pola umum:
     *  - jika lat > 90 dan lng dalam range latitude => kolom tertukar (swap)
     *  - jika lat kecil positif (0..15) dan lng antara 95..141 => kemungkinan tanda '-' hilang -> ubah lat menjadi negative
     *  - jika lat antara 95..141 dan lng antara 0..15 => kemungkinan swap dan butuh flip sign untuk latitude
     *
     * Semua perubahan dicatat via $this->command->info/warn untuk trace.
     */
    private function smartCleanLatLng($latRaw, $lngRaw, $id, $label): array
    {
        $latNum = is_numeric($latRaw) ? (float) $latRaw : null;
        $lngNum = is_numeric($lngRaw) ? (float) $lngRaw : null;

        // early return if both null
        if (is_null($latNum) && is_null($lngNum)) {
            return [null, null];
        }

        $original = ['lat' => $latNum, 'lng' => $lngNum];

        // Rule 1: Jika lat out-of-range (>90) dan lng masuk akal sebagai latitude (<=90),
        // maka kemungkinan kolom tertukar -> swap
        if (! is_null($latNum) && abs($latNum) > 90 && ! is_null($lngNum) && abs($lngNum) <= 90) {
            $this->command->info("↔ Auto-swap detected for {$label} ID {$id}: lat={$latNum} looks like longitude; swapping with lng={$lngNum}.");
            $temp = $latNum;
            $latNum = $lngNum;
            $lngNum = $temp;
        }

        // Rule 2: Jika lat kecil positif (0..15) dan lng masuk rentang Indonesia (95..141),
        // kemungkinan tanda minus hilang untuk lat (Indonesia berada di selatan -> lat negatif)
        if (! is_null($latNum) && $latNum > 0 && $latNum <= 15 && ! is_null($lngNum) && $lngNum >= 95 && $lngNum <= 141) {
            $this->command->info("↕ Flipping sign for {$label} ID {$id}: lat={$latNum} appears positive while lng={$lngNum} in Indonesian range -> lat = -{$latNum}");
            $latNum = -$latNum;
        }

        // Rule 3: Jika lat berada di range longitude (95..141) dan lng kecil (0..15) => kemungkinan juga swap
        if (! is_null($latNum) && $latNum >= 95 && $latNum <= 141 && ! is_null($lngNum) && $lngNum >= 0 && $lngNum <= 15) {
            $this->command->info("↔ Auto-swap+flip detected for {$label} ID {$id}: lat={$latNum} seems longitude and lng={$lngNum} seems latitude -> swapping and flipping sign for lat.");
            $temp = $latNum;
            $latNum = -$lngNum; // make latitude negative (Indonesia)
            $lngNum = $temp;
        }

        // Validasi rentang akhir
        if (! is_null($latNum) && ($latNum < -90 || $latNum > 90)) {
            $this->command->warn("⚠ {$label} ID {$id}: Final Latitude {$latNum} out of range, set to null.");
            $latNum = null;
        }
        if (! is_null($lngNum) && ($lngNum < -180 || $lngNum > 180)) {
            $this->command->warn("⚠ {$label} ID {$id}: Final Longitude {$lngNum} out of range, set to null.");
            $lngNum = null;
        }

        // Jika ada perubahan, catat log info singkat
        if ($original['lat'] !== $latNum || $original['lng'] !== $lngNum) {
            $this->command->info("→ {$label} ID {$id} normalized: from lat={$original['lat']}, lng={$original['lng']} to lat={$latNum}, lng={$lngNum}");
        }

        return [$latNum, $lngNum];
    }
}
