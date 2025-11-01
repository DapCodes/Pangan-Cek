<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Village extends Model
{
    protected $fillable = ['district_id', 'name', 'code', 'lat', 'lng'];

    public function district()
    {
        return $this->belongsTo(District::class);
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
