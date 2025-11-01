<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DearthReport;                          // <- laporan kelangkaan
use App\Models\PriceReport;                           // <- laporan harga
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class VerifyReportController extends Controller
{
    /**
     * Halaman verifikasi dengan filter jenis laporan (dearth/price).
     */
    public function index()
    {
        return view('main.reports.verify');           // 1 halaman untuk dua jenis laporan
    }

    /**
     * DataTables server-side.
     * Param wajib: ?type=dearth|price
     */
 public function getData(Request $request)
    {
        $type = $request->get('type', 'dearth');

        if ($type === 'price') {
            // PRICE REPORT: eager load komoditas, prioritas pending, terbaru di atas
            $query = PriceReport::with(['commodity:id,name'])
                ->select([
                    'id','commodity_id','price','quantity_unit',
                    'province_id','regency_id','district_id','village_id',
                    'lat','lng','source','reported_at','status','created_at','updated_at'
                ])
                ->orderByRaw("CASE WHEN status = 'pending' THEN 0 ELSE 1 END") // pending paling atas
                ->orderByRaw('COALESCE(reported_at, created_at) DESC');       // yang terbaru di atas

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('commodity', fn($r) => $r->commodity->name ?? '-') // nama komoditas dari relasi
                ->addColumn('price_pretty', fn($r) => number_format((float)$r->price, 2, ',', '.') . ' / ' . ($r->quantity_unit ?? '-'))
                ->editColumn('reported_at', fn($r) => optional($r->reported_at)->format('d-m-Y H:i'))
                ->editColumn('created_at',  fn($r) => optional($r->created_at)->format('d-m-Y H:i'))
                ->editColumn('updated_at',  fn($r) => optional($r->updated_at)->format('d-m-Y H:i'))
                ->editColumn('status', function ($r) {
                    $map = ['approved' => 'success', 'pending' => 'warning', 'rejected' => 'danger'];
                    return '<span class="badge bg-'.($map[$r->status] ?? 'secondary').' text-uppercase">'.$r->status.'</span>';
                })
                ->addColumn('action', function ($r) {
                    // TAMPILKAN AKSI HANYA SAAT PENDING
                    if ($r->status !== 'pending') return '';
                    return '
                        <button type="button" class="btn btn-sm btn-success approve-btn me-1" data-id="'.$r->id.'" data-type="price" title="Verifikasi (Setuju)">
                            <i class="bx bx-check"></i>
                        </button>
                        <button type="button" class="btn btn-sm btn-danger review-btn" data-id="'.$r->id.'" data-type="price" title="Tinjau / Tolak">
                            <i class="bx bx-x"></i>
                        </button>
                    ';
                })
                ->rawColumns(['status','action'])
                ->make(true);
        }

        // DEARTH REPORT (kelangkaan)
        $query = DearthReport::with(['commodity:id,name'])
            ->select([
                'id','commodity_id','kabupaten','kecamatan','severity','description',
                'province_id','regency_id','district_id','village_id',
                'lat','lng','source','reported_at','status','created_at','updated_at'
            ])
            ->orderByRaw("CASE WHEN status = 'pending' THEN 0 ELSE 1 END")
            ->orderByRaw('COALESCE(reported_at, created_at) DESC');

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('commodity', fn($r) => $r->commodity->name ?? '-')
            ->editColumn('reported_at', fn($r) => optional($r->reported_at)->format('d-m-Y H:i'))
            ->editColumn('created_at',  fn($r) => optional($r->created_at)->format('d-m-Y H:i'))
            ->editColumn('updated_at',  fn($r) => optional($r->updated_at)->format('d-m-Y H:i'))
            ->editColumn('status', function ($r) {
                $map = ['approved' => 'success', 'pending' => 'warning', 'rejected' => 'danger'];
                return '<span class="badge bg-'.($map[$r->status] ?? 'secondary').' text-uppercase">'.$r->status.'</span>';
            })
            ->addColumn('action', function ($r) {
                if ($r->status !== 'pending') return '';
                return '
                    <button type="button" class="btn btn-sm btn-success approve-btn me-1" data-id="'.$r->id.'" data-type="dearth" title="Verifikasi (Setuju)">
                        <i class="bx bx-check"></i>
                    </button>
                    <button type="button" class="btn btn-sm btn-danger review-btn" data-id="'.$r->id.'" data-type="dearth" title="Tinjau / Tolak">
                        <i class="bx bx-x"></i>
                    </button>
                ';
            })
            ->rawColumns(['status','action'])
            ->make(true);
    }

    /**
     * Aksi cepat (✔): set status approved.
     * Body: {id, type}
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'id'   => 'required|integer',
            'type' => 'required|in:dearth,price',
        ]);

        $model = $validated['type'] === 'price' ? PriceReport::class : DearthReport::class;
        $report = $model::findOrFail($validated['id']);
        $report->update(['status' => 'approved']);

        return response()->json([
            'success' => true,
            'message' => 'Laporan disetujui.',
        ]);
    }

    /**
     * Ambil detail untuk modal review (✕).
     * Param wajib: ?type=dearth|price
     */
    public function edit($id, Request $request)
    {
        $type = $request->get('type','dearth');
        $model = $type === 'price' ? PriceReport::class : DearthReport::class;

        $report = $model::findOrFail($id);
        return response()->json($report);
    }

    /**
     * Keputusan dari modal: approved / rejected.
     * Body: {decision: approved|rejected, note?, type}
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'type'     => 'required|in:dearth,price',
            'decision' => 'required|in:approved,rejected',
            'note'     => 'nullable|string|max:500',
        ]);

        $model = $validated['type'] === 'price' ? PriceReport::class : DearthReport::class;
        $report = $model::findOrFail($id);

        $payload = ['status' => $validated['decision']];
        // Kalau kamu sediakan kolom review_note di kedua tabel, aktifkan baris di bawah:
        // $payload['review_note'] = $validated['note'] ?? null;

        $report->update($payload);

        return response()->json([
            'success' => true,
            'message' => 'Status laporan diperbarui ke: '.$validated['decision'],
        ]);
    }

    /**
     * Opsional: hapus laporan (tidak wajib).
     */
    public function destroy(Request $request, $id)
    {
        $validated = $request->validate([
            'type' => 'required|in:dearth,price',
        ]);
        $model = $validated['type'] === 'price' ? PriceReport::class : DearthReport::class;

        $report = $model::findOrFail($id);
        $report->delete();

        return response()->json([
            'success' => true,
            'message' => 'Laporan dihapus.',
        ]);
    }
}
