<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Province;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class ProvinceController extends Controller
{
    public function index()
    {
        return view('main.provinces.index');
    }

    public function getData()
    {
        $provinces = Province::select(['id', 'name', 'code', 'created_at']);

        return DataTables::of($provinces)
            ->addIndexColumn()
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
            'name' => 'required|string|max:100',
            'code' => 'required|string|max:100|unique:provinces,code',
        ]);

        Province::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Provinsi berhasil ditambahkan',
        ]);
    }

    public function edit($id)
    {
        $province = Province::findOrFail($id);

        return response()->json($province);
    }

    public function update(Request $request, $id)
    {
        $province = Province::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'code' => 'required|string|max:100|unique:provinces,code,'.$id,
        ]);

        $province->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Provinsi berhasil diperbarui',
        ]);
    }

    public function destroy($id)
    {
        try {
            $province = Province::findOrFail($id);
            $province->delete();

            return response()->json([
                'success' => true,
                'message' => 'Provinsi berhasil dihapus',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus provinsi: '.$e->getMessage(),
            ], 500);
        }
    }
}
