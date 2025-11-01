<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Commodity;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class CommodityController extends Controller
{
    public function index()
    {
        // Menampilkan halaman utama CRUD
        return view('main.commodities.index');
    }

    public function getData()
    {
        // Query utama untuk DataTables
        $commodities = Commodity::select(['id', 'name', 'unit', 'category', 'created_at', 'updated_at']);

        return DataTables::of($commodities)
            ->addIndexColumn()
            ->editColumn('created_at', fn($row) => $row->created_at->format('d-m-Y H:i'))
            ->editColumn('updated_at', fn($row) => $row->updated_at->format('d-m-Y H:i'))
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
            ->rawColumns(['action'])
            ->make(true);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:commodities,name',
            'unit' => 'required|string|max:20',
            'category' => 'required|string|max:50',
        ]);

        Commodity::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Komoditas berhasil ditambahkan',
        ]);
    }

    public function edit($id)
    {
        $commodity = Commodity::findOrFail($id);

        return response()->json($commodity);
    }

    public function update(Request $request, $id)
    {
        $commodity = Commodity::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:commodities,name,'.$id,
            'unit' => 'required|string|max:20',
            'category' => 'required|string|max:50',
        ]);

        $commodity->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Komoditas berhasil diperbarui',
        ]);
    }

    public function destroy($id)
    {
        try {
            $commodity = Commodity::findOrFail($id);
            $commodity->delete();

            return response()->json([
                'success' => true,
                'message' => 'Komoditas berhasil dihapus',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus komoditas: '.$e->getMessage(),
            ], 500);
        }
    }
}
