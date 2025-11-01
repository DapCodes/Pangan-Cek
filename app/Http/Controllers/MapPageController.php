<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Commodity;
use App\Models\PriceReport;
use App\Models\DearthReport;
use Carbon\Carbon;

class MapPageController extends Controller
{
    /**
     * Display the main map page with dual reporting system
     */
    public function index()
    {
        // Get all commodities ordered by category and name
        $commodities = Commodity::orderBy('category')
                                ->orderBy('name')
                                ->get();
        
        // Get statistics for today
        $todayPriceReports = PriceReport::whereDate('created_at', today())->count();
        $todayDearthReports = DearthReport::whereDate('created_at', today())->count();
        
        // Get this week statistics
        $weekPriceReports = PriceReport::whereBetween('created_at', [
            Carbon::now()->startOfWeek(),
            Carbon::now()->endOfWeek()
        ])->count();
        
        $weekDearthReports = DearthReport::whereBetween('created_at', [
            Carbon::now()->startOfWeek(),
            Carbon::now()->endOfWeek()
        ])->count();
        
        // Get latest price reports with commodity info
        $latestPriceReports = PriceReport::with('commodity')
            ->where('status', 'APPROVED')
            ->latest('reported_at')
            ->take(10)
            ->get();
        
        // Get latest dearth reports with commodity info
        $latestDearthReports = DearthReport::with('commodity')
            ->where('status', 'APPROVED')
            ->latest('reported_at')
            ->take(10)
            ->get();
        
        // Get dearth statistics by severity for today
        $dearthBySeverity = DearthReport::whereDate('created_at', today())
            ->selectRaw('severity, COUNT(*) as count')
            ->groupBy('severity')
            ->pluck('count', 'severity')
            ->toArray();
        
        // Ensure all severity levels are present
        $severityStats = [
            'LOW' => $dearthBySeverity['LOW'] ?? 0,
            'MEDIUM' => $dearthBySeverity['MEDIUM'] ?? 0,
            'HIGH' => $dearthBySeverity['HIGH'] ?? 0,
            'CRITICAL' => $dearthBySeverity['CRITICAL'] ?? 0,
        ];
        
        return view('map', compact(
            'commodities',
            'todayPriceReports',
            'todayDearthReports',
            'weekPriceReports',
            'weekDearthReports',
            'latestPriceReports',
            'latestDearthReports',
            'severityStats'
        ));
    }
}