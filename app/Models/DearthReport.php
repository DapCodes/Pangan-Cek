<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DearthReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'commodity_id',
        'lat',
        'lng',
        'kabupaten',
        'kecamatan',
        'severity',
        'description',
        'source',
        'reported_at',
        'status',
    ];

    protected $casts = [
        'lat' => 'decimal:7',
        'lng' => 'decimal:7',
        'reported_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relasi ke Commodity
     */
    public function commodity()
    {
        return $this->belongsTo(Commodity::class);
    }

    /**
     * Scope untuk filter berdasarkan kabupaten
     */
    public function scopeByKabupaten($query, $kabupaten)
    {
        return $query->where('kabupaten', $kabupaten);
    }

    /**
     * Scope untuk filter berdasarkan severity
     */
    public function scopeBySeverity($query, $severity)
    {
        return $query->where('severity', $severity);
    }

    /**
     * Scope untuk laporan yang approved
     */
    public function scopeApproved($query)
    {
        return $query->where('status', 'APPROVED');
    }

    /**
     * Scope untuk laporan dalam rentang tanggal
     */
    public function scopeInDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('reported_at', [$startDate, $endDate]);
    }

    /**
     * Get severity color untuk mapping
     */
    public function getSeverityColorAttribute()
    {
        return match($this->severity) {
            'LOW' => '#FFD700',      // Yellow
            'MEDIUM' => '#FFA500',   // Orange
            'HIGH' => '#FF6347',     // Red-Orange
            'CRITICAL' => '#DC143C', // Crimson
            default => '#808080'     // Gray
        };
    }

    /**
     * Get severity label bahasa Indonesia
     */
    public function getSeverityLabelAttribute()
    {
        return match($this->severity) {
            'LOW' => 'Sedikit Langka',
            'MEDIUM' => 'Cukup Langka',
            'HIGH' => 'Sangat Langka',
            'CRITICAL' => 'Tidak Tersedia',
            default => 'Tidak Diketahui'
        };
    }
}