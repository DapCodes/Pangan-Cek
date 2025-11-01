<?php
namespace App\Http\Controllers;

use App\Models\Province;
use App\Models\Regency;
use App\Models\District;
use App\Models\Village;
use Illuminate\Http\Request;

class LocationController extends Controller
{
    public function getProvinces()
    {
        $provinces = Province::select('id', 'name')
            ->orderBy('name')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $provinces
        ]);
    }

    public function getRegencies($provinceId)
    {
        $regencies = Regency::where('province_id', $provinceId)
            ->select('id', 'name', 'province_id')
            ->orderBy('name')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $regencies
        ]);
    }

    public function getDistricts($regencyId)
    {
        $districts = District::where('regency_id', $regencyId)
            ->select('id', 'name', 'regency_id')
            ->orderBy('name')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $districts
        ]);
    }

    public function getVillages($districtId)
    {
        $villages = Village::where('district_id', $districtId)
            ->select('id', 'name', 'district_id')
            ->orderBy('name')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $villages
        ]);
    }
}