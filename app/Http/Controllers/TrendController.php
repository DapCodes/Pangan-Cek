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
        $query = DearthReport::where('reported_at', '>=', now()->subDays($days))
            ->where('status', 'APPROVED');

        if ($commodityId) {
            $query->where('commodity_id', $commodityId);
        }

        $trends = $query->select(
                DB::raw('DATE(reported_at) as date'),
                DB::raw('COUNT(*) as total_reports'),
                DB::raw('SUM(CASE WHEN severity = "CRITICAL" THEN 1 ELSE 0 END) as critical_count'),
                DB::raw('SUM(CASE WHEN severity = "HIGH" THEN 1 ELSE 0 END) as high_count'),
                DB::raw('SUM(CASE WHEN severity = "MEDIUM" THEN 1 ELSE 0 END) as medium_count'),
                DB::raw('SUM(CASE WHEN severity = "LOW" THEN 1 ELSE 0 END) as low_count')
            )
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get();

        return response()->json([
            'success' => true,
            'type' => 'dearth',
            'data' => $trends
        ]);
    }
}