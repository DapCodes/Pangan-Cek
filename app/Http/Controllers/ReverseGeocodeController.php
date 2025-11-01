<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Province;
use App\Models\Regency;
use App\Models\District;
use App\Models\Village;

class ReverseGeoCodeController extends Controller
{
    public function getLocation(Request $request)
    {
        // Validasi koordinat
        $validated = $request->validate([
            'lat' => 'required|numeric|min:-90|max:90',
            'lng' => 'required|numeric|min:-180|max:180',
        ]);

        $lat = (float) $validated['lat'];
        $lng = (float) $validated['lng'];

        try {
            // Cari desa terdekat radius 5km
            $village = Village::selectRaw("
                    villages.*,
                    (6371 * acos(
                        cos(radians(?)) * cos(radians(lat)) *
                        cos(radians(lng) - radians(?)) +
                        sin(radians(?)) * sin(radians(lat))
                    )) AS distance
                ", [$lat, $lng, $lat])
                ->whereNotNull('lat')->whereNotNull('lng')
                ->havingRaw('distance < ?', [5])
                ->orderBy('distance')
                ->with(['district.regency.province'])
                ->first();

            if ($village) {
                $locationText = sprintf(
                    '%s, %s, %s, %s',
                    $village->name,
                    $village->district->name,
                    $village->district->regency->name,
                    $village->district->regency->province->name
                );

                return response()->json([
                    'success' => true,
                    'data' => [
                        'village_id'    => $village->id,
                        'village_name'  => $village->name,
                        'district_id'   => $village->district_id,
                        'district_name' => $village->district->name,
                        'regency_id'    => $village->district->regency_id,
                        'regency_name'  => $village->district->regency->name,
                        'province_id'   => $village->district->regency->province_id,
                        'province_name' => $village->district->regency->province->name,
                        'location_text' => $locationText,
                        'distance'      => round($village->distance, 2),
                    ],
                ]);
            }

            // Jika tidak ada desa, cari kecamatan radius 10km
            $district = District::selectRaw("
                    districts.*,
                    (6371 * acos(
                        cos(radians(?)) * cos(radians(lat)) *
                        cos(radians(lng) - radians(?)) +
                        sin(radians(?)) * sin(radians(lat))
                    )) AS distance
                ", [$lat, $lng, $lat])
                ->whereNotNull('lat')->whereNotNull('lng')
                ->havingRaw('distance < ?', [10])
                ->orderBy('distance')
                ->with(['regency.province'])
                ->first();

            if ($district) {
                $locationText = sprintf(
                    '%s, %s, %s',
                    $district->name,
                    $district->regency->name,
                    $district->regency->province->name
                );

                return response()->json([
                    'success' => true,
                    'data' => [
                        'village_id'    => null,
                        'village_name'  => null,
                        'district_id'   => $district->id,
                        'district_name' => $district->name,
                        'regency_id'    => $district->regency_id,
                        'regency_name'  => $district->regency->name,
                        'province_id'   => $district->regency->province_id,
                        'province_name' => $district->regency->province->name,
                        'location_text' => $locationText,
                        'distance'      => round($district->distance, 2),
                    ],
                ]);
            }

            // Jika tidak ada kecamatan, cari kab/kota radius 20km
            $regency = Regency::selectRaw("
                    regencies.*,
                    (6371 * acos(
                        cos(radians(?)) * cos(radians(lat)) *
                        cos(radians(lng) - radians(?)) +
                        sin(radians(?)) * sin(radians(lat))
                    )) AS distance
                ", [$lat, $lng, $lat])
                ->whereNotNull('lat')->whereNotNull('lng')
                ->havingRaw('distance < ?', [20])
                ->orderBy('distance')
                ->with(['province'])
                ->first();

            if ($regency) {
                $locationText = sprintf('%s, %s', $regency->name, $regency->province->name);

                return response()->json([
                    'success' => true,
                    'data' => [
                        'village_id'    => null,
                        'village_name'  => null,
                        'district_id'   => null,
                        'district_name' => null,
                        'regency_id'    => $regency->id,
                        'regency_name'  => $regency->name,
                        'province_id'   => $regency->province_id,
                        'province_name' => $regency->province->name,
                        'location_text' => $locationText,
                        'distance'      => round($regency->distance, 2),
                    ],
                ]);
            }

            // Tidak ditemukan
            return response()->json([
                'success' => false,
                'message' => 'Lokasi tidak ditemukan dalam database. Koordinat tetap dapat disimpan.'
            ], 404);

        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal melakukan reverse geocoding: '.$e->getMessage()
            ], 500);
        }
    }
}
