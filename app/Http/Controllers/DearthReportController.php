<?php
namespace App\Http\Controllers;

use App\Models\DearthReport;
use App\Models\Commodity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DearthReportController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'commodity_id' => 'required|exists:commodities,id',
            'lat' => 'required|numeric|min:-90|max:90',
            'lng' => 'required|numeric|min:-180|max:180',
            'kabupaten' => 'required|string|max:100',
            'kecamatan' => 'nullable|string|max:100',
            'severity' => 'required|in:LOW,MEDIUM,HIGH,CRITICAL',
            'description' => 'nullable|string',
            'province_id' => 'nullable|exists:provinces,id',
            'regency_id' => 'nullable|exists:regencies,id',
            'district_id' => 'nullable|exists:districts,id',
            'village_id' => 'nullable|exists:villages,id',
            'source' => 'nullable|in:USER,ENUMERATOR,OFFICIAL',
        ]);

        $dearthReport = DearthReport::create([
            'commodity_id' => $validated['commodity_id'],
            'lat' => $validated['lat'],
            'lng' => $validated['lng'],
            'kabupaten' => $validated['kabupaten'],
            'kecamatan' => $validated['kecamatan'] ?? null,
            'severity' => $validated['severity'],
            'description' => $validated['description'] ?? null,
            'province_id' => $validated['province_id'] ?? null,
            'regency_id' => $validated['regency_id'] ?? null,
            'district_id' => $validated['district_id'] ?? null,
            'village_id' => $validated['village_id'] ?? null,
            'source' => $validated['source'] ?? 'USER',
            'status' => 'PENDING',
            'reported_at' => now(),
        ]);

        return response()->json([
            'message' => 'Laporan kelangkaan berhasil dikirim',
            'data' => $dearthReport->load(['commodity', 'province', 'regency', 'district', 'village']),
        ], 201);
    }

// app/Http/Controllers/Api/DearthController.php
public function getDearthMap(Request $request)
{
    $commodityId = $request->input('commodity_id');
    $days = (int) $request->input('days', 30);

    $query = DearthReport::where('reported_at', '>=', now()->subDays($days))
        ->where('status', 'APPROVED');

    if ($commodityId) {
        $query->where('commodity_id', $commodityId);
    }

    // ✅ KUNCI UTAMA: agregasi per regency_id + severity
    $reports = $query->select(
            'regency_id',      // ← dipakai untuk join ke layer peta
            'kabupaten',       // ← tetap kirim untuk tampilan tooltip
            'severity',
            DB::raw('COUNT(*) as total_reports')
        )
        ->groupBy('regency_id', 'kabupaten', 'severity')
        ->get();

    $grouped = $reports->groupBy('regency_id');

    $mapData = [];
    foreach ($grouped as $regencyId => $items) {
        $severityCount = [
            'CRITICAL' => 0,
            'HIGH'     => 0,
            'MEDIUM'   => 0,
            'LOW'      => 0,
        ];

        $kabupatenName = null;
        foreach ($items as $item) {
            $kabupatenName = $kabupatenName ?: $item->kabupaten;
            $severityCount[$item->severity] = (int) $item->total_reports;
        }

        $totalReports = array_sum($severityCount);

        // LOW=0, MEDIUM=1, HIGH=2, CRITICAL=3 → rata-rata severity (0..3)
        $scoreMap  = ['LOW'=>0,'MEDIUM'=>1,'HIGH'=>2,'CRITICAL'=>3];
        $numerator = 0;
        foreach ($severityCount as $sev => $cnt) {
            $numerator += ($scoreMap[$sev] * $cnt);
        }
        $averageSeverity = $totalReports > 0 ? $numerator / $totalReports : 0.0;

        $mapData[] = [
            'regency_id'           => (string) $regencyId,     // ← kirim sebagai string
            'kabupaten'            => $kabupatenName,
            'total_reports'        => $totalReports,
            'severity_distribution'=> $severityCount,
            'average_severity'     => round($averageSeverity, 4),
        ];
    }

    return response()->json([
        'success' => true,
        'data'    => $mapData,
    ]);
}


    public function getRecent(Request $request)
    {
        $limit = $request->input('limit', 10);

        $reports = DearthReport::with(['commodity', 'regency'])
            ->where('status', 'APPROVED')
            ->orderBy('reported_at', 'desc')
            ->limit($limit)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $reports
        ]);
    }
}
