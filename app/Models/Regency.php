<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Regency extends Model
{
    protected $fillable = ['province_id', 'name', 'type', 'code', 'lat', 'lng'];

    public function province()
    {
        return $this->belongsTo(Province::class);
    }

    public function districts()
    {
        return $this->hasMany(District::class);
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
