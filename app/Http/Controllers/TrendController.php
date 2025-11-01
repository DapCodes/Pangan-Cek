<?php

namespace App\Http\Controllers;

use App\Models\PriceReport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class TrendController extends Controller
{
    public function trend(Request $request)
    {
        $validated = $request->validate([
            'commodity_id' => 'required|exists:commodities,id',
            'lat' => 'required|numeric|min:-90|max:90',
            'lng' => 'required|numeric|min:-180|max:180',
            'radius_km' => 'nullable|numeric|min:1|max:50',
            'days' => 'nullable|integer|min:1|max:90',
        ]);

        $radiusKm = $validated['radius_km'] ?? 5;
        $days = $validated['days'] ?? 30;
        $lat = $validated['lat'];
        $lng = $validated['lng'];
        $commodityId = $validated['commodity_id'];

        // Tanggal mulai
        $startDate = Carbon::now()->subDays($days)->startOfDay();

        // Query dengan Haversine formula
        $results = PriceReport::select(
            DB::raw('DATE(reported_at) as report_date'),
            DB::raw('AVG(price) as avg_price')
        )
            ->where('commodity_id', $commodityId)
            ->where('status', 'APPROVED')
            ->where('reported_at', '>=', $startDate)
            ->whereRaw(
                "(6371 * ACOS(
                    COS(RADIANS(?)) * COS(RADIANS(lat)) *
                    COS(RADIANS(lng) - RADIANS(?)) +
                    SIN(RADIANS(?)) * SIN(RADIANS(lat))
                )) <= ?",
                [$lat, $lng, $lat, $radiusKm]
            )
            ->groupBy('report_date')
            ->orderBy('report_date', 'asc')
            ->get()
            ->keyBy('report_date');

        // Generate semua tanggal dalam range
        $labels = [];
        $values = [];
        
        for ($i = 0; $i < $days; $i++) {
            $date = Carbon::now()->subDays($days - $i - 1)->format('Y-m-d');
            $labels[] = $date;
            
            if (isset($results[$date])) {
                $values[] = round($results[$date]->avg_price, 2);
            } else {
                $values[] = null; // Tidak ada data
            }
        }

        return response()->json([
            'labels' => $labels,
            'values' => $values,
        ]);
    }
}