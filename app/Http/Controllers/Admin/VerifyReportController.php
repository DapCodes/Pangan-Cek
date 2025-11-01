<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DearthReport;                         // laporan kelangkaan
use App\Models\PriceReport;                          // laporan harga
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class VerifyReportController extends Controller
{
    /**
     * index
     * Menampilkan halaman verifikasi (1 halaman untuk 2 jenis laporan).
     */
    public function index()
    {
        return view('main.reports.verify');
    }

    /**
     * getData
     * Mengirim JSON untuk Yajra DataTables.
     * Wajib param query: ?type=dearth|price
     *
     * Kolom yang DIKIRIM (sesuai permintaan):
     * - PRICE  : No, Komoditas, Kabupaten, Kecamatan, Price(Unit), Sumber, Reported, Status
     * - DEARTH : No, Komoditas, Kabupaten, Kecamatan, Severity,    Sumber, Reported, Status
     */
    public function getData(Request $request)
    {
        $type = $request->get('type', 'dearth');

        if ($type === 'price') {
            // Ambil laporan HARGA, pending ditaruh paling atas, terbaru di atas
            $query = PriceReport::with([
                    'commodity:id,name',           // asumsi relasi ada
                    'regency:id,name',            // jika belum ada relasi, ganti jadi '-'
                    'district:id,name',
                ])
                ->select([
                    'id','commodity_id',
                    'province_id','regency_id','district_id','village_id',
                    'price','quantity_unit',
                    'source','reported_at','status',
                    'created_at','updated_at',
                ])
                ->orderByRaw("CASE WHEN status = 'pending' THEN 0 ELSE 1 END")
                ->orderByRaw('COALESCE(reported_at, created_at) DESC');

            return DataTables::of($query)
                ->addIndexColumn() // menghasilkan kolom "No"
                // === Kolom tampil di tabel ===
                ->addColumn('commodity', fn($r) => $r->commodity->name ?? '-')
                ->addColumn('kabupaten', fn($r) => $r->regency->name   ?? '-')   // fallback '-' jika relasi belum ada
                ->addColumn('kecamatan', fn($r) => $r->district->name  ?? '-')
                ->addColumn('price_unit', function ($r) {
                    $price = number_format((float)$r->price, 2, ',', '.');
                    return $price.' / '.($r->quantity_unit ?: '-');
                })
                ->addColumn('source', fn($r) => $r->source ?? '-')
                ->addColumn('reported', fn($r) => optional($r->reported_at)->format('d-m-Y H:i') ?: '-')
                ->addColumn('status_badge', function ($r) {
                    $map = ['approved' => 'success', 'pending' => 'warning', 'rejected' => 'danger'];
                    return '<span class="badge bg-'.($map[$r->status] ?? 'secondary').' text-uppercase">'.$r->status.'</span>';
                })
                // Hanya kolom HTML yang perlu di-raw
                ->rawColumns(['status_badge'])
                // Batasi hanya field yang diminta DataTables di FE
                ->make(true);
        }

        // === Dearth (kelangkaan) ===
        $query = DearthReport::with(['commodity:id,name'])
            ->select([
                'id','commodity_id',
                'kabupaten','kecamatan',          // sudah berupa string pada tabel kamu
                'severity','source','reported_at',
                'status','created_at','updated_at',
                'province_id','regency_id','district_id','village_id',
                'lat','lng','description'
            ])
            ->orderByRaw("CASE WHEN status = 'pending' THEN 0 ELSE 1 END")
            ->orderByRaw('COALESCE(reported_at, created_at) DESC');

        return DataTables::of($query)
            ->addIndexColumn() // "No"
            // === Kolom tampil di tabel ===
            ->addColumn('commodity', fn($r) => $r->commodity->name ?? '-')
            ->addColumn('kabupaten', fn($r) => $r->kabupaten ?? '-')
            ->addColumn('kecamatan', fn($r) => $r->kecamatan ?? '-')
            ->addColumn('severity',  fn($r) => $r->severity  ?? '-')
            ->addColumn('source',    fn($r) => $r->source    ?? '-')
            ->addColumn('reported',  fn($r) => optional($r->reported_at)->format('d-m-Y H:i') ?: '-')
            ->addColumn('status_badge', function ($r) {
                $map = ['approved' => 'success', 'pending' => 'warning', 'rejected' => 'danger'];
                return '<span class="badge bg-'.($map[$r->status] ?? 'secondary').' text-uppercase">'.$r->status.'</span>';
            })
            ->rawColumns(['status_badge'])
            ->make(true);
    }

    /**
     * show
     * Melihat detail data untuk modal/detail pane.
     * Wajib param query: ?type=dearth|price
     *
     * (Versi harga)   : komoditas, map(lat/lng), provinsi, kabupaten, kecamatan, desa, harga+unit, sumber, reported
     * (Versi kelangkaan): komoditas, map(lat/lng), provinsi, kabupaten, kecamatan, desa, severity,  sumber, reported
     */
    public function show($id, Request $request)
    {
        $type = $request->get('type', 'dearth');

        if ($type === 'price') {
            $r = PriceReport::with([
                    'commodity:id,name',
                    'province:id,name',
                    'regency:id,name',
                    'district:id,name',
                    'village:id,name',
                ])->findOrFail($id);

            return response()->json([
                'id'           => $r->id,
                'type'         => 'price',
                'commodity'    => $r->commodity->name ?? '-',
                'map'          => ['lat' => $r->lat, 'lng' => $r->lng], // nanti dipakai Leaflet
                'province'     => $r->province->name ?? '-',
                'regency'      => $r->regency->name  ?? '-',
                'district'     => $r->district->name ?? '-',
                'village'      => $r->village->name  ?? '-',
                'price'        => $r->price,
                'unit'         => $r->quantity_unit,
                'source'       => $r->source,
                'reported_at'  => optional($r->reported_at)->format('d-m-Y H:i') ?: '-',
                'status'       => $r->status,
            ]);
        }

        $r = DearthReport::with([
                'commodity:id,name',
                'province:id,name',
                'regency:id,name',
                'district:id,name',
                'village:id,name',
            ])->findOrFail($id);

        return response()->json([
            'id'           => $r->id,
            'type'         => 'dearth',
            'commodity'    => $r->commodity->name ?? '-',
            'map'          => ['lat' => $r->lat, 'lng' => $r->lng], // untuk Leaflet
            'province'     => $r->province->name ?? '-',
            'regency'      => $r->regency->name  ?? ($r->kabupaten ?? '-'),  // fallback pakai kolom string
            'district'     => $r->district->name ?? ($r->kecamatan ?? '-'),
            'village'      => $r->village->name  ?? '-',
            'severity'     => $r->severity,
            'source'       => $r->source,
            'reported_at'  => optional($r->reported_at)->format('d-m-Y H:i') ?: '-',
            'status'       => $r->status,
        ]);
    }

    /**
     * updateStatus
     * Ubah status menjadi approved / rejected.
     * Body: {type: dearth|price, decision: approved|rejected, note?}
     */
    public function updateStatus(Request $request, $id)
    {
        $validated = $request->validate([
            'type'     => 'required|in:dearth,price',
            'decision' => 'required|in:approved,rejected',
            'note'     => 'nullable|string|max:500',
        ]);

        $model  = $validated['type'] === 'price' ? PriceReport::class : DearthReport::class;
        $report = $model::findOrFail($id);

        $payload = ['status' => $validated['decision']];
        // Jika ada kolom review_note pada tabel, tinggal buka komentar:
        // $payload['review_note'] = $validated['note'] ?? null;

        $report->update($payload);

        return response()->json([
            'success' => true,
            'message' => 'Status laporan diubah menjadi: '.$validated['decision'],
        ]);
    }

    /**
     * destroy
     * Menghapus laporan berdasarkan ID & tipe.
     * Body/Query: {type: dearth|price}
     */
    public function destroy(Request $request, $id)
    {
        $validated = $request->validate([
            'type' => 'required|in:dearth,price',
        ]);

        $model  = $validated['type'] === 'price' ? PriceReport::class : DearthReport::class;
        $report = $model::findOrFail($id);
        $report->delete();

        return response()->json([
            'success' => true,
            'message' => 'Laporan dihapus.',
        ]);
    }
}
