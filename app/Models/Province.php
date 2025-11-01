<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Province extends Model
{
    protected $fillable = ['name', 'code'];

    public function regencies()
    {
        return $this->hasMany(Regency::class);
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
