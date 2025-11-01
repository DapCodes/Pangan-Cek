<?php

namespace App\Http\Controllers;

use App\Models\Commodity;
use App\Models\PriceReport;
use Illuminate\Http\Request;

class PriceReportController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'commodity_id' => 'required|exists:commodities,id',
            'price' => 'required|numeric|min:0.01',
            'lat' => 'required|numeric|min:-90|max:90',
            'lng' => 'required|numeric|min:-180|max:180',
            'province_id' => 'nullable|exists:provinces,id',
            'regency_id' => 'nullable|exists:regencies,id',
            'district_id' => 'nullable|exists:districts,id',
            'village_id' => 'nullable|exists:villages,id',
            'source' => 'nullable|in:USER,ENUMERATOR,OFFICIAL',
        ]);

        $commodity = Commodity::findOrFail($validated['commodity_id']);

        $priceReport = PriceReport::create([
            'commodity_id' => $validated['commodity_id'],
            'price' => $validated['price'],
            'lat' => $validated['lat'],
            'lng' => $validated['lng'],
            'province_id' => $validated['province_id'] ?? null,
            'regency_id' => $validated['regency_id'] ?? null,
            'district_id' => $validated['district_id'] ?? null,
            'village_id' => $validated['village_id'] ?? null,
            'quantity_unit' => $commodity->unit,
            'source' => $validated['source'] ?? 'USER',
            'status' => 'PENDING',
            'reported_at' => now(),
        ]);

        return response()->json([
            'message' => 'Laporan harga berhasil dikirim',
            'data' => $priceReport->load(['commodity', 'province', 'regency', 'district', 'village']),
        ], 201);
    }

    public function index(Request $request)
    {
        $query = PriceReport::with(['commodity', 'regency']);

        if ($request->has('commodity_id')) {
            $query->where('commodity_id', $request->commodity_id);
        }

        if ($request->has('days')) {
            $query->where('reported_at', '>=', now()->subDays($request->days));
        }

        $reports = $query->orderBy('reported_at', 'desc')->get();

        return response()->json([
            'success' => true,
            'data' => $reports
        ]);
    }
}
