<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Province;
use App\Models\Village;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class VillageController extends Controller
{
    public function index()
    {
        $provinces = Province::orderBy('name')->get();

        return view('main.villages.index', compact('provinces'));
    }

    public function getData()
    {
        $villages = Village::with(['district.regency.province'])
            ->select(['id', 'district_id', 'name', 'code', 'lat', 'lng', 'created_at']);

        return DataTables::of($villages)
            ->addIndexColumn()
            ->addColumn('district_name', function ($row) {
                return $row->district->name;
            })
            ->addColumn('regency_name', function ($row) {
                return $row->district->regency->name;
            })
            ->addColumn('province_name', function ($row) {
                return $row->district->regency->province->name;
            })
            ->addColumn('coordinates', function ($row) {
                if ($row->lat && $row->lng) {
                    return $row->lat.', '.$row->lng;
                }

                return '-';
            })
            ->addColumn('action', function ($row) {
                return '
                    <button type="button" class="btn btn-sm btn-primary edit-btn" data-id="'.$row->id.'">
                        <i class="bx bx-edit"></i>
                    </button>
                    <button type="button" class="btn btn-sm btn-danger delete-btn" data-id="'.$row->id.'">
                        <i class="bx bx-trash"></i>
                    </button>
                ';
            })
            ->editColumn('created_at', function ($row) {
                return $row->created_at->format('d-m-Y H:i');
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'district_id' => 'required|exists:districts,id',
            'name' => 'required|string|max:100',
            'code' => 'required|string|max:100|unique:villages,code',
            'lat' => 'nullable|numeric|between:-90,90',
            'lng' => 'nullable|numeric|between:-180,180',
        ]);

        Village::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Desa/Kelurahan berhasil ditambahkan',
        ]);
    }

    public function edit($id)
    {
        $village = Village::with(['district.regency'])->findOrFail($id);

        return response()->json([
            'id' => $village->id,
            'district_id' => $village->district_id,
            'regency_id' => $village->district->regency_id,
            'province_id' => $village->district->regency->province_id,
            'name' => $village->name,
            'code' => $village->code,
            'lat' => $village->lat,
            'lng' => $village->lng,
        ]);
    }

    public function update(Request $request, $id)
    {
        $village = Village::findOrFail($id);

        $validated = $request->validate([
            'district_id' => 'required|exists:districts,id',
            'name' => 'required|string|max:100',
            'code' => 'required|string|max:100|unique:villages,code,'.$id,
            'lat' => 'nullable|numeric|between:-90,90',
            'lng' => 'nullable|numeric|between:-180,180',
        ]);

        $village->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Desa/Kelurahan berhasil diperbarui',
        ]);
    }

    public function destroy($id)
    {
        try {
            $village = Village::findOrFail($id);
            $village->delete();

            return response()->json([
                'success' => true,
                'message' => 'Desa/Kelurahan berhasil dihapus',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus desa/kelurahan: '.$e->getMessage(),
            ], 500);
        }
    }

    public function getByDistrict($districtId)
    {
        $villages = Village::where('district_id', $districtId)->orderBy('name')->get();

        return response()->json($villages);
    }
}
