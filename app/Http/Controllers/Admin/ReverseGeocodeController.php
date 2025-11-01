<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\District;
use App\Models\Province;
use App\Models\Regency;
use App\Models\Village;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ReverseGeocodeController extends Controller
{
    /**
     * Get administrative data from coordinates
     */
    public function getAdministrativeData(Request $request)
    {
        $request->validate([
            'lat' => 'required|numeric',
            'lng' => 'required|numeric',
        ]);

        $lat = $request->lat;
        $lng = $request->lng;

        try {
            // Find closest village by coordinates (PRIMARY METHOD)
            $village = $this->findClosestVillage($lat, $lng);

            if ($village) {
                return response()->json([
                    'success' => true,
                    'data' => [
                        'province_id' => $village->district->regency->province_id,
                        'province_name' => $village->district->regency->province->name,
                        'regency_id' => $village->district->regency_id,
                        'regency_name' => $village->district->regency->name,
                        'district_id' => $village->district_id,
                        'district_name' => $village->district->name,
                        'village_id' => $village->id,
                        'village_name' => $village->name,
                        'location_text' => $this->buildLocationText($village),
                    ],
                ]);
            }

            // Try with larger radius if not found
            $village = $this->findClosestVillage($lat, $lng, 25);

            if ($village) {
                return response()->json([
                    'success' => true,
                    'data' => [
                        'province_id' => $village->district->regency->province_id,
                        'province_name' => $village->district->regency->province->name,
                        'regency_id' => $village->district->regency_id,
                        'regency_name' => $village->district->regency->name,
                        'district_id' => $village->district_id,
                        'district_name' => $village->district->name,
                        'village_id' => $village->id,
                        'village_name' => $village->name,
                        'location_text' => $this->buildLocationText($village),
                    ],
                    'message' => 'Data diambil dari lokasi terdekat',
                ]);
            }

            // No village found within radius - return empty for manual selection
            return response()->json([
                'success' => true,
                'data' => [
                    'province_id' => null,
                    'regency_id' => null,
                    'district_id' => null,
                    'village_id' => null,
                    'location_text' => "Lat: {$lat}, Lng: {$lng}",
                ],
                'message' => 'Tidak ada desa terdekat ditemukan. Silakan pilih lokasi secara manual',
            ]);

        } catch (\Exception $e) {
            Log::error('Reverse Geocoding Error: '.$e->getMessage(), [
                'lat' => $lat,
                'lng' => $lng,
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan. Silakan pilih lokasi secara manual',
                'error' => config('app.debug') ? $e->getMessage() : null,
            ], 200);
        }
    }

    /**
     * Find closest village by coordinates using Haversine formula
     */
    private function findClosestVillage($lat, $lng, $maxDistance = 10)
    {
        $village = Village::whereNotNull('lat')
            ->whereNotNull('lng')
            ->where('lat', '!=', 0)
            ->where('lng', '!=', 0)
            ->select('*')
            ->selectRaw(
                '( 6371 * acos( cos( radians(?) ) * cos( radians( lat ) ) * cos( radians( lng ) - radians(?) ) + sin( radians(?) ) * sin( radians( lat ) ) ) ) AS distance',
                [$lat, $lng, $lat]
            )
            ->having('distance', '<', $maxDistance)
            ->orderBy('distance')
            ->with(['district.regency.province'])
            ->first();

        return $village;
    }

    /**
     * Get regencies by province
     */
    public function getRegencies($provinceId)
    {
        $regencies = Regency::where('province_id', $provinceId)
            ->orderBy('name')
            ->get(['id', 'name', 'type']);

        return response()->json([
            'success' => true,
            'data' => $regencies,
        ]);
    }

    /**
     * Get districts by regency
     */
    public function getDistricts($regencyId)
    {
        $districts = District::where('regency_id', $regencyId)
            ->orderBy('name')
            ->get(['id', 'name']);

        return response()->json([
            'success' => true,
            'data' => $districts,
        ]);
    }

    /**
     * Get villages by district
     */
    public function getVillages($districtId)
    {
        $villages = Village::where('district_id', $districtId)
            ->orderBy('name')
            ->get(['id', 'name']);

        return response()->json([
            'success' => true,
            'data' => $villages,
        ]);
    }

    /**
     * Build location text from village data
     */
    private function buildLocationText($village)
    {
        $parts = [];

        if ($village->name) {
            $parts[] = $village->name;
        }

        if ($village->district && $village->district->name) {
            $parts[] = 'Kec. '.$village->district->name;
        }

        if ($village->district && $village->district->regency) {
            $regency = $village->district->regency;
            $regencyName = $regency->type ? $regency->type.' '.$regency->name : $regency->name;
            $parts[] = $regencyName;
        }

        if ($village->district && $village->district->regency && $village->district->regency->province) {
            $parts[] = $village->district->regency->province->name;
        }

        return implode(', ', $parts);
    }
}
