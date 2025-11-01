<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

// District.php
class District extends Model
{
    protected $fillable = ['regency_id', 'name', 'code', 'lat', 'lng'];

    public function regency()
    {
        return $this->belongsTo(Regency::class);
    }

    public function villages()
    {
        return $this->hasMany(Village::class);
    }

    public function priceReports()
    {
        return $this->hasMany(PriceReport::class);
    }

    public function dearthReports()
    {
        return $this->hasMany(DearthReport::class);
    }
}

