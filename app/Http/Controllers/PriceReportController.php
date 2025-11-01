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
            'source' => 'nullable|in:USER,ENUMERATOR,OFFICIAL',
        ]);

        // Ambil unit dari commodity
        $commodity = Commodity::findOrFail($validated['commodity_id']);
        
        $priceReport = PriceReport::create([
            'commodity_id' => $validated['commodity_id'],
            'price' => $validated['price'],
            'lat' => $validated['lat'],
            'lng' => $validated['lng'],
            'quantity_unit' => $commodity->unit,
            'source' => $validated['source'] ?? 'USER',
            'status' => 'APPROVED', // Auto-approve untuk demo hackathon
            'reported_at' => now(),
        ]);

        return response()->json([
            'message' => 'Laporan harga berhasil dikirim',
            'data' => $priceReport->load('commodity'),
        ], 201);
    }
}