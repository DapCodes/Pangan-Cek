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
            'status' => 'APPROVED',
            'reported_at' => now(),
        ]);

        return response()->json([
            'message' => 'Laporan kelangkaan berhasil dikirim',
            'data' => $dearthReport->load(['commodity', 'province', 'regency', 'district', 'village']),
        ], 201);
    }

    public function getDearthMap(Request $request)
    {
        $commodityId = $request->input('commodity_id');
        $days = $request->input('days', 7);

        $query = DearthReport::where('reported_at', '>=', now()->subDays($days))
            ->where('status', 'APPROVED');

        if ($commodityId) {
            $query->where('commodity_id', $commodityId);
        }

        $reports = $query->select('kabupaten', 'severity', DB::raw('COUNT(*) as total_reports'))
            ->groupBy('kabupaten', 'severity')
            ->get();

        $mapData = [];
        foreach ($reports->groupBy('kabupaten') as $kabupaten => $items) {
            $severityCount = [
                'CRITICAL' => 0,
                'HIGH' => 0,
                'MEDIUM' => 0,
                'LOW' => 0,
            ];

            foreach ($items as $item) {
                $severityCount[$item->severity] = $item->total_reports;
            }

            $totalReports = $items->sum('total_reports');
            
            if ($severityCount['CRITICAL'] > 0) {
                $status = 'Kritis';
                $color = '#E74C3C';
            } elseif ($severityCount['HIGH'] > 0) {
                $status = 'Rawan';
                $color = '#E67E22';
            } elseif ($severityCount['MEDIUM'] > 0) {
                $status = 'Waspada';
                $color = '#F39C12';
            } else {
                $status = 'Aman';
                $color = '#2ECC71';
            }

            $mapData[] = [
                'kabupaten' => $kabupaten,
                'total_reports' => $totalReports,
                'severity_distribution' => $severityCount,
                'status' => $status,
                'color' => $color,
            ];
        }

        return response()->json([
            'success' => true,
            'data' => $mapData,
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
