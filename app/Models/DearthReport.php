<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DearthReport extends Model
{
    protected $fillable = [
        'commodity_id',
        'lat',
        'lng',
        'kabupaten',
        'kecamatan',
        'severity',
        'description',
        'province_id',
        'regency_id',
        'district_id',
        'village_id',
        'source',
        'reported_at',
        'status'
    ];

    protected $casts = [
        'reported_at' => 'datetime',
        'lat' => 'decimal:7',
        'lng' => 'decimal:7'
    ];

    public function commodity()
    {
        return $this->belongsTo(Commodity::class);
    }

    public function province()
    {
        return $this->belongsTo(Province::class);
    }

    public function regency()
    {
        return $this->belongsTo(Regency::class);
    }

    public function district()
    {
        return $this->belongsTo(District::class);
    }

    public function village()
    {
        return $this->belongsTo(Village::class);
    }
}