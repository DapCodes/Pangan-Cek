<?php

namespace App\Http\Controllers;

use App\Models\DearthReport;
use App\Models\Commodity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class DearthReportController extends Controller
{

    /**
     * Store a new dearth report
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'commodity_id' => 'required|exists:commodities,id',
            'lat' => 'required|numeric|between:-90,90',
            'lng' => 'required|numeric|between:-180,180',
            'kabupaten' => 'required|string|max:100',
            'kecamatan' => 'nullable|string|max:100',
            'severity' => 'required|in:LOW,MEDIUM,HIGH,CRITICAL',
            'description' => 'nullable|string|max:1000',
            'source' => 'nullable|in:USER,ENUMERATOR,OFFICIAL',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        $dearthReport = DearthReport::create([
            'commodity_id' => $request->commodity_id,
            'lat' => $request->lat,
            'lng' => $request->lng,
            'kabupaten' => $request->kabupaten,
            'kecamatan' => $request->kecamatan,
            'severity' => $request->severity,
            'description' => $request->description,
            'source' => $request->source ?? 'USER',
            'reported_at' => now(),
            'status' => 'APPROVED',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Laporan kelangkaan berhasil dikirim',
            'data' => $dearthReport->load('commodity')
        ], 201);
    }

    /**
     * Get dearth reports by kabupaten
     */
    public function getByKabupaten(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'kabupaten' => 'required|string',
            'commodity_id' => 'nullable|exists:commodities,id',
            'days' => 'nullable|integer|min:1|max:90',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $days = $request->input('days', 30);
        $startDate = Carbon::now()->subDays($days);

        $query = DearthReport::approved()
            ->byKabupaten($request->kabupaten)
            ->inDateRange($startDate, Carbon::now())
            ->with('commodity');

        if ($request->has('commodity_id')) {
            $query->where('commodity_id', $request->commodity_id);
        }

        $reports = $query->orderBy('reported_at', 'desc')->get();

        return response()->json([
            'success' => true,
            'data' => $reports
        ]);
    }

    /**
     * Get dearth statistics for all kabupaten in Jawa Barat
     */
    public function getDearthMap(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'commodity_id' => 'nullable|exists:commodities,id',
            'days' => 'nullable|integer|min:1|max:90',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $days = $request->input('days', 7);
        $startDate = Carbon::now()->subDays($days);

        $query = DearthReport::approved()
            ->inDateRange($startDate, Carbon::now());

        if ($request->has('commodity_id')) {
            $query->where('commodity_id', $request->commodity_id);
        }

        // Group by kabupaten dan hitung severity rata-rata
        $dearthStats = $query
            ->selectRaw('
                kabupaten,
                COUNT(*) as total_reports,
                AVG(CASE 
                    WHEN severity = "LOW" THEN 1
                    WHEN severity = "MEDIUM" THEN 2
                    WHEN severity = "HIGH" THEN 3
                    WHEN severity = "CRITICAL" THEN 4
                    ELSE 0
                END) as severity_score,
                MAX(CASE 
                    WHEN severity = "LOW" THEN 1
                    WHEN severity = "MEDIUM" THEN 2
                    WHEN severity = "HIGH" THEN 3
                    WHEN severity = "CRITICAL" THEN 4
                    ELSE 0
                END) as max_severity
            ')
            ->groupBy('kabupaten')
            ->get()
            ->map(function ($item) {
                // Tentukan warna berdasarkan severity score
                $score = $item->severity_score;
                $color = $this->getSeverityColor($score, $item->total_reports);
                
                return [
                    'kabupaten' => $item->kabupaten,
                    'total_reports' => $item->total_reports,
                    'severity_score' => round($score, 2),
                    'max_severity' => $item->max_severity,
                    'color' => $color,
                    'status' => $this->getDearthStatus($score, $item->total_reports)
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $dearthStats
        ]);
    }

    /**
     * Get severity color based on score and report count
     */
    private function getSeverityColor($score, $totalReports)
    {
        // Jika tidak ada laporan atau score rendah -> hijau (aman)
        if ($totalReports == 0 || $score < 1.5) {
            return '#2ECC71'; // Hijau
        } elseif ($score < 2.5) {
            return '#F39C12'; // Kuning-Orange
        } elseif ($score < 3.5) {
            return '#E67E22'; // Orange
        } else {
            return '#E74C3C'; // Merah
        }
    }

    /**
     * Get dearth status label
     */
    private function getDearthStatus($score, $totalReports)
    {
        if ($totalReports == 0 || $score < 1.5) {
            return 'Aman';
        } elseif ($score < 2.5) {
            return 'Waspada';
        } elseif ($score < 3.5) {
            return 'Rawan';
        } else {
            return 'Kritis';
        }
    }

    /**
     * Get recent dearth reports
     */
    public function getRecent(Request $request)
    {
        $limit = $request->input('limit', 10);
        
        $reports = DearthReport::approved()
            ->with('commodity')
            ->orderBy('reported_at', 'desc')
            ->limit($limit)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $reports
        ]);
    }
}