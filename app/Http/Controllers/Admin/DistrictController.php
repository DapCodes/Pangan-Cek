<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\District;
use App\Models\Province;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class DistrictController extends Controller
{
    public function index()
    {
        $provinces = Province::orderBy('name')->get();

        return view('main.districts.index', compact('provinces'));
    }

    public function getData()
    {
        $districts = District::with(['regency.province'])->select(['id', 'regency_id', 'name', 'code', 'created_at']);

        return DataTables::of($districts)
            ->addIndexColumn()
            ->addColumn('regency_name', function ($row) {
                return $row->regency->name;
            })
            ->addColumn('province_name', function ($row) {
                return $row->regency->province->name;
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
            'regency_id' => 'required|exists:regencies,id',
            'name' => 'required|string|max:100',
            'code' => 'required|string|max:100|unique:districts,code',
        ]);

        District::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Kecamatan berhasil ditambahkan',
        ]);
    }

    public function edit($id)
    {
        $district = District::with('regency')->findOrFail($id);

        return response()->json([
            'id' => $district->id,
            'regency_id' => $district->regency_id,
            'province_id' => $district->regency->province_id,
            'name' => $district->name,
            'code' => $district->code,
        ]);
    }

    public function update(Request $request, $id)
    {
        $district = District::findOrFail($id);

        $validated = $request->validate([
            'regency_id' => 'required|exists:regencies,id',
            'name' => 'required|string|max:100',
            'code' => 'required|string|max:100|unique:districts,code,'.$id,
        ]);

        $district->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Kecamatan berhasil diperbarui',
        ]);
    }

    public function destroy($id)
    {
        try {
            $district = District::findOrFail($id);
            $district->delete();

            return response()->json([
                'success' => true,
                'message' => 'Kecamatan berhasil dihapus',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus kecamatan: '.$e->getMessage(),
            ], 500);
        }
    }

    public function getByRegency($regencyId)
    {
        $districts = District::where('regency_id', $regencyId)->orderBy('name')->get();

        return response()->json($districts);
    }
}
