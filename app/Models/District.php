<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class District extends Model
{
    use HasFactory;

    protected $fillable = [
        'regency_id',
        'name',
        'code',
        'lat',
        'lng',
    ];

    protected $casts = [
        'lat' => 'double',
        'lng' => 'double',
    ];

    /**
     * Relasi ke Regency
     */
    public function regency()
    {
        return $this->belongsTo(Regency::class);
    }

    /**
     * Relasi ke Villages
     */
    public function villages()
    {
        return $this->hasMany(Village::class);
    }

    /**
     * Relasi ke Reports
     */
    public function reports()
    {
        return $this->hasMany(Report::class);
    }
}
