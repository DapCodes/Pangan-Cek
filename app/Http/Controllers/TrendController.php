<?php

// TrendController.php
namespace App\Http\Controllers;

use App\Models\PriceReport;
use App\Models\DearthReport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TrendController extends Controller
{
    public function trend(Request $request)
    {
        $commodityId = $request->input('commodity_id');
        $days = $request->input('days', 30);
        $type = $request->input('type', 'price'); // 'price' or 'dearth'

        if ($type === 'price') {
            return $this->getPriceTrend($commodityId, $days);
        } else {
            return $this->getDearthTrend($commodityId, $days);
        }
    }

    private function getPriceTrend($commodityId, $days)
    {
        $query = PriceReport::where('reported_at', '>=', now()->subDays($days))
            ->where('status', 'APPROVED');

        if ($commodityId) {
            $query->where('commodity_id', $commodityId);
        }

        $trends = $query->select(
                DB::raw('DATE(reported_at) as date'),
                DB::raw('AVG(price) as avg_price'),
                DB::raw('MIN(price) as min_price'),
                DB::raw('MAX(price) as max_price'),
                DB::raw('COUNT(*) as total_reports')
            )
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get();

        return response()->json([
            'success' => true,
            'type' => 'price',
            'data' => $trends
        ]);
    }

private function getDearthTrend($commodityId, $days)
{
    // --- Normalisasi & rentang tanggal (INCLUSIVE) ---
    // Misal $days = 7 -> ambil 7 hari terakhir termasuk hari ini
    $days = (int) ($days ?? 7);
    $end   = now()->endOfDay();
    $start = now()->startOfDay()->subDays($days - 1);

    // Query dasar
    $query = DearthReport::whereBetween('reported_at', [$start, $end])
        ->where('status', 'APPROVED');

    if (!empty($commodityId)) {
        $query->where('commodity_id', $commodityId);
    }

    // Ambil agregasi per HARI (format Y-m-d) + hitung masing-masing severity
    $rows = $query->select(
            DB::raw('DATE(reported_at) as date'),
            DB::raw('COUNT(*) as total_reports'),
            DB::raw('SUM(CASE WHEN severity = "CRITICAL" THEN 1 ELSE 0 END) as critical_count'),
            DB::raw('SUM(CASE WHEN severity = "HIGH" THEN 1 ELSE 0 END) as high_count'),
            DB::raw('SUM(CASE WHEN severity = "MEDIUM" THEN 1 ELSE 0 END) as medium_count'),
            DB::raw('SUM(CASE WHEN severity = "LOW" THEN 1 ELSE 0 END) as low_count')
        )
        ->groupBy(DB::raw('DATE(reported_at)'))
        ->orderBy(DB::raw('DATE(reported_at)'), 'asc')
        ->get();

    // Jadikan map [tanggal => data] agar mudah diisi ke rentang hari lengkap
    $byDate = $rows->mapWithKeys(function ($r) {
        $d = (string) $r->date; // 'YYYY-MM-DD'
        return [$d => [
            'total_reports'  => (int) $r->total_reports,
            'critical_count' => (int) $r->critical_count,
            'high_count'     => (int) $r->high_count,
            'medium_count'   => (int) $r->medium_count,
            'low_count'      => (int) $r->low_count,
        ]];
    });

    // Buat label tanggal lengkap (agar chart tetap terisi walau ada hari tanpa data)
    $labels = [];
    $critical = [];
    $high = [];
    $medium = [];
    $low = [];
    $total = [];

    $cursor = $start->copy();
    while ($cursor->lte($end)) {
        $key = $cursor->format('Y-m-d');
        $labels[] = $key;

        $data = $byDate[$key] ?? [
            'total_reports' => 0,
            'critical_count' => 0,
            'high_count' => 0,
            'medium_count' => 0,
            'low_count' => 0,
        ];

        $critical[] = (int) $data['critical_count'];
        $high[]     = (int) $data['high_count'];
        $medium[]   = (int) $data['medium_count'];
        $low[]      = (int) $data['low_count'];
        $total[]    = (int) $data['total_reports'];

        $cursor->addDay();
    }

    // Struktur khusus Line Chart (Chart.js/ApexCharts)
    $chartData = [
        'labels' => $labels,
        'datasets' => [
            [
                'label' => 'Critical',
                'data' => $critical,
                'borderColor' => '#ff0000',
                'fill' => false,
                'tension' => 0.3, // garis agak mulus
            ],
            [
                'label' => 'High',
                'data' => $high,
                'borderColor' => '#ff8000',
                'fill' => false,
                'tension' => 0.3,
            ],
            [
                'label' => 'Medium',
                'data' => $medium,
                'borderColor' => '#ffcc00',
                'fill' => false,
                'tension' => 0.3,
            ],
            [
                'label' => 'Low',
                'data' => $low,
                'borderColor' => '#00cc00',
                'fill' => false,
                'tension' => 0.3,
            ],
            [
                'label' => 'Total Reports',
                'data' => $total,
                'borderColor' => '#3b82f6',
                'fill' => false,
                'borderDash' => [6, 6], // opsional: total sebagai garis putus-putus
                'tension' => 0.3,
            ],
        ],
    ];

    // (Opsional) kirim juga data mentah agar kompatibel dgn kode lama
    $raw = $rows->map(function ($r) {
        return [
            'date' => (string) $r->date,
            'total_reports'  => (int) $r->total_reports,
            'critical_count' => (int) $r->critical_count,
            'high_count'     => (int) $r->high_count,
            'medium_count'   => (int) $r->medium_count,
            'low_count'      => (int) $r->low_count,
        ];
    })->values();

    return response()->json([
        'success' => true,
        'type' => 'dearth',
        'range' => [
            'start' => $start->toDateTimeString(),
            'end'   => $end->toDateTimeString(),
            'days'  => $days,
        ],
        'chart' => $chartData,
        'raw'   => $raw, // supaya frontend lama yang masih butuh array objek tetap bisa pakai
    ]);
}

}
