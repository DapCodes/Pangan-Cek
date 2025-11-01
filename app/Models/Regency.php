<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Regency extends Model
{
    use HasFactory;

    protected $fillable = [
        'province_id',
        'name',
        'type',
        'code',
        'lat',
        'lng',
    ];

    protected $casts = [
        'lat' => 'double',
        'lng' => 'double',
    ];

    /**
     * Relasi ke Province
     */
    public function province()
    {
        return $this->belongsTo(Province::class);
    }

    /**
     * Relasi ke Districts
     */
    public function districts()
    {
        return $this->hasMany(District::class);
    }
}
