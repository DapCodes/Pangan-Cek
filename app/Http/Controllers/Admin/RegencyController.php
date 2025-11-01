<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Province;
use App\Models\Regency;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class RegencyController extends Controller
{
    public function index()
    {
        $provinces = Province::orderBy('name')->get();

        return view('main.regencies.index', compact('provinces'));
    }

    public function getData()
    {
        $regencies = Regency::with('province')->select(['id', 'province_id', 'name', 'type', 'code', 'created_at']);

        return DataTables::of($regencies)
            ->addIndexColumn()
            ->addColumn('province_name', function ($row) {
                return $row->province->name;
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
            ->editColumn('type', function ($row) {
                return ucfirst($row->type);
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
            'province_id' => 'required|exists:provinces,id',
            'name' => 'required|string|max:100',
            'type' => 'required|in:kabupaten,kota',
            'code' => 'required|string|max:100|unique:regencies,code',
        ]);

        Regency::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Kabupaten/Kota berhasil ditambahkan',
        ]);
    }

    public function edit($id)
    {
        $regency = Regency::findOrFail($id);

        return response()->json($regency);
    }

    public function update(Request $request, $id)
    {
        $regency = Regency::findOrFail($id);

        $validated = $request->validate([
            'province_id' => 'required|exists:provinces,id',
            'name' => 'required|string|max:100',
            'type' => 'required|in:kabupaten,kota',
            'code' => 'required|string|max:100|unique:regencies,code,'.$id,
        ]);

        $regency->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Kabupaten/Kota berhasil diperbarui',
        ]);
    }

    public function destroy($id)
    {
        try {
            $regency = Regency::findOrFail($id);
            $regency->delete();

            return response()->json([
                'success' => true,
                'message' => 'Kabupaten/Kota berhasil dihapus',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus kabupaten/kota: '.$e->getMessage(),
            ], 500);
        }
    }

    public function getByProvince($provinceId)
    {
        $regencies = Regency::where('province_id', $provinceId)->orderBy('name')->get();

        return response()->json($regencies);
    }
}
